<?php

use App\Models\Epc\SolarInverter;
use GuzzleHttp\Client;
class SolarInverterTableSeeder extends \Illuminate\Database\Seeder
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


        $response = $client->request('GET', '/api/solar_inverters', [
                'headers' => [
                    'x-api-key' => 'm3IBkklzbb0v1JrrDD6jPmhPkFx6Snkh'
                ]
            ]
        );

        $body = json_decode($response->getBody()->getContents());


        SolarInverter::create([
            'name' => '3.8KW SolarEdge',
            'epc_id' => 1,
            'epc_owner_id' => $body[0]->uuid,
            'model' => 'SE3800H-US RGM'

        ]);

        SolarInverter::create([
            'name' => '5KW SolarEdge',
            'epc_id' => 1,
            'epc_owner_id' => $body[0]->uuid,
            'model' => 'SE5000H-US RGM'

        ]);
    }
}
