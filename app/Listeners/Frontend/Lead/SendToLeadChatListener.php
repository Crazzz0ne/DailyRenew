<?php

namespace App\Listeners\Frontend\Lead;

use App\Events\Backend\SalesFlow\Lead\Note\NewUserLeadChatEvent;
use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use App\Events\Backend\SalesFlow\Queue\NewQueueEvent;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Notifications\Backend\SalesFlow\Queue\NewQueueNotification;
use App\Repositories\Backend\Auth\UserRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;


class SendToLeadChatListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    protected $userRepository;

    public function __construct()
    {
        // set the model
        $this->userRepository = new UserRepository();
    }

    /**
     * Handle the event.
     *
     * @param LeadNewMessageEvent $event
     * @return void
     */
    public function handle(LeadNewMessageEvent $event)
    {
        $lead = $event->lead;
//        $userOnLead = UserHasLead::where('lead_id', $event->lead->Id)->get();

        $allUsers = Cache::remember('all-users-with-roles', 600, function () {
            return  User::with('roles')->where('terminated', null)->get();

        });
        $user = \Auth::user();
        if ($user){
            $allUsers->reject(function($element) use ($user){
                return $element->id === $user->id;
            });
        }


        $allSeeing = Cache::remember('all-seeing', 600, function () use ($allUsers) {
            $payload = $allUsers->filter(function ($user, $key) {
                return $user->hasRole('account manager') || $user->hasRole('proposal builder') || $user->hasRole('executive') || $user->hasRole('administrator');
            });
            return $payload->all();
        });

        $regional = Cache::remember('regional.office.' . $lead->office_id, 600, function () use ($lead, $allUsers) {
            $regionalOfficeID = $lead->office_id;
            $marketId = Office::where('id', $regionalOfficeID)->get()->pluck('market_id');
            $offices = Office::where('market_id', $marketId)->get()->pluck('id')->toarray();
            $payload = $allUsers->filter(function ($user, $key) use ($offices, $lead) {
                if (in_array($user->office_id, $offices) && $user->hasRole('regional manager')) {
                    return true;
                }
                return false;
            });
            return $payload->all();
        });
        $mangers = Cache::remember('manager.office.' . $lead->office_id, 600, function () use ($lead, $allUsers) {


            $payload = $allUsers->filter(function ($user, $key) use ($lead) {
                if (($lead->office_id === $user->office_id || $lead->orgin_office_id === $user->office_id)
                    && $user->hasRole('manager')) {
                    return true;
                }
                return false;
            });
            return $payload->all();
        });


        $onLead = Cache::remember('reps.lead.' . $lead->id, 600, function () use ($lead, $allUsers) {
            return $lead->reps;
        });
        $noSeeing = collect($allSeeing);
        $noSeeing->merge($regional);
        $noSeeing->merge($mangers);
        $noSeeing->merge($onLead);
        $users = $noSeeing->unique()->all();
        $note = LeadNote::where('id', $event->id)->first();
        foreach ($users as $user) {


            event(new NewUserLeadChatEvent($note, $user));
        }

    }
}
