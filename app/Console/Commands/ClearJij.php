<?php


namespace App\Console\Commands;


use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Console\Command;

class ClearJij extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fixJij';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes JIJ error';

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
        Lead::whereIn('jeopardy_id', [14, 15, 16, 17, 19, 20, 21])->update(['jeopardy_id' => null]);


    }
}
