<?php

namespace App\Console\Commands\newCommands;

use App\Models\Auth\User;
use App\Models\Epc\EpcFinance;
use App\Models\Epc\Finance;
use Illuminate\Console\Command;

class ExportSp2Number extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:exportsp2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds finances ';

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
        $fileName = 'sp2.csv';

        // Open the file handle once
        $handle = fopen($fileName, 'a');

        // Check if the file was opened successfully
        if (!$handle) {
            throw new \Exception('Failed to open file for writing.');
        }

        // Use chunk to process large data sets without consuming too much memory
        User::role('sp2')->chunk(200, function ($users) use ($handle) {
            foreach ($users as $user) {
                fputcsv($handle, [
                  $user->phone_number
                ]);
            }
        });

        // Close the file handle
        fclose($handle);
    }

}

