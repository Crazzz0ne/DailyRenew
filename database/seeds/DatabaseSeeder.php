<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    use TruncateTable;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        Model::unguard();

        $this->truncateMultiple([
            'cache',
            'jobs',
            'sessions',
        ]);

        $this->call(AuthTableSeeder::class);
        $this->call(MarketTableSeeder::class);
        $this->call(AnnouncementTableSeeder::class);
        $this->call(OfficesTableSeeder::class);
//        $this->call(OfficeUserTableSeeder::class);
        $this->call(OfficesStandingDataTableSeeder::class);
        $this->call(OfficeStandingTableSeeder::class);
        $this->call(ManagerEfficiencyTableSeeder::class);
        $this->call(LinkTableSeeder::class);
        $this->call(VendorTableSeeder::class);
        $this->call(LinkLoginTableSeeder::class);
        $this->call(CategoryTableSeeder::class);
        $this->call(TrainingCategoryTableSeeder::class);
        $this->call(TrainingContentTableSeeder::class);
        $this->call(CollateralContentSeeder::class);
        $this->call(CollateralCategorySeeder::class);
        $this->call(MastermindContentTableSeeder::class);
        $this->call(MastermindCategoryTableSeeder::class);
//        $this->call(CustomerTableSeeder::class); //Populated in LeadTableSeeder
        $this->call(CommissionTypesSeeder::class);
        $this->call(OfficeCommissionsSeeder::class);
        $this->call(PositionTableSeeder::class);
        $this->call(EpcTableSeeder::class);
        $this->call(RoofTypesSeeder::class);
        $this->call(AppointmentTypeTableSeeder::class);
//        $this->call(LeadUtilityTableSeeder::class); //Populated in LeadTableSeeder
        $this->call(LeadTableSeeder::class);
        $this->call(RoundRobinTableSeeder::class);
        $this->call(AddersTableSeeder::class);
        $this->call(FinanceTableSeeder::class);
        $this->call(PowerCompanyTableSeeder::class);
        $this->call(SolarInverterTableSeeder::class);
        $this->call(SolarModuleTableSeeder::class);
        $this->call(RequestedSystemSeeder::class);
        $this->call(PayrollSeeder::class);
        $this->call(CommissionLedgerSeeder::class);
        $this->call(AppointmentAvailabilitySeeder::class);
        $this->call(PayrateSeeder::class);
        $this->call(NotificationSeeder::class);
        Model::reguard();
    }
}
