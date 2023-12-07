<?php


namespace App\Http\Controllers\Api\SalesFlow\Lead;


use App\Events\Backend\SalesFlow\Lead\LeadClosedEvent;
use App\Events\Backend\SalesFlow\Lead\SoldSystemEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Http\Resources\Lead\SystemPPWResource;
use App\Http\Resources\Lead\SystemResource;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\System\System;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SystemController
{
    public function index(Lead $lead, System $system, Request $request)
    {
        $user = \Auth::user();

        if ($user->hasPermissionTo('view ppw')) {
            return new SystemPPWResource(System::where('id', '=', $lead->system_id)->first());
        } else {
            return new SystemResource(System::where('id', '=', $lead->system_id)->first());
        }

    }

    public function update(Request $request, Lead $lead, System $system)
    {
        $payload = $request->except(['userNote']);
        $system->update($payload);
//        gets only the changes
        $something = $system->getChanges();
        if (count($something) > 0) {
//        I need the ID for vue to match on the page
            $something['id'] = $system->id;
            $data = collect($something);
//        lets everyone else know of the changes
//            event(new SoldSystemEvent($system, $data, $lead->id));

        }
        return $something;
    }

    public function closeIt(Request $request, Lead $lead)
    {
//        create lead system
        $system = new System();
        $system->contract_amount = 1;
        $system->inverter_id = 1;
        $system->modules_id = 1;
        $system->modules_count = 1;
        $system->monthly_payment = 1;
        $system->save();
//        update lead
        $lead->close_date = Carbon::now();
        $lead->system_id = $system->id;

        $lead->status_id = 11;
        $lead->save();
        $lead->salesPacket()->update(
            [
                'solar_agreement_signed' => Carbon::now(),
                'cpuc_doc_signed' => Carbon::now()
            ]
        );
        event(new UpdateZapierEvent($lead, 'sit'));

        event(new LeadClosedEvent($lead->id, $lead));

        $line = new Line();
        $line->requested_user_id = Auth::user()->id;
        $line->lead_id = $lead->id;
        $line->type = 'NTS';
        $line->save();


        $subject = 'NTS (' . $line->lead_id . ')';
        $body = 'Can You Submit Paperwork?';
        $link = URL::to('/') . '/dashboard/lead/queue';
        $ntsPeoples = User::permission('edit NTS')->where('terminated', null)->get();
//            if env is production, send to all NTS
            foreach ($ntsPeoples as $nts) {
                if ($lead->source === 'call center' || $lead->source === 'call center alpha') {
                    Mail::to($nts->email)->queue(new BaseMailable($subject, $body, $link, 'NTS'));
                } elseif ($nts->id === 12) {
                    Mail::to($nts->email)->queue(new BaseMailable($subject, $body, $link, 'NTS'));
                }
            }

        if (app()->environment('production')) {
            $size = $lead->system->system_size;
            $ppw = $lead->system->ppw;
            $body = $lead->customer->first_name . ' ' . $lead->customer->last_name . ' CLOSED by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
            event(new TextEvent('8564306685', $body));
            event(new TextEvent('6199406423', $body));
            event(new TextEvent('9106907839', $body));
            event(new TextEvent('7654093132', $body));
        }
        return new SystemResource($system);

    }


}
