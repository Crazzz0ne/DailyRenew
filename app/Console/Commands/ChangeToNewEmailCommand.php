<?php

namespace App\Console\Commands;

use App\Mail\SalesFlow\BaseMailable;
use App\Mail\Support\EmailChangeMailable;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ChangeToNewEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:changeEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $list = [
            'milton@solcalenergy.com',
            'alexis@solcalenergy.com',
            'Nefertary@solcalenergy.com',
            'basher26@live.com',
            'wyrmknight17@gmail.com',
            'Jerrygee832@gmail.com',
            'Miltonarroyo30@gmail.com',
            'elizabethgnlz@aol.com',
            'justin.andersen@solcalenergy.com',
            'Nfurmansolar@gmail.com',
            'abraham00.cera@gmail.com',
            'Mccordja94@gmail.com',
            'ryan.ostronic@solcalenergy.com',
            'tate.daniels@solcalenergy.com',
            'omar@solcalenergy.com',
            'dan.wilkins@solcalenergy.com',
            'jeff.carn@solcalenergy.com',
            '4irjosh@gmail.com',
            'mjinnovations20@gmail.com',
            'info@totalcleanenergy.com',
            'jbahena1216@gmail.com'
        ];

        foreach ($list as $l) {
            $user = User::where('email', $l)->first();

            if ($user == null){
                continue;
            }

            $newEmail = strtolower($user->first_name) . '.' . strtolower($user->last_name) . '@sunrunsolar.info';

            $user->update([
                'email' => $newEmail,
            ]);

            Mail::to($user->email)->queue(new EmailChangeMailable($newEmail, $user->first_name));

        }


    }
}
