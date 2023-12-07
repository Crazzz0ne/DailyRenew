<?php

use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadLogin;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\LeadRoof;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class LeadTableSeeder extends \Illuminate\Database\Seeder
{
    use DisableForeignKeys;

    public static int $AMOUNT_TO_SEED = 10;
    private static int $createdSoFar = 0;
    private CarbonImmutable $closeDate;
    private CarbonImmutable $surveyDate;
    private CarbonImmutable $installDate;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $isPending = true;
        $this->generateNewDates();
        $this->disableForeignKeys();
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 2; $i++) {
            factory(Lead::class, self::$AMOUNT_TO_SEED)
                ->create([
                    'status_id' => $isPending ? 11 : 1,
                    'close_date' => $isPending ? fn() => $this->generateNewDates() : null,
                    'office_id' => 1,
                    'epc_id' => 1,
                    'credit_status_id' => 1,
                    'integrations_approved' => 3,
                    'origin_office_id' => 1,
                    'utility_id' => fn() => factory(LeadUtility::class)->create()->id,
                    'customer_id' => fn() => factory(Customer::class)->create()->id,
                    'system_id' => fn() =>factory(\App\Models\SalesFlow\Lead\System\System::class)->create()->id,
                    'sales_packet_id' => fn() => factory(SalesPacket::class)
                        ->create([
                            'sat' => $isPending ? 1 : 0,
                            'converted' => $this->surveyDate->addDays(5),
                            'site_plan' => $this->surveyDate->addWeeks(2)->addDays(5),
                            'pto' => null,
                            'design_plan_sent_date' => $this->surveyDate->addDays(5),
                            'submitted_for_permitting_date' => $this->closeDate->addDays(20),
                            'permitting_received_date' => $this->closeDate->addMonth()->addDays(20),
                            'nem_doc_signed' => Carbon::now()->addDays(rand(1, 7)),
                            'cpuc_doc_signed' => Carbon::now()->addDays(rand(1, 7)),
                            'ach_doc_signed' => null,
                            'credit_doc_signed' => null,
                            'solar_agreement_signed' => null,
                            'proposal_doc_signed' => null,
                            'site_survey_note' => 'FILLED FROM LEAD SEEDER',
                            'deleted_at' => null,
                            'submitted' => null,
                        ])->id,
                ])
                ->each(function ($lead) use ($faker, $isPending) {
                    if ($isPending) {
                        factory(Appointment::class)
                            ->create([
                                'user_id' => rand(1, 10),
                                'subject' => "Install Date",
                                'comment' => "Seeded Appointment Install Date, Lead ID: {$lead->id}",
                                'type_id' => 5,
                                'lead_id' => $lead->id,
                                'remote' => 0,
                                'start_time' => $this->installDate,
                                'finish_time' => $this->installDate->addHour(),
                            ]);

                        factory(Appointment::class)
                            ->create([
                                'user_id' => rand(1, 10),
                                'subject' => "Site Survey",
                                'comment' => "Seeded Appointment Site Survey, Lead ID: {$lead->id}",
                                'type_id' => 4,
                                'lead_id' => $lead->id,
                                'remote' => 0,
                                'start_time' => $this->surveyDate,
                                'finish_time' => $this->surveyDate->addHour(),
                            ]);
                    }

                    factory(LeadNote::class, rand(0, 3))->create([
                        'lead_id' => $lead->id,
                        'user_id' => fn() => rand(1, 100),
                        'note' => fn() => $faker->realText(160)
                    ]);


                    factory(LeadRoof::class)->create([
                        'lead_id' => $lead->id,
                        'roof_type_id' => fn() => rand(1, 14),
                        'age' => fn() => rand(1, 30)
                    ]);
//$proposals = fn() => rand(1, 4);


                    factory(UserHasLead::class, 1)
                        ->create([
                            'user_id' => rand(1, 100),
                            'lead_id' => $lead->id,
                            'position_id' => 1,
                        ]);
                    factory(UserHasLead::class, 1)
                        ->create([
                            'user_id' => rand(1, 100),
                            'lead_id' => $lead->id,
                            'position_id' => 2,
                        ]);
                    factory(UserHasLead::class, 1)
                        ->create([
                            'user_id' => rand(1, 100),
                            'lead_id' => $lead->id,
                            'position_id' => 3,
                        ]);


                    //Close Half Sits
                    $shouldCloseSit = ((self::$createdSoFar % 2) == 0);

                    if (!$shouldCloseSit) {
                        $lead->close_date = null;
                        $lead->save();
                    }

                    //Keep track of our count.
                    self::$createdSoFar += 1;
                });

            //Flip the switch for the 2nd run
            $isPending = true;
        }
        //Clean up, we're done here.
        $this->enableForeignKeys();
    }

    private function generateNewDates(): CarbonImmutable
    {
        $rng = rand(0, 28);

//        Log::info("Generating new dates: ", [$rng]);
        $this->closeDate = CarbonImmutable::now();
        Log::info($this->closeDate);
        $this->surveyDate = $this->closeDate->addDays(rand(1, 2));
        $this->installDate = $this->surveyDate->addWeeks(rand(1, 7));

        return $this->closeDate;
    }
}
