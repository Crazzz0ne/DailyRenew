<?php

namespace App\Console\Commands;

use App\Models\Epc\SolarInverter;
use App\Models\Epc\SolarModule;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\System\System;
use Illuminate\Console\Command;

class GenerateInverters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:Inverters';

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
        $inverters = [
            [
                'manufacturer' => '3.8kw SolarEdge ',
                'model' => 'SE3800H-US RGM',
                'uuid' => '634977cd-4cae-4170-aee0-7641ab5c2bce',
            ],
            [
                'manufacturer' => '5kw SolarEdge',
                'model' => 'SE5000H-US RGM',
                'uuid' => 'f547404c-3afa-41f0-8ff6-e9eee2227fb4',
            ],
            [
                'manufacturer' => '6kw SolarEdge',
                'model' => 'SE6000H-US RGM',
                'uuid' => '0d270eef-82d9-4d35-8cd9-2da8729615a6',
            ],
            [
                'manufacturer' => '7.6kw SolarEdge',
                'model' => 'SE7600H-US RGM',
                'uuid' => 'e77454c5-1024-45ac-b55b-0af2246232e2',
            ],
            [
                'manufacturer' => '3kw SolarEdge',
                'model' => 'SE3000H-US RGM',
                'uuid' => 'd6f1b8a7-8cf2-4aa5-bc1c-bab9ab96e7ac',
            ],
            [
                'manufacturer' => 'Enphase Energy 240w',
                'model' => 'IQ7-60-2-US',
                'uuid' => '5fcfae24-01e2-42ad-beb5-a661647ae152',
            ],
            [
                'manufacturer' => 'Enphase Energy 295w',
                'model' => 'IQ7PLUS-72-2-US',
                'uuid' => '93cd0dbc-b742-4d69-8354-8cc5c65a773d',
            ],
            [
                'manufacturer' => '10kw SolarEdge',
                'model' => 'SE10000H-US RGM',
                'uuid' => 'c8679a53-39e1-40ba-a424-c49b03406401',
            ],
            [
                'manufacturer' => '11.4kw SolarEdge',
                'model' => 'SE11400H-US RGM',
                'uuid' => '85c3c634-a1f6-4dfa-9847-c6ce58ce265c',
            ],
        ];

        foreach ($inverters as $inverter)
        {
            SolarInverter::create([
                'name' => $inverter['manufacturer'],
                'epc_owner_id' => $inverter['uuid'],
                'model' => $inverter['model'],
            ]);
        }

        $requestedSystems = RequestedSystem::all();
        $proposedSystems = ProposedSystem::all();
        $system = System::all();
        $this->updateSystems($requestedSystems);
        $this->updateSystems($proposedSystems);
        $this->updateSystems($system);

    }

    public function updateSystems($systems){
        foreach ($systems as $system){
            switch ($system->inverter_id){
                case 9:
                    $system->inverter_id = 7;
                    break;
                case 8:
                    $system->inverter_id = 4;
                    break;
                case 7:
                    $system->inverter_id = 3;
                    break;
                case 6:
                    $system->inverter_id = 2;
                    break;
                case 5:
                    $system->inverter_id = 1;
                    break;
            }
            $system->save();
        }
    }
}
