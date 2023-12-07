<?php

namespace App\Console\Commands\Crons;

use App\Models\BatchedJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;

class SendBatchedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendBatchedJobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Zaps to Zapier';

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
        $batchedJobs = BatchedJob::all();

        foreach ($batchedJobs as $job) {
            $data = json_decode($job->data, true);
                $response = $this->sendToZapier($data);
                if ($response->getStatusCode() == 200) {
                    BatchedJob::where('id', $job->id)->delete();
                }
                sleep(10);
        }


    }

    function sendToZapier($jobsData)
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', 'https://hooks.zapier.com/hooks/catch/16196972/3rasjz5/', [
            'json' => [ $jobsData],
        ]);
        return $res;
    }
}
