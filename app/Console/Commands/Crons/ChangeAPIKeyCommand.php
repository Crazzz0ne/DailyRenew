<?php

namespace App\Console\Commands\Crons;

use App\Models\Auth\User;
use App\Models\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ChangeAPIKeyCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:rotateAPIKey';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change All keys';

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
        User::all()->each(function ($user){
            $user->api_token = Str::random(60);
            $user->save();
        });
    }
}
