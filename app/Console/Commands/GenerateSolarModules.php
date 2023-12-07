<?php

namespace App\Console\Commands;

use App\Models\Epc\SolarModule;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use Illuminate\Console\Command;

class GenerateSolarModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:solarModules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Solar Modules';

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
        $solar_modules = [
            [
                'manufacturer' => 'LONGi 350w',
                'watt' => 350,
                'model' => 'LR4-60HPB-350M',
                'uuid' => '86b6ea46-d02b-42c2-970c-ab22f756561c',
            ],
            [
                'manufacturer' => 'LG 410w',
                'watt' => 410,
                'model' => 'LG410N2T-L5',
                'uuid' => 'fd0ce77b-0e6c-4d19-a7b2-d22e0bf29b9b',
            ],
            [
                'manufacturer' => 'CertainTeed 325w',
                'watt' => 325,
                'model' => 'CT325HC11-04',
                'uuid' => '15a2e933-e4df-4dd6-b363-6722827f0670',
            ],
            [
                'manufacturer' => 'LG 360w',
                'model' => 'LG360N1C-N5',
                'watt' => 360,
                'uuid' => '8477b651-cdc3-4003-a4be-492e31de8403',
            ],
            [
                'manufacturer' => 'LG 365w',
                'model' => 'LG365M1C-N5',
                'watt' => 365,
                'uuid' => '49b4e06f-ba1c-483d-bc28-c31f4b33d672',
            ],
            [
                'manufacturer' => 'Silfab Solar 370w',
                'model' => 'SIL-370 BK',
                'watt' => 370,
                'uuid' => '6ef1f082-d987-41ad-997a-f6f307242b32',
            ],
            [
                'manufacturer' => 'Silfab Solar 360w',
                'model' => 'SIL-360 NX',
                'watt' => 360,
                'uuid' => '25de4872-4c54-4312-8be5-eb920ba2a1f4',
            ],
            [
                'manufacturer' => 'Certainteed 360w',
                'model' => '360',
                'watt' => 360,
                'uuid' => 'ba22ed4a-b45b-4778-98b0-1e05426b7a19',
            ],
            [
                'manufacturer' => 'CertainTeed 300w',
                'model' => 'CT300M11-03',
                'watt' => 300,
                'uuid' => '77cfc025-3937-43d9-b8d4-985d79edb87c',
            ],
        ];

        foreach ($solar_modules as $module)
        {
            SolarModule::create([
                'name' => $module['manufacturer'],
                'watts' => $module['watt'],
                'epc_owner_id' => $module['uuid'],
                'model' => $module['model'],
            ]);
        }


    }
}
