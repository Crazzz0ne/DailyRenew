<?php


namespace App\Console\Commands;


use App\Events\Backend\SalesFlow\Lead\Note\NewNoteEvent;
use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\MassText\MassText;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;


class Temp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Does Temp things';

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

//    public function getClient()
//    {
//
//
//        $client = new Google_Client();
//        $client->setApplicationName('Scout');
//        $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
//        $client->setAuthConfig(storage_path('app/google/service-account-credentials.json'));
//        $client->setAccessType('offline');
//        $client->setPrompt('select_account consent');
//        $client->setRedirectUri(env('GOOGLE_REDIRECT'));
//
//        // Load previously authorized token from a file, if it exists.
//        // The file token.json stores the user's access and refresh tokens, and is
//        // created automatically when the authorization flow completes for the first
//        // time.
//        $tokenPath = storage_path('app/google-calendar/service-account-credentials.json');
//        if (file_exists($tokenPath)) {
//            $accessToken = json_decode(file_get_contents($tokenPath), true);
//            $client->setAccessToken($accessToken);
//        }
//
//        // If there is no previous token or it's expired.
//        if ($client->isAccessTokenExpired()) {
//            // Refresh the token if possible, else fetch a new one.
//            if ($client->getRefreshToken()) {
//                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
//            } else {
//                // Request authorization from the user.
//                $authUrl = $client->createAuthUrl();
//                printf("Open the following link in your browser:\n%s\n", $authUrl);
//                print 'Enter verification code: ';
//                $authCode = trim(fgets(STDIN));
//
//                // Exchange authorization code for an access token.
//                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
//                $client->setAccessToken($accessToken);
//
//                // Check to see if there was an error.
//                if (array_key_exists('error', $accessToken)) {
//                    throw new Exception(join(', ', $accessToken));
//                }
//            }
//            // Save the token to a file.
//            if (!file_exists(dirname($tokenPath))) {
//                mkdir(dirname($tokenPath), 0700, true);
//            }
//            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
//        }
//        return $client;
//    }

    public function handle()
    {

        $viewLead = Permission::create(['name' => 'view lead']);


        $canvasser = Role::findByName('canvasser');
//

        $openerPermissions = $canvasser->permissions()->pluck('name');
        $opener = Role::create(['name' => 'opener']);
        $opener->syncPermissions($openerPermissions);

$canvasser->givePermissionTo('view lead');
        $sp1 = Role::findByName('sp1');
        $sp1->givePermissionTo('view lead');

        $sp2 = Role::findByName('sp2');
        $sp2->givePermissionTo('view lead');

        $manager = Role::findByName('manager');
        $manager->givePermissionTo('view lead');

        $pb = Role::findByName('proposal builder');
        $pb->givePermissionTo('view lead');

        $executive = Role::findByName('executive');
        $executive->givePermissionTo('view lead');

        $integrations = Role::findByName('integrations');
        $integrations->givePermissionTo('view lead');

        $proposalBuilder = Role::findByName('proposal builder');
        $proposalBuilder->givePermissionTo('view lead');


      $users = User::whereHas('office', function ($q) {
          $q->where('call_center', true);
      })->get();

      foreach ($users as $user) {
          if ($user->hasAllRoles(['canvasser'])) {
              $user->syncRoles(['opener']);
//              return $user;
          }
      }
    }
}
