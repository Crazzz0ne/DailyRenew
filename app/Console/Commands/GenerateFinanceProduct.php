<?php

namespace App\Console\Commands;

use App\Models\Epc\EpcFinance;
use App\Models\Epc\SolarModule;
use Illuminate\Console\Command;

class GenerateFinanceProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:fp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $finance_products = [
            0 => [
                'finance_product' => 'CA - Sunnova 25yr 0.99%',
                'uuid' => '7e3105f0-28f2-47db-8d00-8dae02125eab',
            ],
            1 => [
                'finance_product' => 'CA - Sunnova 25yr 1.99%',
                'uuid' => '39b6a718-24ba-4292-813b-0a9762def413',
            ],
            2 => [
                'finance_product' => 'CA - Sunnova PPA',
                'uuid' => '6ee20c60-a84c-46ef-b700-b34925438f02',
            ],
            3 => [
                'finance_product' => 'CA - Sunnova PPA-EZ',
                'uuid' => '2ae947e1-56a5-48c9-88d1-72d88b22a838',
            ],
            4 => [
                'finance_product' => 'CA/TX - Sunnova Loan Storage 25yr 0%',
                'uuid' => '73f6c25f-8a75-426b-842d-7ddd5678f425',
            ],
            5 => [
                'finance_product' => 'CalFirst 25yr 3.69% Sunrun Pre-paid',
                'uuid' => 'ede6b314-e102-418d-8ddd-85c43315fd95',
            ],
            6 => [
                'finance_product' => 'Cash',
                'uuid' => '4a33e2fd-b14e-486c-9876-2d4e5fa8286c',
            ],
            7 => [
                'finance_product' => 'Dividend 20yr 0.99%',
                'uuid' => '559c71e6-3b03-4cbe-a981-8f6b00278737',
            ],
            8 => [
                'finance_product' => 'Dividend 25yr 1.99%',
                'uuid' => '11ee416a-f4f1-468b-84b1-26c141d2657e',
            ],
            9 => [
                'finance_product' => 'Mosaic 10yr 1.49%',
                'uuid' => 'b5359a13-e778-499a-b1d6-4794e152cf4f',
            ],
            10 => [
                'finance_product' => 'Mosaic 12yr 2.99%',
                'uuid' => '6beaa823-b0a4-463b-ba23-eb7a056c9a42',
            ],
            11 => [
                'finance_product' => 'Mosaic 15yr 1.49%',
                'uuid' => '915a3ce6-6749-49ab-8ed4-3988d38d760c',
            ],
            12 => [
                'finance_product' => 'Mosaic 15yr 1.9%',
                'uuid' => 'abb48873-568a-445a-ab94-4784ae5228c9',
            ],
            13 => [
                'finance_product' => 'Mosaic 20yr 0.99%',
                'uuid' => '17396c02-c81f-4798-90ca-1bc0d31e6d6a',
            ],
            14 => [
                'finance_product' => 'Mosaic 25yr 1.99%',
                'uuid' => '280fbe7d-9874-49df-93e2-aca6ee5157ec',
            ],
            15 => [
                'finance_product' => 'Mosaic PowerSwitch 20yr 2.99%',
                'uuid' => 'f78ec89f-5535-4888-9b6b-ce05ba5a8d30',
            ],
            16 => [
                'finance_product' => 'NC/SC Mosaic 25yr 1.99%',
                'uuid' => '2457014f-108c-4d7f-9b28-17e534a48b60',
            ],
            17 => [
                'finance_product' => 'NC/SC Sunlight 10yr 0.99%',
                'uuid' => 'd83bb0c7-a870-4a73-bd69-aea37866a425',
            ],
            18 => [
                'finance_product' => 'NC/SC Sunlight 12yr 2.99%',
                'uuid' => 'dd1bec3a-e060-46ab-bfe1-d30ffdc06e70',
            ],
            19 => [
                'finance_product' => 'NC/SC Sunlight 15yr 3.99%',
                'uuid' => '84f1543f-c36f-4f94-8a5f-8117c4a70d0c',
            ],
            20 => [
                'finance_product' => 'NC/SC Sunlight 20yr 1.99%',
                'uuid' => 'f0c53a10-8f77-436d-adab-6754039da16f',
            ],
            21 => [
                'finance_product' => 'Sunlight Financial 10yr 1.99%',
                'uuid' => 'cf6dcaa3-b5fb-4d8d-b27c-c4f09fbce318',
            ],
            22 => [
                'finance_product' => 'Sunlight Financial 12yr 0.99%',
                'uuid' => 'f490b7fa-40f1-4ed5-b3c8-742cf7306db3',
            ],
            23 => [
                'finance_product' => 'Sunlight Financial 15yr 0.99%',
                'uuid' => 'e5457dc3-235a-44bb-a64d-4de7bd7b31d6',
            ],
            24 => [
                'finance_product' => 'Sunlight Financial 20yr 0.99%',
                'uuid' => '02abce85-6999-4f03-b34f-4bbacfe0fbb3',
            ],
            25 => [
                'finance_product' => 'Sunlight Financial 25yr 1.99%',
                'uuid' => '25d385f0-5f76-43e9-9e22-a9412a61219d',
            ],
            26 => [
                'finance_product' => 'Sunlight Solar+Roof 10yr 0.99%',
                'uuid' => 'dc64f1ee-d6fa-4f2b-9bec-559cf9ed1487',
            ],
            27 => [
                'finance_product' => 'Sunlight Solar+Roof 10yr 1.49%',
                'uuid' => 'b4c45e86-78ce-4f21-bf3d-72a77f9dd239',
            ],
            28 => [
                'finance_product' => 'Sunlight Solar+Roof 20yr 0.99%',
                'uuid' => 'c117e843-3ff9-42d3-9e0b-f084dd193785',
            ],
            29 => [
                'finance_product' => 'Sunlight Solar+Roof 20yr 1.49%',
                'uuid' => '34b0d822-3a6e-4ee7-99e2-e61d323132bb',
            ],
            30 => [
                'finance_product' => 'Sunrun Lease',
                'uuid' => '9cffc922-8226-4443-9028-5043019dee2f',
            ],
            31 => [
                'finance_product' => 'Sunrun Monthly PPA',
                'uuid' => '7f79b34e-a741-4a89-8370-81fc0c7e4bbf',
            ],
            32 => [
                'finance_product' => 'Sunrun PPA Prepaid',
                'uuid' => '4d066073-c94b-4018-b164-f9ab7ee374a8',
            ],
        ];

        foreach ($finance_products as $fp)
        {
            $term = strpos($fp['finance_product'], 'yr') - 2;

            EpcFinance::create([
                'name' => $fp['finance_product'],
                'epc_id' => 1,
                'rate' => '',
                'term' => $term,
                'fee'=> '',
                'epc_owner_id' => $fp['uuid'],
            ]);
        }
    }
}


