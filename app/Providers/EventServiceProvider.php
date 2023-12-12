<?php

namespace App\Providers;

use App\Events\Backend\CallCenter\CallCenterEvent;
use App\Events\Backend\SalesFlow\AppointmentBookedEvent;
use App\Events\Backend\SalesFlow\Customer\CustomerAppointmentEvent;
use App\Events\Backend\SalesFlow\Lead\Closed\CompleteClosedEvent;
use App\Events\Backend\SalesFlow\Lead\CustomerSatEvent;
use App\Events\Backend\SalesFlow\Lead\JeopardyEvent;
use App\Events\Backend\SalesFlow\Lead\LeadClosedEvent;
use App\Events\Backend\SalesFlow\Lead\LeadUpdateTwoEvent;
use App\Events\Backend\SalesFlow\Lead\SalesRabbit\CreateSalesRabbitLeadEvent;
use App\Events\Backend\SalesFlow\Lead\SendFileToEPCEvent;
use App\Events\Backend\SalesFlow\LeadFileUploadEvent;
use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use App\Events\Backend\SalesFlow\MailChimpDripAddEvent;
use App\Events\Backend\SalesFlow\Queue\NewQueueEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Events\Backend\SalesFlow\Users\Notifications\NewUserNotificationEvent;
use App\Events\Backend\Slack\SlackWebhookNotificationEvent;
use App\Events\Frontend\Auth\UserRegistered;
use App\Listeners\Backend\Auth\Role\RoleEventListener;
use App\Listeners\Backend\Auth\User\SendUserInvitationNotification;
use App\Listeners\Backend\Auth\User\UserEventListener;
use App\Listeners\Backend\CallCenter\CallCenterListener;
use App\Listeners\Backend\SalesFlow\Commissions\AppointmentCommissionListener;
use App\Listeners\Backend\SalesFlow\Commissions\CloseCommissionListener;
use App\Listeners\Backend\SalesFlow\Commissions\RemoveOpenersPayListener;
use App\Listeners\Backend\SalesFlow\Commissions\SitCommissionListener;
use App\Listeners\Backend\SalesFlow\CreateCompleteLeadListener;
use App\Listeners\Backend\SalesFlow\Customer\CustomerAppointmentListener;
use App\Listeners\Backend\SalesFlow\Lead\Appointment\UnstaleAppointmentListener;
use App\Listeners\Backend\SalesFlow\Lead\Note\CustomerSatNoteListener;
use App\Listeners\Backend\SalesFlow\Lead\Note\UnStaleNoteListener;
use App\Listeners\Backend\SalesFlow\Lead\SalesRabbit\CreateSalesRabbitLeadListener;
use App\Listeners\Backend\SalesFlow\Lead\SendGHLWebhookListener;
use App\Listeners\Backend\SalesFlow\MailChimpDripAddListener;
use App\Listeners\Backend\SalesFlow\NewUserNotificationListener;
use App\Listeners\Backend\SalesFlow\NewQueueListener;
use App\Listeners\Backend\SalesFlow\SendFileToEPCListener;
use App\Listeners\Backend\SalesFlow\SendTextListener;
use App\Listeners\Backend\SalesFlow\UpdateZapierListener;
use App\Listeners\Backend\SalesFlow\UploadedNotificationListener;
use App\Listeners\Backend\Slack\SlackWebhookNotificationListener;
use App\Listeners\Frontend\Lead\NewLeadNoteListener;
use App\Listeners\Frontend\Lead\SendToLeadChatListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Junaidnasir\Larainvite\Events\Invited;

/**
 * Class EventServiceProvider.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//        TextEvent::class => [
//            SendTextListener::class,
//        ],

        Invited::class => [
            SendUserInvitationNotification::class,
        ],
        LeadFileUploadEvent::class => [
            UploadedNotificationListener::class
        ],
        NewQueueEvent::class => [
            NewQueueListener::class,
        ],
        NewUserNotificationEvent::class => [
            NewUserNotificationListener::class
        ],
        LeadNewMessageEvent::class => [
            NewLeadNoteListener::class,
            UnStaleNoteListener::class,
            SendToLeadChatListener::class
        ],
//        CompleteClosedEvent::class => [
//            CreateCompleteLeadListener::class,
//        ],
//        SendFileToEPCEvent::class => [
//            SendFileToEPCListener::class
//        ],
        JeopardyEvent::class => [
            SendGHLWebhookListener::class,
        ],
//        CreateSalesRabbitLeadEvent::class => [
//            CreateSalesRabbitLeadListener::class
//        ],
        CustomerAppointmentEvent::class => [
            CustomerAppointmentListener::class
        ],
        UserRegistered::class => [

        ],
        CallCenterEvent::class => [
            CallCenterListener::class
        ],
        CustomerSatEvent::class => [
            SitCommissionListener::class,
            CustomerSatNoteListener::class,
        ],
        AppointmentBookedEvent::class => [
//            AppointmentCommissionListener::class,
            UnstaleAppointmentListener::class
        ],
        LeadUpdateTwoEvent::class => [
            RemoveOpenersPayListener::class
        ],
        SlackWebhookNotificationEvent::class => [
            SlackWebhookNotificationListener::class
        ],
//        UpdateZapierEvent::class => [
//            UpdateZapierListener::class
//        ],

    ];

    /**
     * Class event subscribers.
     *
     * @var array
     */
    protected $subscribe = [
        // Frontend Subscribers

        // Auth Subscribers
        \App\Listeners\Frontend\Auth\UserEventListener::class,

        // Backend Subscribers

        // Auth Subscribers
        UserEventListener::class,
        RoleEventListener::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();

        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
