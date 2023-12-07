<?php


namespace App\Listeners\Backend\SalesFlow;


use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Models\Epc\SolarModule;
use App\Models\Office\Market\RegionAPI;
use App\Models\SalesFlow\Lead\Lead;

use Carbon\Carbon;

use Exception;
use GuzzleHttp\Client;


use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CreateCompleteLeadListener implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->lead->customer->state !== 'CA') {
            return true;
        }

        $lead = Lead::where('id', $event->leadId)->with('customer', 'utility', 'utility.powerCompany', 'system.finance')->first();

        if ($lead->origin_office_id === 5) {
            $key = 'lfRGNSEUyzL5wiIvdECHAAIsdSpBAy6J';
        } else {
            $key = $this->getKey($lead);
        }
        if ($lead->system->system_size > 100 || $lead->system->system_size < 1) {
            $systemSize = $lead->system->system_size;
        } else {

            $watts = SolarModule::where('id', $lead->system->modules_id)->pluck('watts')
                ->first();
            $systemSize = ($watts * $lead->system->modules_count) / 1000;

        }

        if ($lead->origin_office_id === 5) {
            $salesRep = [
                'first_name' => 'Chris',
                'last_name' => 'Furman',
                'email' => 'chris@solving.solar',
                'phone' => '6199406423',
                'organization' => 'Solving Solar',
            ];
        } else {
            $salesRep = [
                'first_name' => 'Shane',
                'last_name' => 'Montana',
                'email' => 'shane@solarbrightwave.com',
                'phone' => '8564306685',
                'organization' => 'Solar Brightwave',
            ];
        }


        if (app()->environment('production')) {
            $url = 'https://heliotrack.completesolar.com/';

        } else {
            $url = 'https://heliotrack.completesolar.biz/';
            $key = env('complete_solar_test_key');

            $salesRep = [
                'first_name' => 'Chris',
                'last_name' => 'Furman',
                'email' => 'chris.furman@solcalenergy.com',
                'phone' => '6199406423',
                'organization' => 'chris.furman@solcalenergy.com'
            ];
        }
        if ($lead->siteSurveyQuestions->un_permitted_work == 'yes') {
            $unpermitted_work = true;
        } else {
            $unpermitted_work = false;
        }
        if ($lead->siteSurveyQuestions->alarms_working == 'yes') {
            $smoke_carbon_detectors = true;
        } else {
            $smoke_carbon_detectors = false;
        }
        if ($lead->siteSurveyQuestions->tree_removal == 'yes') {
            $tree_removal = true;
        } else {
            $tree_removal = false;
        }
        if ($lead->siteSurveyQuestions->access_issues == 'yes') {
            $access_issue = true;
        } else {
            $access_issue = false;
        }
        if ($lead->siteSurveyQuestions->access_issues_details) {
            $additional_access_issue_comment = $lead->siteSurveyQuestions->access_issues_details;
        } else {
            $additional_access_issue_comment = 'none';
        }
        if ($lead->siteSurveyQuestions->promises == 'yes') {
            $additional_promises = true;
        } else {
            $additional_promises = false;
        }
        if ($lead->siteSurveyQuestions->promises_details) {
            $additional_promises_description = $lead->siteSurveyQuestions->promises_details;
        } else {
            $additional_promises_description = 'none';
        }
        if ($lead->siteSurveyQuestions->hoa == 'yes') {
            $hoa = true;
        } else {
            $hoa = false;
        }


        $payload = [
            'external_source' => 'Scout',
            'external_id' => $event->leadId,
            'language' => ucfirst($lead->customer->language),
            'address' => [
                'street' => $lead->customer->street_address,
                'city' => $lead->customer->city,
                'state' => 'CA',
                'zip' => $lead->customer->zip_code,
            ],
            'utility' => ['uuid' => $lead->utility->powerCompany->epc_owner_id],
            'customer' => [
                'first_name' => $lead->customer->first_name,
                'last_name' => $lead->customer->last_name,
                'email' => $lead->customer->email,
                'phone' => $lead->customer->cell_phone
            ],
            'finance_product' => ['uuid' => $lead->system->finance->epc_owner_id],
            'solar_module' => ['uuid' => $lead->system->module->epc_owner_id],
            'inverter' => ['uuid' => $lead->system->inverter->epc_owner_id],
            'system_size' => $systemSize,
            'solar_module_count' => $lead->system->modules_count,
            'contract_date' => Carbon::now()->setTimezone('America/Los_Angeles')->toDateTimeString(),
            'sales_rep' => $salesRep,
            'contract_amount' => $lead->system->contract_amount,
            'down_payment' => 0,
            'lifetime_savings' => 0,
            'solar_production' => 0,
            'unpermitted_work' => $unpermitted_work,
            'smoke_carbon_detectors' => $smoke_carbon_detectors,
            'tree_removal' => $tree_removal,
            'access_issue' => $access_issue,
            'access_issue_comment' => $additional_access_issue_comment,
            'additional_promises' => $additional_promises,
            'additional_promises_description' => $additional_promises_description,
            'in_hoa' => $hoa,


        ];


        $client = new Client([
            'base_uri' => $url,
            'timeout' => 500.0,
        ]);
        //env('COMPLETE_API')
        try {
            $response = $client->request('POST', '/api/customers', [
                'headers' => [
                    'x-api-key' => $key
                ],
                'json' => $payload
            ]);
            $body = json_decode($response->getBody()->getContents());

            $lead->epc_owner_id = $body->uuid;
            $lead->save();
            Log::debug('response from complete: ' . json_encode([$body]));
            Log::debug('Lead updated from close: ' . json_encode([$lead]));
        } catch (ClientException|Exception $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
//            Log::info('response from complete: ' . json_encode([$body]));
            Log::alert('Error response from complete:  ' . $responseBodyAsString);
            Log::debug('Key: ' . $key, ['ueee']);
            TwilioSMSController::sendSms('6199406423', 'complete lead create failed lead id ' . $lead->id);
        }

        event(new LeadUpdateEvent($lead->getChanges(), $lead->id));


    }

    function getKey($lead)
    {
        $regionId = $lead->office()->first()->market_id;
        return Cache::remember('regionKey' . $regionId, 60000, function () use ($regionId) {
            return RegionAPI::where('region_id', $regionId)->where('type', 'complete-x-api-key')->first()->api_key;
        });
    }
}
