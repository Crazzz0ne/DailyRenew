<?php


namespace App\Events\Backend\SalesFlow\Lead;


use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use Auth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Slack;

class LeadClosedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $lead;
    public $leadId;

    public function __construct($leadId, Lead $lead)
    {
        $this->leadId = $leadId;
        $this->lead = $lead;

        $userName = Auth::user()->name;


//        $customer = Customer::where('id', $lead->customer->id)->first();

        $options = [
            'color' => 'good',
            'fields' => [
                [
                    'title' => 'Congratulations!',
                    'value' => "$userName has just closed {$lead->customer->name}!",
                    'short' => false,
                ]
            ],
        ];
        Slack::compose("", config('slack.channels.closers_solvida'), $options);

        $options = [
            'color' => 'good',
            'fields' => [
                [
                    'title' => 'Submit Docs (NTS)',
                    'value' => "$userName closed a deal and needs to have docs submitted {$lead->customer->name}!",
                    'short' => false,
                ]
            ],
        ];

        Slack::compose(Slack::link(config('app.url') . '/dashboard/lead/queue', 'View Queue'), config('slack.channels.core'), $options);
    }
}
