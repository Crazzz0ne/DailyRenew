<?php


namespace App\Console\Commands\Crons;


use App\Events\Backend\SalesFlow\TextEvent;
use App\Models\SalesFlow\MassText\MassText;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CronTextInvite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendMassText30';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send A text to phone numbers that were uploaded to /masstext';

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
        $texts = MassText::where('sent', '=', 0)
            ->type('review')
            ->take(15)
            ->get();

        $i =0;
        foreach ($texts as $text) {

            $body = '👋😷↔️😷👋
            Hi ' . $text->customer_name . ', this is SolCal Energy Solutions your Sunrun Certified Partner,
            Thank you for your recent solar business with ' . $text->rep_name . ', and congrats on your installation!
            leave us a review from your initial consolation to your seamless installation and
            savings you are experiencing on Google and Yelp - we just got our page going and it would mean the world to us.
            When you\'ve done it, will you text me back @ 760-689-9262 so I can Venmo you $10 💸 as a token of appreciation during these hard times.
            https://yelp.to/qTKq/dAYpqDfTw5
            http://bit.ly/solcalenergy_google
            ';

            event(new TextEvent($text->customer_number, $body));
            $i++;
            $text->sent = true;
            $text->sent_date = Carbon::now();
            $text->save();

        }
        if($i) {

            event(new TextEvent('8564306685', 'Robot has sent ' . $i . ' text messages brace for the awesome reviews! - Chris'));
            event(new TextEvent('6199406423', 'Robot has sent ' . $i . ' text messages brace for the awesome reviews! - Chris'));
        }
    }
}
