<?php


namespace App\Console\Commands\Crons;


use App\Events\Backend\SalesFlow\TextEvent;
use App\Models\Commission\CommissionLedgers;
use App\Models\SalesFlow\MassText\MassText;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\WebhookServer\WebhookCall;


class ZapCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendCommZap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends All Zaps';

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

        $sevenDaysAgo = Carbon::now()->subDays(7)->startOfDay()->toDateTimeString();

        $commissions = CommissionLedgers::where('created_at', '>', $sevenDaysAgo)->with('lead.customer')->get();


        foreach ($commissions as $commission) {
            $pay = [
                'id' => $commission->id,
                'date' => Carbon::parse($commission->created_at)->format('m/d/yy'),
                'leadId' => $commission->lead_id,
                'type' => $commission->type->name,
                'rep name' => $commission->user->full_name,
                'amount' => $commission->amount,
                'customer name' => $commission->lead->customer->fullName
            ];

            WebhookCall::create()
                ->useSecret('sign-using-this-secret')
                ->url('https://hooks.zapier.com/hooks/catch/8765154/opgxgd8')
                ->payload($pay)
                ->dispatch();


        }



    }
}
