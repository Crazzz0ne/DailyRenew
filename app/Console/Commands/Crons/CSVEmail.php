<?php


namespace App\Console\Commands\Crons;


use App\Events\Backend\SalesFlow\TextEvent;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\SalesFlow\MassText\MassText;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage as Storage;

class CSVEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:cronSendPayrollCSV';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a payroll csv to need to know parties. ';

    protected $leadRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->leadRepository = new LeadRepository();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $endOfPayRange = Carbon::now()->hour(20)->minutes(00)->second(00);
        $startofPayRange = Carbon::now()->hour(20)->minutes(01)->second(00)->subDays(7)->minutes(1);

		try {
			$csv =  $this->leadRepository->csvPayRollOutPut($startofPayRange, $endOfPayRange);
			$path = 'tmp/'.$startofPayRange->format('M-d').'-'.$endOfPayRange->format('d').'RAW-payroll.csv';
			$csvPath = Storage::put($path, $csv);

		} catch (\Exception $e ) {
			\Log::alert('Problem writing payroll CSV');

			$subject = 'Problem writing payroll CSV';
			$body = '';
			Mail::to('chrisfurman86@gmail.com')
				->send(new BaseMailable($subject, $body,null, $path));
		}

		try {
			$subject ='Weekly payroll CSV '. $startofPayRange->format('M-d'). ' To  '. $endOfPayRange->format('d') ;
			$body ='';

			Mail::to('chrisfurman86@gmail.com')
				->send(new BaseMailable($subject, $body,null, $path));
		} catch (\Exception $e) {
			\Log::alert('Unable to send payroll Email');
			\Log::alert($e);
		}
	}
}
