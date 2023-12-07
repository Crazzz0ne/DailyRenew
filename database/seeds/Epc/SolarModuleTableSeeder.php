<?php

use App\Models\Epc\SolarModule;
use GuzzleHttp\Client;

class SolarModuleTableSeeder extends \Illuminate\Database\Seeder
{
    use DisableForeignKeys;

    public function run()
    {


        if (env('APP_ENV') === 'production') {
            $url = 'https://heliotrack.completesolar.com/';
        } else {
            $url = 'https://heliotrack.completesolar.biz/';
        }
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $url,
            // You can set any number of default request options.
            'timeout' => 500.0,
        ]);


        $response = $client->request('GET', '/api/solar_modules', [
                'headers' => [
                    'x-api-key' => 'm3IBkklzbb0v1JrrDD6jPmhPkFx6Snkh'
                ]
            ]
        );

        $body = json_decode($response->getBody()->getContents());

        $i = 0;
        $watts = 360;
        foreach ($body as $b) {
            if ($i === 2) {
                break;
            }
            SolarModule::create([
                'name' => $b->manufacturer,
                'epc_id' => 1,
                'watts' => $watts,
                'epc_owner_id' => $b->uuid,
                'model' => $b->model
            ]);

            SolarModule::create([
                'name' => $b->manufacturer,
                'epc_id' => 1,
                'watts' => $watts,
                'epc_owner_id' => $b->uuid,
                'model' => $b->model
            ]);
            $i++;
            $watts += 5;
        }

    }
}
