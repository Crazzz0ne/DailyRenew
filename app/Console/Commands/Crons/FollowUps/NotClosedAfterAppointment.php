<?php


namespace App\Console\Commands\Crons\FollowUps;


use App\Mail\SalesFlow\BaseMailable;
use App\Mail\SalesFlow\DIdNotCloseMailable;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotClosedAfterAppointment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:emailNotClosedOne';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email Garry requested to send if the deal does not close';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notClosedArray = [2, 3, 4, 5, 14, 17, 18, 20, 21];
        $leads = Lead::whereHas('appointments', function ($q) {
            $q->whereDate('start_time', '2021-08-07');
            $q->where('type_id', 6);
        })
            ->where('origin_office_id', 10)
            ->whereIn('status_id', $notClosedArray)
            ->get();

        foreach ($leads as $lead) {

          Mail::to($lead->customer->email)->bcc('gary@solcalenergy.com')->queue(new DIdNotCloseMailable('Net Metering Program - Cuts Your Electric Bill 20%-50%', $lead->customer, $lead->utility->powerCompany->name));

        }

//        Mail::to()


    }
}
