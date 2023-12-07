<?php

namespace App\Console\Commands;

use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BringInServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:bringInServer';

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
        $json = Storage::disk('local')->get('serverMove.json');
        $leads = json_decode($json, true);

        $financeMap = $this->getJsonData('finance-map.json');
        $powerCompanyMap = $this->getJsonData('power-company-map.json');
        $solarModulesMap = $this->getJsonData('solar-modules-map.json');
        $inverterMap = $this->getJsonData('inverter-map.json');

        foreach ($leads as $lead) {
            $utility = $this->createLeadUtility($lead['utility'], $powerCompanyMap);
            $salesPacket = $this->createSalesPacket($lead['sales_packet']);
            $customer = $this->createCustomer($lead['customer']);
            $newLead = $this->createLead($lead, $utility->id, $customer->id, $salesPacket->id);

            if (count($lead['proposal'])){
                foreach ($lead['proposal'] as $proposal){
               $this->createProposal($proposal, $newLead->id, $financeMap, $solarModulesMap, $inverterMap);
$this->info('requested system made');
                }
            }
            if (count($lead['proposed_systems'])) {
                $this->info('proposed system made');
                foreach ($lead['proposed_systems'] as $proposal) {
                     $this->createProposedSystems($proposal, $newLead->id, $financeMap, $solarModulesMap, $inverterMap);
                }
            }


        }

        $this->info($customer);
    }

    private function createProposedSystems($proposal, $id, $financeMap, $solarModulesMap, $inverterMap)
    {

    }

    private function createLeadUtility($utilityData, $powerCompanyMap)
    {
        $validUtilityData = $this->filterUtilityData($utilityData);
        $utility = new LeadUtility($validUtilityData);

        if ($utilityData['power_company_id'] && isset($powerCompanyMap[$utilityData['power_company_id']])) {
            $utility->power_company_id = $powerCompanyMap[$utilityData['power_company_id']];
        }else{
            $utility->power_company_id = null;
        }
        if (!$utilityData['power_discount_id'] && !isset($powerCompanyMap[$utilityData['power_discount_id']])) {
            $utility->power_discount_id = 1;
        }

        $utility->save();

        return $utility;
    }

    private function createSalesPacket($salesPacketData)
    {
        $validSalesPacketData = $this->filterSalesPacketData($salesPacketData);
        $salesPacket = new SalesPacket($validSalesPacketData);
        $salesPacket->save();

        return $salesPacket;
    }

    private function createCustomer($customerData)
    {
        $validCustomerData = $this->filterCustomerData($customerData);
        $customer = Customer::create($validCustomerData);

        return $customer;
    }

    private function createLead($leadData, $utilityId, $customerId, $salesPacketId)
    {
        $validLeadData = $this->filterLeadData($leadData);
        $newLead = new Lead($validLeadData);
        $newLead->utility_id = $utilityId;
        $newLead->office_id = 5;
        $newLead->origin_office_id = 5;
        $newLead->customer_id = $customerId;
        $newLead->sales_packet_id = $salesPacketId;
        $newLead->save();

        return $newLead;
    }

    private function createProposal($proposal, $id, $financeMap, $solarModulesMap, $inverterMap)
    {

        $proposal['lead_id'] = $id;

        if ($proposal['epc_finance_id'] && isset($financeMap[$proposal['epc_finance_id']])){
            $proposal['epc_finance_id'] = $financeMap[$proposal['epc_finance_id']];
        }else{
            $proposal['epc_finance_id'] = 1;
        }
        if ($proposal['modules_id'] && isset($solarModulesMap[$proposal['modules_id']])){
            $proposal['modules_id'] = $solarModulesMap[$proposal['modules_id']];
        }else{
            $proposal['modules_id'] = 0;
        }
        if ($proposal['inverter_id'] && isset($inverterMap[$proposal['inverter_id']])){
            $proposal['inverter_id'] = $inverterMap[$proposal['inverter_id']];
        }else{
            $proposal['inverter_id'] = 0;
        }
        $proposal['adders'] = [];

        $validProposalData = $this->filterProposalData($proposal);
        $newProposal = new RequestedSystem($validProposalData);
        $newProposal->save();

        return $newProposal;
    }


    private function filterSalesPacketData($data){
        $allowedFields = [

            'ach_doc_signed',
            'converted',
            'credit_doc_signed',
            'solar_agreement_signed',
            'proposal_doc_signed',
            'nem_doc_signed',
            'cpuc_doc_signed',
            'site_survey_note',
            'site_plan',
            'pto',
            'deleted_at',
            'created_at',
            'updated_at',
            'submitted',
            'sat',
            'submitted_for_permitting_date',
            'permitting_received_date',
            'design_plan_sent_date',
            'twilio_number',
        ];

        return array_only($data, $allowedFields);
    }

    private function getJsonData($fileName)
    {
        $path = Storage::disk('local')->path($fileName);
        return json_decode(file_get_contents($path), true);
    }

    private function filterCustomerData($customerData)
    {
        $allowedFields = [

            'first_name',
            'last_name',
            'spouse_name',
            'street_address',
            'city',
            'state',
            'zip_code',
            'home_phone',
            'cell_phone',
            'email',
            'language',
            'deleted_at',
            'dob',
            'last_four',
            'lat',
            'lng',
            'sales_force_url',

        ];

        return array_only($customerData, $allowedFields);
    }

    private function filterUtilityData($customerData)
    {
        $allowedFields = [
            'kw_year_usage',
            'power_company',
            'rate_plan',
            'power_discount_plan',
            'average_bill',
            'name_on_bill',
            'deleted_at',
            'power_company_id',
            'power_discount_id',
            'power_program_id'
        ];

        return array_only($customerData, $allowedFields);
    }

    private function filterLeadData($leadData)
    {
        $allowedFields = [
            'status_id',
            'jeopardy_id',
            'epc_id',
            'epc_lead_id',
            'proposed_system_id',
            'system_id',
            'sales_packet_id',

            'source',
            'customer_id',
            'credit_status_id',
            'integrations_approved',
            'close_date',
            'deleted_at',
            'epc_owner_id',
            'intake_id',
            'stale'
        ];

        return array_only($leadData, $allowedFields);
    }


    private function filterProposalData($proposal)
    {
        $allowedFields = [
            'lead_id',
            'epc_finance_id',
            'epc_system_id',
            'inverter_id',
            'modules_count',
            'modules_id',
            'system_size',
            'monthly_payment',
            'adders',
            'needed_by',
            'system',
            'offset',
            'solar_rate',
            'deleted_at',
            'created_at',
            'updated_at',
            'ppw',
            'roof_work',
            'approved',
            'sales_rep_note',
            'pb_note'
        ];

        return array_only($proposal, $allowedFields);
    }


}
