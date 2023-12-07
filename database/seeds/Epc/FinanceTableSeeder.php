<?php
use App\Models\Epc\EpcFinance;
use GuzzleHttp\Client;
class FinanceTableSeeder  extends \Illuminate\Database\Seeder
{
    use DisableForeignKeys;

    public function run()
    {

        if (env('APP_ENV') === 'production') {
            $url = 'https://heliotrack.completesolar.com';
            $key = env('COMPLETE_API_SCES');
        } else {
            $url = 'https://heliotrack.completesolar.biz';
            $key = env('COMPLETE_API_CALLCENTER');
        }
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $url,
            // You can set any number of default request options.
            'timeout' => 500.0,
        ]);


        $response = $client->request('GET', '/api/finance_products', [
                'headers' => [
                    'x-api-key' => 'm3IBkklzbb0v1JrrDD6jPmhPkFx6Snkh'
                ]
            ]
        );

        $body = json_decode($response->getBody()->getContents());


        EpcFinance::create([
            'name' => 'CA - Sunnova 25yr 0.99%',
            'epc_id' => 1,
            'rate' => 1.49,
            'term' => 10,
            'fee' => 20,
            'epc_owner_id' => $body[0]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunrun PPA 0%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[1]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunrun PPA 1.9%',
            'epc_id' => 1,
            'rate' => .019,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[2]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunrun PPA 2.9%',
            'epc_id' => 1,
            'rate' => .029,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[2]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunlight Financial 10 yr 2.49%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunlight Financial 20 yr 1.49%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunnova 10yr 5.49%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunnova 25yr 1.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);
        EpcFinance::create([
            'name' => 'Sunnova 25yr 2.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunlight Financial 20 yr 0.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunnova 25yr 1.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);
        EpcFinance::create([
            'name' => 'CA - Sunnova 25yr 0.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'CA - Sunnova 25yr 1.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => ' CA - Sunnova PPA',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);
        EpcFinance::create([
            'name' => 'CA - Sunnova PPA-EZ',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'CA/TX - Sunnova Loan Storage 25yr 0%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'CalFirst 25yr 3.69% Sunrun Pre-paid',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);
        EpcFinance::create([
            'name' => 'Dividend 20yr 0.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);
        EpcFinance::create([
            'name' => 'Dividend 25yr 1.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunlight Financial 10yr 1.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunlight Financial 10yr 1.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunlight Financial 12yr 0.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);
        EpcFinance::create([
            'name' => 'Sunlight Financial 15yr 0.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunlight Financial 20yr 0.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);

        EpcFinance::create([
            'name' => 'Sunlight Financial 25yr 1.99%',
            'epc_id' => 1,
            'rate' => 0,
            'term' => 20,
            'fee' => 20,
            'epc_owner_id' => $body[3]->uuid
        ]);
    }
}
