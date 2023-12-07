<?php


namespace App\Console\Commands;


use App\Events\Backend\SalesFlow\MailChimpDripAddEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Models\SalesFlow\MassText\MassText;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MailChimpDripAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:mailChimpMassAdd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'adds 30 people to mailchimp';

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
        $emails = MassText::where('sent', '=', 0)
            ->where('type', '=', 'mailchimp')
            ->take(40)
            ->get();
        foreach ($emails as $email) {
            $customerName = explode(' ', trim($email->customer_name));
            event(new MailChimpDripAddEvent($email->email, $customerName[0], $customerName[1]));
        }
    }
}
