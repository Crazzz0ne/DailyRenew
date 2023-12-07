<?php

namespace App\Console\Commands\newCommands;

use App\Models\SalesFlow\Lead\Lead;
use App\Services\GeoService;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GeoSpaceCommand extends Command
{

    protected GeoService $geoService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendtoGeoSpace';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds finances ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GeoService $geoService)
    {
        parent::__construct();
        $this->geoService = $geoService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $leads = Lead::where('office_id', 5)->orWhere('origin_office_id', 5)
            ->with('customer', 'salesPacket', 'reps')
            ->get();
//        reverse the order of leads
        $leads = $leads->reverse();
        $count = 0;
        foreach ($leads as $lead) {
            dump($count);
            $re = $this->formatData($lead);
            if ($re === 0) {
                dump('Couldnt find rep',
                    $lead->id
                );
            }
            $count++;
        }

        return 'done';
    }

    /**
     * @throws Exception
     */
    private function formatDate($date)
    {
        $dateTime = new DateTime($date);
        return $dateTime->format('M y');
    }

    private function tagsForLeads($lead)
    {
        $tags = [];

        if ($lead->status_id === 13) {
            $tags[] = 'PTO ' . $this->formatDate($lead->salesPacket->pto);
        }

        if ($lead->close_date != null) {
            $tags[] = 'Closed ' . $this->formatDate($lead->close_date);
        }

        if ($lead->salesPacket->sat) {
            $tags[] = $this->getSatTag($lead);
        }

        if ($lead->isCreditPass(true)) {
            $tags[] = 'Credit Pass ' . $this->formatDate($lead->created_at);
        } else if ($lead->credit_status_id === 7) {
            $tags[] = 'Credit Fail ' . $this->formatDate($lead->created_at);
        }
//        check proposals for high system size
        $proposals = $lead->proposals;
        if ($proposals) {
            foreach ($proposals as $proposal) {
                if ($proposal->system_size > 7) {
                    $tags[] = 'Large System Size ' . $this->formatDate($lead->created_at);
                }
            }
        }


        return $tags;
    }

    private function getSatTag($lead)
    {
        $appointment = $lead->appointments()->where('type_id', 6)->first();

        if ($appointment) {
            return 'SAT ' . $this->formatDate($appointment->start_time);
        }

        return 'SAT ' . $this->formatDate($lead->created_at);
    }

    private function findSrRep($lead)
    {
//        $lead->reps[]->position_id 2 or 3 return user email.
        $sp2 = $lead->reps()->where('position_id', 3)->first();
        $sp1 = $lead->reps()->where('position_id', 2)->first();

        if ($sp2) {
            return $sp2->email;
        } else if ($sp1) {
            return $sp1->email;
        } else {
            return null;
        }
    }

    private function formatData($lead)
    {
        if (!$lead->customer) {
            return 0;
        }
        $srRep = $this->findSrRep($lead);
        if (!$srRep) {
            return 0;
        }
        $tags = $this->tagsForLeads($lead);
        try {
            $payload = [
                'name' => $lead->customer->first_name . ' ' . $lead->customer->last_name,
                'street_address' => $lead->customer->street_address,
                'city' => $lead->customer->city,
                'state' => $lead->customer->state,
                'zip' => $lead->customer->zip_code,
                'tags' => $tags,
                'location' => ['longitude' => $lead->customer->lng, 'latitude' => $lead->customer->lat ?? 0],
                'email' => $srRep,
                'link' => 'https://scout.solarbrightwave.com/dashboard/lead/' . $lead->id,
            ];
        } catch (\Exception $e) {
            // You can log the error here if needed
            \Log::error("Error processing lead with ID: {$lead->id}. Error: {$e->getMessage()}");
            // Continue to the next iteration
            return 0;
        }

        $this->geoService->saveData($payload);
    }


}

