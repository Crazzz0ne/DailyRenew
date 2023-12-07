<?php
use App\Models\Epc\PowerCompany\PowerCompany;
use GuzzleHttp\Client;
use App\Models\Office\Market\PowerCompany\Program;

class PowerCompanyTableSeeder extends \Illuminate\Database\Seeder
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


        $response = $client->request('GET', '/api/utilities?zip_code=91941', [
                'headers' => [
                    'x-api-key' => 'm3IBkklzbb0v1JrrDD6jPmhPkFx6Snkh'
                ]
            ]
        );

        $body = json_decode($response->getBody()->getContents());
        PowerCompany::create([
            'name' => 'SDGE',
            'epc_id' => 1,
            'epc_owner_id' => $body[0]->uuid

        ]);

        Program::create([
            'name' => 'TOU -D-4-9',
            'type' => 'rate_plan',
            'power_company_id' => '1'
        ]);

        Program::create([
            'name' => 'TOU -D-5-8',
            'type' => 'rate_plan',
            'power_company_id' => '1'
        ]);

        Program::create([
            'name' => 'None',
            'type' => 'discount_program',
            'power_company_id' => '1'
        ]);
        Program::create([
            'name' => 'SDP',
            'type' => 'discount_program',
            'power_company_id' => '1'
        ]);

//        PowerCompany::create([
//            'name' => 'SCE',
//            'epc_id' => 2,
//            'epc_owner_id' => ''
//
//        ]);
//
//        PowerCompany::create([
//            'name' => 'PGE',
//            'epc_id' => 1,
//            'epc_owner_id' => '2a9b2bc5-9598-4522-9fc8-1ecac23e0e1f'
//
//        ]);
//
//        PowerCompany::create([
//            'name' => 'NVE - South',
//            'epc_id' => 1,
//            'epc_owner_id' => '2a9b2bc5-9598-4522-9fc8-1ecac23e0e1f'
//
//        ]);
//
//
//        PowerCompany::create([
//            'name' => 'SMUD',
//            'epc_id' => 1,
//            'epc_owner_id' => 'c0a4238d-802d-4eec-97de-18c78dd277b1'
//
//        ]);
//
//
//        PowerCompany::create([
//            'name' => 'SDGE',
//            'epc_id' => 1,
//            'epc_owner_id' => '3b8e428f-9d1d-4a7f-afad-8bf90893cd04'
//
//        ]);
//
//        PowerCompany::create([
//            'id' => 34,
//            'name' => 'LADWP',
//            'epc_id' => 1,
//            'epc_owner_id' => '3b8e428f-9d1d-4a7f-afad-8bf90893cd04'
//
//        ]);


    }
}
