<?php


namespace App\Console\Commands\Crons;


use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\SalesPacket;
use Illuminate\Console\Command;

class MakeThemSit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:makeThemSit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'makes sure closed deals are sat';

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

        $leads =  Lead::where('close_date', '!=', null)
            ->with('salesPacket')->get();

        foreach ($leads as $lead){
            SalesPacket::where('id', $lead->salesPacket->id)->update(['sat' => true]);
        }



    }
}
