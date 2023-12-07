<?php

namespace App\Console\Commands;

use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OldCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fixJij';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes JIJ error';

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
        $goodStatusArray = [1, 2, 3, 5, 14, 17, 20];
//        return Carbon::now()->subMonths(5);
//        $leads = Lead::where('origin_office_id', 10)
        $leads = Lead::where('origin_office_id', 10)
            ->whereIn('status_id', $goodStatusArray)
            ->where('credit_status_id', 2)
            ->where('created_at', '<', Carbon::now()->subMonths(2)
                ->format('Y-m-d'))
            ->with('customer')
            ->get();

        $columns = array('Lead Id', 'First Name', 'Last Name', 'Phone', 'Email');
        $columns = array('Lead Id', 'First Name', 'Last Name', 'Phone', 'Email', 'link');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');

        $file = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
        fputcsv($file, $columns);
        foreach ($leads as $key => $leadl) {

            foreach ($leads as $lead) {
//                $opener = null;
//                $sp1 = null;
//                $closer = null;
//                $close = null;

//                foreach ($lead->reps as $rep) {
//                    if ($rep->pivot->position_id == 1) {
//                        $opener = $rep->full_name;
//                    }
//                    if ($rep->pivot->position_id == 2) {
//                        $sp1 = $rep->full_name;
//                    }
//                    if ($rep->pivot->position_id == 3) {
//                        $closer = $rep->full_name;
//                    }
//                }
//                    TODO: data is not changing in the lead controller.
//                if ($lead->status = 'closed') {
//                    $close = $lead->close_date;
//                }

                fputcsv($file, array($lead->id, $lead->customer->first_name, $lead->customer->last_name,
                    $lead->customer->cell_phone, $lead->customer->email));
//                $lead->customer->cell_phone, $lead->customer->email, 'https://scout.solar/dashboard/lead/' . $lead->id));
            }
        }
        rewind($file);
        $output = stream_get_contents($file);

//        readfile('download/' . $output);
        return $output;
	}

}

