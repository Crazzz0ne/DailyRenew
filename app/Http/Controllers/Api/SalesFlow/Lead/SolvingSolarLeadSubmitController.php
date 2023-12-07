<?php

namespace App\Http\Controllers\Api\SalesFlow\Lead;

use App\Events\Backend\SalesFlow\Lead\Closed\CompleteClosedEvent;
use App\Events\Backend\SalesFlow\Lead\CustomerSatEvent;
use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Events\Backend\SalesFlow\Lead\ProposedSystemEvent;
use App\Events\Backend\SalesFlow\Lead\SalesPacketEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Http\Controllers\Controller;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\Epc\SolarModule;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadStatus;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\System\System;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SolvingSolarLeadSubmitController extends Controller
{

    public function post(Lead $lead, $id, Request $request)
    {

        $user = \Auth::user();

        try {
            $proposedSystem = ProposedSystem::where('id', $id)->first();
            $proposedSystem->rep_design_approved = $user->id;
            $proposedSystem->save();
        } catch (\Exception $exception) {
            Log::error('Proposed System Failed', [request()->fullUrl()]);
            $proposedSystem = ProposedSystem::where('id', $lead->proposedSystems[0]->id)->first();
            $proposedSystem->rep_design_approved = $user->id;
            $proposedSystem->save();
        }

        $lead->close_date = Carbon::now()->timezone('America/los_angeles')->toDateString();
        $lead->save();

        $something = $proposedSystem->getChanges();
        if (count($something) > 0) {
//        I need the ID for vue to match on the page
            $something['id'] = $proposedSystem->id;
            $data = collect($something);
//        lets everyone else know of the changes
            event(new ProposedSystemEvent($data, $lead->id));
        }


        if (($proposedSystem->modules_count && $proposedSystem->modules_id) && $proposedSystem->system_size === null) {
            $watts = SolarModule::where('id', $proposedSystem->modules_id)->pluck('watts')
                ->first();
            $systemSize = ($watts * $proposedSystem->modules_count) / 1000;
            $proposedSystem->system_size = $systemSize;
            $proposedSystem->save();
        }

        $system = new System();
        $system->inverter_id = $proposedSystem->inverter_id;
        $system->modules_count = $proposedSystem->modules_count;
        $system->modules_id = $proposedSystem->modules_id;
        $system->epc_finance_id = $proposedSystem->epc_finance_id;
        $system->system_size = $proposedSystem->system_size;
        $system->monthly_payment = $proposedSystem->monthly_payment ?? 0;
        $system->contract_amount = $proposedSystem->contract_amount;
        $system->yearly_production = $proposedSystem->yearly_production;
        $system->solar_rate = $proposedSystem->solar_rate;
        $system->offset = $proposedSystem->offset;
        $system->adders = $proposedSystem->adders;
        $system->roof_work = $proposedSystem->roof_work;
        $system->external_proposal_id = $proposedSystem->external_proposal_id;
        $system->ppw = $proposedSystem->ppw;
        $system->save();


        ProposedSystem::where('lead_id', '=', $lead->id)
            ->delete();

        RequestedSystem::where('lead_id', '=', $lead->id)
            ->delete();


        $lead->status_id = 7;


        $lead->system_id = $system->id;
        $lead->save();
        event(new CompleteClosedEvent($lead->id, $lead));

        $salesPacket = SalesPacket::where('id', $lead->sales_packet_id)->first();

        $now = Carbon::now()->toDateTimeString();

        $salesPacket->solar_agreement_signed = $now;
        $salesPacket->cpuc_doc_signed = $now;
        $salesPacket->sat = true;
        $salesPacket->save();
        event(new CustomerSatEvent($lead, $user));

        $salesPacketData = $salesPacket->getChanges();

        if (count($salesPacketData) > 0) {
//        I need the ID for vue to match on the page
            $something['id'] = $salesPacket->id;
            $data = collect($salesPacketData);
//        lets everyone else know of the changes
            event(new SalesPacketEvent($data, $lead->id));
        }
        $leadChanges = $lead->getChanges();
        if (count($leadChanges) > 0) {
//        I need the ID for vue to match on the page
            if (isset($leadChanges['status_id'])) {
                $statusName = LeadStatus::where('id', $leadChanges['status_id'])->first();
                $leadChanges['status'] = $statusName->name;
            }
//        lets everyone else know of the changes
            event(new LeadUpdateEvent($leadChanges, $lead->id));
        }

        $size = $lead->system->system_size;
        $ppw = $lead->system->ppw;
        $body = $lead->customer->first_name . ' ' . $lead->customer->last_name . ' CLOSED by ' . $user->first_name . ' ' . $user->last_name . ' 🤑💸
            Size: ' . $size . '
            PPW: $' . $ppw;

        event(new TextEvent('7609830713', $body));
        event(new TextEvent('6199406423', $body));

        return $system;


    }


}
