<?php


namespace App\Console\Commands\Crons;


use App\Events\Backend\SalesFlow\TextEvent;
use App\Models\Auth\User;
use App\Models\Commission\CommissionLedgers;
use App\Models\Commission\Payroll;
use App\Models\SalesFlow\MassText\MassText;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TwilioOptOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:twilioOptOut';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Text';

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
        $users = User::all();

        foreach ($users as $user){

            if ($user->terminated == null){
                $user->twilio_opt_out = 1;
                $user->save();

            event(new TextEvent($user->phone_number, 'Hello from Scout,
             Reply START to receive future messages!
             Reply QUIT to unsubscribe'));
                }
        }


    }
}
