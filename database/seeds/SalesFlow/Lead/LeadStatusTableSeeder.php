<?php

use App\Models\SalesFlow\Lead\LeadStatus;

class LeadStatusTableSeeder extends \Illuminate\Database\Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        factory(LeadStatus::class, 1)->create([
            'name' => 'New Lead',
            'description' => 'This is a new lead!',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Pending Credit Check',
            'description' => 'This lead is waiting to clear a pending credit check.',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Credit Pass',
            'description' => 'This lead has passed their pending credit check!',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Usage Unavailable',
            'description' => 'The usage of this lead is unavailable.',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Negotiating System',
            'description' => 'The negotiating has begun!',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Pending Paper Work',
            'description' => 'Waiting for lead to supply required documents.',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Pending Site Survey',
            'description' => "Waiting for the surveyor's report.",
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Change Order',
            'description' => 'Change Order has been requested.',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Pending Site Plan Signed',
            'description' => 'The pending site plan has been signed.',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Pending Install',
            'description' => 'Waiting for completion of installation.',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Installed',
            'description' => 'Installation has concluded.',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'PTO',
            'description' => 'PTO',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Cancelled Appointment',
            'description' => 'The appointment was cancelled! :(',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Failed Credit',
            'description' => 'Credit Check Failed!',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Job In Jeopardy',
            'description' => 'At risk of losing the job!',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Hard No',
            'description' => 'Aww hell nah',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Low Usage',
            'description' => 'The usage is low.',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Missed Opportunity',
            'description' => "Can't let opportunities pass you by!",
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'No Show',
            'description' => "Didn't even bother....",
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Cancelled',
            'description' => 'Job was Closed/Sold, but then cancelled.',
        ]);

        factory(LeadStatus::class, 1)->create([
            'name' => 'Soft No',
            'description' => 'There may be some possible convincing to do...',
        ]);
        $this->enableForeignKeys();
    }

}
