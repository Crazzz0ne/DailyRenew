<?php


namespace App\Console\Commands\Crons\OutsideSync;


use App\Models\Auth\User;
use App\Models\Auth\UserApi;
use App\Models\Office\Market\Market;
use App\Models\Office\Market\RegionAPI;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SalesRabbitUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncUserWithSalesRabbit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs user id with sales rabbit';

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
        $markets = Market::whereHas('apiKey', function ($q) {
            $q->where('type', 'sales-rabbit');
        })->with('apiKey')->get();
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.salesrabbit.com',
            // You can set any number of default request options.
            'timeout' => 500.0,
        ]);
        foreach ($markets as $market) {
            $apiKey = $market->apiKey['api_key'];

            try {
                $response = $client->request('GET', '/users', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey
                    ],
                ]);
                $body = json_decode($response->getBody()->getContents());


//                Log::debug('response from complete: ' . json_encode([$body]));
//                Log::debug('Lead updated from close: ' . json_encode([$lead]));
            } catch (ClientException | \Exception $e) {
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
//            Log::info('response from complete: ' . json_encode([$body]));
                Log::alert('Error response from complete:  ' . $responseBodyAsString);

            }
            $users = collect($body);

            foreach ($users['data'] as $userAPI) {

                $user = User::where('email', strtolower($userAPI->email))->first();
                if ($user) {
                    UserApi::updateOrCreate(
                        [
                            'type' => 'sales-rabbit',
                            'user_id' => $user->id,
                        ],
                        ['api_key' => $userAPI->id]
                    );
                }

            }
        }
    }
}
