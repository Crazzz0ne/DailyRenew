<?php

namespace App\Console\Commands;

use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Outside\ReHash;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MakeStale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:makeStales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'makes a lead stale';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {


        $leads = Lead::where('id', '>', 1)->where(function ($q) {
            $q->where('origin_office_id', 10);
            $q->orWhere('origin_office_id', 33);
            $q->orWhere('origin_office_id', 34);
            $q->orWhere('origin_office_id', 47);
            $q->orWhere('origin_office_id', 2);
        })->with('appointments', 'leadNote', 'customer')
            ->get();

        $csvArray = [];
        $twoWeeksAgo = Carbon::now()->subWeeks(3);

        foreach ($leads as $lead) {

            $triggered = false;
            if ($lead->status_id === 24 || $lead->status_id === 13 || $lead->status_id === 12 || $lead->status_id === 11 ||
                $lead->status_id === 10 || $lead->status_id === 9 || $lead->status_id === 8) {
                continue;
            }
            foreach ($lead->appointments as $appointment) {
                if ($twoWeeksAgo->greaterThan($appointment->start_time) && !$triggered) {
                    $triggered = true;
                    $lead->stale = Carbon::now();
                    $lead->save();
                    $josh = $lead->customer;
                    if ($lead->statusName) {
                        $josh->reason = $lead->statusName->name;
                        array_push($csvArray, $josh->toarray());

                    }


                }
            }
            foreach ($lead->leadNote as $note) {
                if ($twoWeeksAgo->greaterThan($note->created_at) && !$triggered) {
                    $triggered = true;
                    $lead->stale = Carbon::now();
                    $lead->save();
                    if ($lead->statusName) {
                        $josh = $lead->customer;
                        $josh->reason = $lead->statusName->name;
                        array_push($csvArray, $josh->toarray());
                        $reHash = new ReHash();
                        $reHash->lead_id = $lead->id;
                        $reHash->name = 'attaway';
                        $reHash->save();
                    }
                }
            }
            if (!$triggered && $twoWeeksAgo->greaterThan($lead->updated_at)) {
                $josh = $lead->customer;
                $josh->reason = $lead->statusName->name;
                $lead->stale = Carbon::now();
                $lead->save();
                array_push($csvArray, $josh->toarray());
                $reHash = new ReHash();
                $reHash->lead_id = $lead->id;
                $reHash->name = 'attaway';
                $reHash->save();
            }
        }
        $reHashCSV = fopen('test', 'a');
        $header = ['id', 'first_name', 'last_name', 'cell_phone', 'home_phone', 'email', 'street_address',
            'city', 'zip_code', 'language', 'reason'];
        fputcsv($reHashCSV, $header);
        foreach ($csvArray as $array) {
            if (substr($array['cell_phone'], 0,1) === '+'){
                $cell =   trim($array['cell_phone'], '+1');
            }else{
                $cell = $array['cell_phone'];
            }
            fputcsv($reHashCSV, [$array['id'], $array['first_name'], $array['last_name'], $cell,
                $array['home_phone'], $array['street_address'], $array['city'], $array['zip_code'], $array['language'], $array['reason']]);
        }
        fclose($reHashCSV);
    }

}
