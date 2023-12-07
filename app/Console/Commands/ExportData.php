<?php

namespace App\Console\Commands;

use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:exportData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saves leads to CSV';

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
        $fileName = 'leads.csv';
        $handle = fopen($fileName, 'w');

        // Write header row
        fputcsv($handle, [
            'name', 'status', 'street_address', 'city', 'zip_code',
            'cell_phone', 'email',
        ]);

        Lead::whereIn('status_id', [14, 15, 17, 20, 19, 21, 23])
            ->chunk(200, function ($leads) use ($handle) {
                foreach ($leads as $lead) {
                    if ($lead->office_id === 5 || $lead->origin_office_id === 5) {
                        continue;
                    }
                    if (!$lead->customer) {
                        continue;
                    }

                    $fields = [
                        'name' => $lead->customer->first_name . ' ' . $lead->customer->last_name,
                        'language' => $lead->customer->language ?? '',
                        'status' => $lead->statusName->name ?? '',
                        'street_address' => $lead->customer->street_address,
                        'city' => $lead->customer->city,
                        'zip_code' => $lead->customer->zip_code,
                        'cell_phone' => $lead->customer->cell_phone,
                        'email' => $lead->customer->email,

                    ];

                    fputcsv($handle, $fields);
                }
            });

        fclose($handle);
    }
}
