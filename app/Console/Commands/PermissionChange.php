<?php


namespace App\Console\Commands;


use App\Models\Auth\User;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\SalesPacket;
use Illuminate\Console\Command;

class PermissionChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:permissionsChange';

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
        $users = User::where('id', '!=', 1)
            ->with('positions')
            ->get();

       foreach ($users as $user){
           $array = [];

           foreach ($user->positions as $p){
              switch ($p->id){
                  case 1:
                     array_push($array, 'canvasser');
                     break;
                  case 2:
                      array_push($array, 'sp1');
                      break;
                  case 3:
                      array_push($array, 'sp2');
                      break;
                  case 4:
                      array_push($array, 'integrations');
                      break;
                  case 5:
                      array_push($array, 'sales rep');
                      break;
                  case 6:
                      array_push($array, 'proposal builder');
                      break;
              }
           }
           $user->syncRoles($array);

       }
    }

}
