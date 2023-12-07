<?php


namespace App\Console\Commands;


use App\Models\Epc\PowerCompany\PowerCompany;
use App\Models\Epc\SolarInverter;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\System\System;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GeneratePowerCompanyFromComplete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:PowerCompany';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Solar Inverters in DB.';

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
        if (app()->environment('production')) {
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

        $customers = Customer::all();

        $zips = $customers->pluck('zip_code')->unique();
        $zipCodes = $zips->values()->all();

        foreach ($zipCodes as $zipCode) {
            if (strlen($zipCode) !== 5) {
                continue;
            }
            $response = $client->request('GET', '/api/utilities?zip_code=' . $zipCode, [
                'headers' => [
                    'x-api-key' => 'm3IBkklzbb0v1JrrDD6jPmhPkFx6Snkh'
                ]
            ]);
            $powerCompanyList = json_decode($response->getBody()->getContents());

            if ($powerCompanyList === null) {
                continue;
            }
            foreach ($powerCompanyList as $body) {
                switch ($body->name) {
                    case 'San Diego Gas & Electric Co':
                        $powerCompany = PowerCompany::where('name', 'SDGE')->where('epc_id', 1)->update(['epc_owner_id' => $body->uuid, 'name' => $body->name]);
                        break;
                    case 'Los Angeles Department of Water & Power':
                        $powerCompany = PowerCompany::where('name', 'LADWP')->where('epc_id', 1)->update(['epc_owner_id' => $body->uuid, 'name' => $body->name]);
                        break;
                    case 'Southern California Edison Co':
                        $powerCompany = PowerCompany::where('name', 'SCE')->where('epc_id', 1)->update(['epc_owner_id' => $body->uuid, 'name' => $body->name]);
                        break;
                    case 'Pacific Gas & Electric Co':
                        $powerCompany = PowerCompany::where('name', 'PGE')->where('epc_id', 1)->update(['epc_owner_id' => $body->uuid, 'name' => $body->name]);
                        break;
                    default:
                        $powerCompany = PowerCompany::updateOrCreate(
                            ['name' => $body->name],
                            ['name' => $body->name,
                                'epc_id' => 1,
                                'epc_owner_id' => $body->uuid
                            ]);
                        break;
                }
            }
            sleep(10);

        }
        return 1;
    }
}
