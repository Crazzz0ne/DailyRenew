<?php


namespace App\Repositories\Backend\SalesFlow\Lead;


use App\Events\Backend\SalesFlow\Lead\FailedCreditEvent;
use App\Events\Backend\SalesFlow\LeadEvent;
use App\Events\Backend\SalesFlow\LeadNewAppointment;
use App\Events\Backend\SalesFlow\Queue\ElevatorEvent;
use App\Events\Backend\SalesFlow\Queue\NewQueueEvent;
use App\Events\Backend\SalesFlow\Queue\UpdateEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\HelperController;
use App\Http\Resources\Lead\LineResource;
use App\Http\Resources\LeadsResource;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\Commission\CommissionLedgers;
use App\Models\Office\OfficeCommissions;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadLogin;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\LeadUpload;
use App\Models\SalesFlow\Lead\RequestedSystem;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\System;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Models\Transaction\LeadTransaction;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema as Schema;


class LeadRepository extends BaseRepository
{
    public function model()
    {
        return Lead::class;

    }

    public function firstOrMost($lead, $userId, $amount, $startOfDay)
    {
        $endOfDay = $startOfDay->copy()->endOfDay();
        Log::debug('Bonus check', ['amount' => $amount, 'lead' => $lead, 'Canvasser' => $userId]);

        $commissions = CommissionLedgers::where('type_id', 1)
            ->where('office_id', $lead->office_id)
            ->whereBetween('created_at', array($startOfDay->toDateTimeString(), $endOfDay->toDateTimeString()))
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->get();


        $i = 0;

        if (count($commissions) === 0) {
            CommissionLedgers::where('type_id', 2)
                ->where('office_id', $lead->office_id)
                ->whereBetween('created_at', array($startOfDay->toDateTimeString(), $endOfDay->toDateTimeString()))
                ->delete();
            return 'out';
        }
        if (count($commissions) === 1) {
            Log::debug('$commissions counts', ['userId' => $userId]);

            $currentKing = CommissionLedgers::where('type_id', 2)
                ->where('office_id', $lead->office_id)
                ->whereBetween('created_at', array($startOfDay->toDateTimeString(), $endOfDay->toDateTimeString()))
                ->first();

            if ($currentKing->user_id !== $commissions[0]->user_id) {
                $currentKing->delete();

                $newBonus = new CommissionLedgers();
                $newBonus->user_id = $commissions[0]->user_id;
                $newBonus->lead_id = $lead->id;
                $newBonus->type_id = 2;
                $newBonus->amount = $amount;
                $newBonus->office_id = $lead->office_id;
                $newBonus->created_at = $startOfDay->toDateTimeString();
                $newBonus->save();

            }
            Log::debug('$commissions counts', ['king' => $currentKing]);
            return 'done';

        } elseif (count($commissions) > 1) {
            foreach ($commissions as $commission) {
                if ($i === 0) {
                    $first = $commission->total;

                    if ($commissions[1]->total !== $first) {

                        $currentKing = CommissionLedgers::where('type_id', 2)
                            ->where('office_id', $lead->office_id)
                            ->whereBetween('created_at', array($startOfDay->toDateTimeString(), $endOfDay->toDateTimeString()))
                            ->first();

                        if ($currentKing->user_id !== $commissions[0]->user_id) {
                            $currentKing->delete();

                            $newBonus = new CommissionLedgers();
                            $newBonus->user_id = $commissions[0]->user_id;
                            $newBonus->lead_id = $lead->id;
                            $newBonus->type_id = 2;
                            $newBonus->amount = $amount;
                            $newBonus->office_id = $lead->office_id;
                            $newBonus->created_at = $startOfDay;
                            $newBonus->save();

                        }


                    }
                }


            }

            $i++;
        }


    }

    public function removeCommission($lead)
    {

        $canvasser = UserHasLead::where('lead_id', $lead->id)
            ->where('position_id', 1)
            ->first();
        $pastPay = CommissionLedgers::where('type_id', 1)
            ->where('user_id', $canvasser->user_id)
            ->where('lead_id', $lead->id)
            ->first();
//        Log::debug('Removing commission', ['past pay' =>$pastPay, 'lead' => $lead, 'Canvasser' => $canvasser]);

//$pastPay
        if ($pastPay !== null) {
            Log::debug('found pass pay');
            $commissionDate = CarbonImmutable::parse($pastPay->created_at);
            $recentPayrollDate = CarbonImmutable::now()->modify("last Thursday");


            if ($commissionDate->lessThan($recentPayrollDate)) {

                $commission = new CommissionLedgers();
                $commission->type_id = 1;
                $commission->lead_id = $lead->id;
                $commission->amount = -$pastPay->amount;
                $commission->office_id = $lead->office_id;
                $commission->user_id = $canvasser->user_id;
                $commission->save();

            } else {
                $pastPay->delete();
                $startOfDay = $commissionDate->startOfDay();
                $endOfDay = $commissionDate->endOfDay();

                $officeCommissionBonus = OfficeCommissions::where('office_id', $lead->office_id)
                    ->where('type_id', 2)
                    ->first();

                $this->firstOrMost($lead, $canvasser->user_id, $officeCommissionBonus->amount, $startOfDay);
            }


//            if ($pastBonus) {
//
//                $commissions = CommissionLedgers::where('type_id', 1)
//                    ->where('office_id', $lead->office_id)
//                    ->whereBetween('created_at', array($startOfDay, $endOfDay))
//                    ->select('user_id', DB::raw('count(*) as total'))
//                    ->groupBy('user_id')
//                    ->orderBy('total', 'desc')
//                    ->get();
//
//                $commissionsTotal = CommissionLedgers::where('type_id', 1)
//                    ->where('office_id', $lead->office_id)
//                    ->whereBetween('created_at', array($startOfDay, $endOfDay))
//                    ->where('user_id', $canvasser->user_id)
//                    ->get();
//
//
//                $officeCommissionBonus = OfficeCommissions::where('office_id', $lead->office_id)
//                    ->where('type_id', '=', 2)
//                    ->first();
//
//
//                $i = 0;
//                $userId = $canvasser->user_id;
//                $pastBonusPosition = $commissions->where('user_id', $userId);
//
//
////                if (count($commissionsTotal) === 0) {
////                    $pastBonus->delete();
////                } else {
////                    foreach ($pastBonusPosition as $c => $s) {
////                        foreach ($commissions as $commission) {
////
////                            if ($i === $c) {
////                                break;
////                            }
////                            if ($s->total < $commission->total) {
////
////
////                                $pastBonus->delete();
////                                $newBonus = new CommissionLedgers();
////                                $newBonus->user_id = $canvasser->id;
////                                $newBonus->lead_id = $lead->id;
////                                $newBonus->type_id = 2;
////                                $newBonus->amount = $officeCommissionBonus->amount;
////                                $newBonus->office_id = $lead->office_id;
////                                $newBonus->save();
////                            }
////                            $i++;
////                        }
////                    }
////                }
//            }
        }
    }


    public function callCenterCommission($lead, $apiUser, $type)
    {
        return 0;
//dump($type);
        $sp1 = UserHasLead::where('lead_id', $lead->id)
            ->where('position_id', 2)
            ->first();
//Removed sit pay
        if ($type == 3) {
            return 0;
        }

// filter out people tying to pay themselves
        if (
//            Make sure the user is makring their own pay
            ($sp1->id == $apiUser->id && $type == 1)
//Check to make sure they are not marking their own sits if they are sp2
            || ($sp1->id == $apiUser->id && $type == 3)
        ) {
            return 0;
        }

//        Check the position and assign user.
        $position = 0;
        if ($position == 1) {
            //need to prevent double pay.
            $commissionRep = UserHasLead::where('lead_id', $lead->id)
                ->where('position_id', 1)
                ->first();
            $commissionRep = $commissionRep->user_id;

        } else {
            $commissionRep = $sp1->user_id;
        }

        $pastCommission = CommissionLedgers::where('lead_id', $lead->id)
            ->where('type_id', $type)
            ->where('user_id', $commissionRep)
            ->count();

        if ($pastCommission == 0) {

            $officeCommissionRegular = OfficeCommissions::where('office_id', $lead->origin_office_id)
                ->where('type_id', $type)
                ->first();

            if ($position === 1) {
                $commissionPay = $officeCommissionRegular->amount - 5;
            } else {
                $commissionPay = $officeCommissionRegular->amount;
            }
            $commission = new CommissionLedgers();
            $commission->type_id = $type;
            $commission->lead_id = $lead->id;
            $commission->amount = $commissionPay;
            $commission->office_id = $lead->origin_office_id;
            $commission->user_id = $commissionRep;
            $commission->save();
        }

        return true;
    }

    public function canvasserCalc($lead)
    {
        $startOfDay = Carbon::now()->startOfDay()->timezone('America/los_angeles');
        $pastCommission = CommissionLedgers::where('lead_id', $lead->id)
            ->where('type_id', 1)
            ->first();
        if (!$pastCommission) {

            $canvasser = UserHasLead::where('lead_id', $lead->id)
                ->where('position_id', 1)
                ->first();

            $pastCommission = CommissionLedgers::where('created_at', '>', $startOfDay->toDateTimeString())
                ->where('user_id', $canvasser->user_id)
                ->where('type_id', 1)
                ->sum('amount');


            $bonus = CommissionLedgers::where('office_id', $lead->office_id)
                ->where('type_id', 2)
                ->where('created_at', '>', $startOfDay->toDateTimeString())
                ->get()
                ->count();

            $officeCommissionBonus = OfficeCommissions::where('office_id', $lead->office_id)
                ->where('type_id', '=', 2)
                ->first();

            $officeCommissionRegular = OfficeCommissions::where('office_id', $lead->office_id)
                ->where('type_id', '=', 1)
                ->first();

            if ($pastCommission != 0) {

                $regularCommissionPay = $pastCommission + 100;

            } else {
                if (Carbon::now()->isSunday() || $canvasser->user_id === 29) {
                    $commission = $officeCommissionRegular->amount * 2;
                } else {
                    $commission = $officeCommissionRegular->amount;
                }
                $regularCommissionPay = $commission;
            }

            $commission = new CommissionLedgers();
            $commission->type_id = 1;
            $commission->lead_id = $lead->id;
            $commission->amount = $regularCommissionPay;
            $commission->office_id = $lead->office_id;
            $commission->user_id = $canvasser->user_id;
            $commission->save();

//            if ($bonus === 0) {
//                $newBonus = new CommissionLedgers();
//                $newBonus->type_id = 2;
//                $newBonus->lead_id = $lead->id;
//                $newBonus->amount = $officeCommissionBonus->amount;
//                $newBonus->office_id = $lead->office_id;
//                $newBonus->user_id = $canvasser->user_id;
//                $newBonus->save();
//            } else {
//                $this->firstOrMost($lead, $canvasser->user_id, $officeCommissionBonus->amount, $startOfDay);
//
//            }

        }

    }

    public function formatAddress($customer)
    {
        $address = $customer->street_address . ' ' . $customer->city . ' ' . $customer->zip_code;
        return str_replace(' ', '+', $address);
    }

    public function travelTime($origin, $destination)
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->get('https://maps.googleapis.com/maps/api/directions/json?origin='
            . $origin . '&destination=' . $destination . '&key=AIzaSyAUyWQcN-DzrSx7-DU2nG1ppCfP378Hbi8');


        $response = $request->getBody();
        $payload = json_decode($response);


        return $payload->routes[0]->legs[0]->duration->text;

    }

    public function update($parameters, $lead)
    {

        $columns = Schema::getColumnListing('leads');

        $allowedKeys = array_diff_key($columns, ['created_at', 'updated_at']);

//Need to get this data in a better space for arrays to go up to avoid the updated at getting put in.
//        $data = array_diff_key($columns, $data);

        $filteredParameters = array_filter(
            $parameters,
            function ($key) use ($allowedKeys) {
                return in_array($key, $allowedKeys);
            },
            ARRAY_FILTER_USE_KEY
        );
        array_diff_ukey($filteredParameters, $lead, HelperController::key_compare_func);


        $lead->update($filteredParameters);

        $columns = array_keys($filteredParameters);
        $values = array_values($filteredParameters);

        foreach ($columns as $c) {
            $transaction['column'] = $c;
        }

        foreach ($values as $v) {
            $transaction['data'] = $v;
        }
        $transaction['lead_id'] = $lead->id;

        LeadTransaction::insert($transaction);

        return 'works';
    }


    public function csvPayrollCreditPass($startOfWeek, $endOfDay)
    {
        $dateRange = [$startOfWeek->toDateTimeLocalString(), $endOfDay->toDateTimeLocalString()];

        $rawData = LeadTransaction::whereBetween('created_at', $dateRange)
            ->where('attribute', '=', 'credit_status')
            ->where(function ($q) {
                $q->where('new_value', 'pass')
                    ->orWhere('new_value', 'manual');
            })
            ->with('lead.customer', 'lead.reps', 'lead.office')
            ->get();

        $payload = array();
        $i = 0;

        foreach ($rawData as $leadTrans) {

            if ($leadTrans->lead->credit_status == 'pass' || 'manual') {
                $notEligible = LeadTransaction::where('lead_id', '=', $leadTrans->lead->id)
                    ->where('created_at', '<', $startOfWeek->toDateTimeLocalString())
                    ->where('attribute', '=', 'credit_status')
                    ->where(function ($q) {
                        $q->where('new_value', 'pass')
                            ->orWhere('new_value', 'manual');
                    })
                    ->get();

                if (!$notEligible->isEmpty()) {
                    break;
                }
                $i++;
                $payload[] = $leadTrans->lead;

            }
        }
        return $payload;
    }

    public function csvPayrollClosed($startOfWeek, $endOfDay)
    {

        $dateRange = [$startOfWeek->toDateTimeLocalString(), $endOfDay->toDateTimeLocalString()];

        $rawData = LeadTransaction::whereBetween('created_at', $dateRange)
            ->where('attribute', '=', 'status')
            ->where(function ($q) {
                $q->where('new_value', 'close')
                    ->orWhere('new_value', 'installed');
            })
            ->with('lead.customer', 'lead.reps', 'lead.office')
            ->get();

        $payload = array();
        $i = 0;

        foreach ($rawData as $leadTrans) {
            $notEligible = LeadTransaction::where('lead_id', '=', $leadTrans->lead->id)
                ->where('created_at', '<', $startOfWeek->toDateTimeLocalString())
                ->where('attribute', '=', 'status')
                ->where(function ($q) {
                    $q->where('new_value', 'close')
                        ->orWhere('new_value', 'installed');
                })
                ->get();

            if (!$notEligible->isEmpty()) {
                break;
            }
            $i++;

            $payload[] = $leadTrans->lead;
        }
        return $payload;
    }

    public function csvPayRollOutPut($startofPayRange, $endOfPayRange)
    {
//        $endOfPayRange = Carbon::now()->hour(20)->minutes(00)->second(00);
//        $startofPayRange = Carbon::now()->hour(20)->minutes(01)->second(00)->subDays(7)->minutes(1);


        $payload['credit'] = $this->csvPayrollCreditPass($startofPayRange, $endOfPayRange);
        $payload['install'] = $this->csvPayrollClosed($startofPayRange, $endOfPayRange);

//return $payload;
        $columns = array('Type', 'Customer', 'Status', 'Opener', 'Sp1', 'Sp2', 'Date of Close', 'System Size', 'Price Per Watt', 'Office');


        $file = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
        fputcsv($file, $columns);
        foreach ($payload as $key => $leads) {

            foreach ($leads as $lead) {
                $opener = null;
                $sp1 = null;
                $closer = null;
                $close = null;

                foreach ($lead->reps as $rep) {
                    if ($rep->pivot->position_id == 1) {
                        $opener = $rep->full_name;
                    }
                    if ($rep->pivot->position_id == 2) {
                        $sp1 = $rep->full_name;
                    }
                    if ($rep->pivot->position_id == 3) {
                        $closer = $rep->full_name;
                    }
                }
//                    TODO: data is not changing in the lead controller.
                if ($lead->status = 'closed') {
                    $close = $lead->close_date;
                }

                fputcsv($file, array($key, $lead->customer->full_name, $lead->status, $opener, $sp1, $closer, $close,
                    $lead->system_size, $lead->customer_cost_per_watt, $lead->office->name));
            }
        }
        rewind($file);
        $output = stream_get_contents($file);
        return $output;
    }

    public function requestTextIntegrations($user, $queueId)
    {

        $integrations = User::permission('accept integrations')->get();

        $body = "New Lead please click the link below to set up an account for the customer. "
            . URL::to('/') . "/dashboard/lead/queue";

        foreach ($integrations as $integration) {
            if ($integration->phone_number) {

                event(new TextEvent($integration->phone_number, $body));

            }
        }

    }

    public function requestTextSp1($officeId, $body)
    {
        $sp1s = User::permission('accept sp1')->where('terminated', null)->where('office_id', $officeId)->get();

        foreach ($sp1s as $sp1) {
            if ($sp1->phone_number) {
                event(new TextEvent($sp1->phone_number, $body));
            }
        }

    }

    public function BuildCreditAppRequest($userId, $leadId)
    {


        $queue = new Line();
        $queue->requested_user_id = $userId;
        $queue->lead_id = $leadId;
        $queue->type = 'credit app';
        $queue->save();

        $builders = $this->proposalBuilderEmails();
        $subject = 'Creat credit app for sp1 in the home lead(' . $leadId . ')';
        $body = 'Can you please create and send a LA Solar credit APP?';
        $link = URL::to('/') . '/dashboard/lead/queue' . $queue->id;

        foreach ($builders as $builder) {
            Mail::to($builder)->queue(new BaseMailable($subject, $body, $link, 'lead'));

        }

    }

    public function CreditPassReport($startOfWeek, $endOfDay)
    {
        $dateRange = [$startOfWeek->toDateTimeLocalString(), $endOfDay->toDateTimeLocalString()];

        $rawData = LeadTransaction::whereBetween('created_at', $dateRange)
            ->where('attribute', '=', 'credit_status')
            ->where(function ($q) {
                $q->where('new_value', 'pass')
                    ->orWhere('new_value', 'manual');
            })
            ->with('lead.customer', 'lead.reps', 'lead.office')
            ->get();

        $payload = array();
        $i = 0;

        foreach ($rawData as $leadTrans) {

            if ($leadTrans->lead->credit_status == 'pass' || 'manual') {
                $notEligible = LeadTransaction::where('lead_id', '=', $leadTrans->lead->id)
                    ->where('created_at', '<', $startOfWeek->toDateTimeLocalString())
                    ->where('attribute', '=', 'credit_status')
                    ->where(function ($q) {
                        $q->where('new_value', 'pass')
                            ->orWhere('new_value', 'manual');
                    })
                    ->get();

                if (!$notEligible->isEmpty()) {
                    break;
                }
                $i++;
                $payload[] = $leadTrans->lead->id;

            }
        }
        return $payload;
    }

    public function textAlert($body, $officeId)
    {

        $sp1s = User::whereHas("positions", function ($q) {
            $q->where("position_id", "=", '2');
        })->where("office_id", "=", $officeId)
            ->get();

        foreach ($sp1s as $sp1) {
            if ($sp1->phone_number != null) {
                event(new TextEvent($sp1->phone_number, $body));
                \Log::info('SP1 text was sent');
            }
        }

        return 'Sms Sent';
    }

    public function leadStatus($leadId)
    {
        $lead = Lead::find($leadId);


        switch ($lead->epc) {
            case 'la solar':
                if ($lead->office->market->id === 2) {
                    switch ($lead->status) {
                        case 'new lead':
                            if ($lead->integrations_approved) {
                                $lead->status = 'pending credit approval';
                                $lead->save();
                            } else {
                                $lead->status = 'low usage';
                                $lead->save();
                            }
                            break;

                        case 'pending credit approval':
                            if ($lead->credit_status === 'Tier II' || $lead->credit_status === 'Tier I') {
                                $lead->status = 'submitted to proposal team';
                                $lead->save();
                                $this->startProposal($lead);
                            }
                            break;
                        case 'submitted to proposal team':
                            if ($lead->system->system_size && $lead->system->monthly_payment && $lead->system->solar_rate && $lead->system->offset
                                && $lead->system->system && $lead->system->external_proposal_id) {

                                if ($lead->salesPacket->quote && $lead->salesPacket->test_contract) {
                                    $lead->status = 'submitted to SR';
                                    $lead->save();

                                    $subject = 'Proposal is ready (' . $lead->id . ')';
                                    $body = 'Your proposal is ready for ' . $lead->customer->first_name . ' ' . $lead->customer->last_name;
                                    $link = URL::to('/') . "/dashboard/lead/" . $lead->id;

                                    $rep = UserHasLead::where('lead_id', '=', $lead->id)
                                        ->where('position_id', '=', 5)
                                        ->get()
                                        ->first();

                                    $this->email($subject, $body, $link, $rep);
                                }
                            }
                            break;
                        case 'sent for signatures':
                            if ($lead->salesPacket->solar_agreement_signed && $lead->salesPacket->cpuc_doc_signed
                                && $lead->salesPacket->credit_doc_signed && $lead->salesPacket->ach_doc_signed
                                && $lead->salesPacket->nem_doc_signed) {
                                $lead->status = 'close';
                                $lead->save();
                            }
                            break;
                        case 'close':
                            $siteSurvey = Appointment::where('lead_id', '=', $lead->id)
                                ->where('type_id', '=', 4)->get()->count();
                            if ($siteSurvey) {
                                $lead->status = 'site survey';
                                $lead->save();
                            }
                            break;
                        case 'SP sent for signatures':
                            if ($lead->salesPacket->site_plan) {
                                $lead->status = 'design approved';
                                $lead->save();
                            }
                            break;
                        case 'design approved';
                            $installDate = Appointment::where('lead_id', '=', $lead->id)
                                ->where('type_id', '=', 5)->get()->count();
                            if ($installDate && $lead->salesPacket->converted) {
                                $lead->status = 'pending install';
                                $lead->save();
                            }
                            break;
                        case 'pending install':
                            $install = Appointment::where('lead_id', '=', $lead->id)
                                ->where('type_id', '=', 5)
                                ->where('start_time', '<', Carbon::now()->toDateTimeString())
                                ->get()->count();
                            if ($install) {
                                $lead->status = 'installed';
                                $lead->save();
                            }
                            break;
                        case 'installed':
                            if ($lead->salesPacket->pto) {
                                $lead->status = 'PTO';
                                $lead->save();


                                $subject = $lead->customer->last_name . ' installed (' . $lead->id . ')';
                                $body = 'Congratulations ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ' Has installed';
                                $link = URL::to('/') . "/dashboard/lead/" . $lead->id;

                                $reps = UserHasLead::where('lead_id', '=', $lead->id)
                                    ->where('position_id', '=', 5)
                                    ->get();

                                foreach ($reps as $rep) {
                                    $this->email($subject, $body, $link, $rep);
                                }
                            }
                            break;
                        default:
                            break;
                    }
                    break;
                } else {
                    switch ($lead->status) {
                        case 'new lead':
                            if ($lead->integrations_approved) {
                                $lead->status = 'pending credit approval';
                                $lead->save();
                            } else if ($lead->integrations_approved === false) {
                                $lead->status = 'low usage';
                                $lead->save();
                            }

                            break;
                        case 'pending credit approval':
                            if ($lead->integrations_approved === false) {
                                $lead->status = 'low usage';
                                $lead->save();
                            }
                            break;
                        case 'submitted to proposal team':
                            if ($lead->system->system_size && $lead->system->monthly_payment && $lead->system->solar_rate && $lead->system->offset
                                && $lead->system->system && $lead->system->external_proposal_id) {

                                if ($lead->salesPacket->quote && $lead->salesPacket->test_contract) {
                                    $lead->status = 'submitted to SR';
                                    $lead->save();

                                    $subject = 'Proposal is ready (' . $lead->id . ')';
                                    $body = 'Your proposal is ready for ' . $lead->customer->first_name . ' ' . $lead->customer->last_name;
                                    $link = URL::to('/') . "/dashboard/lead/" . $lead->id;

                                    $rep = UserHasLead::where('lead_id', '=', $lead->id)
                                        ->where('position_id', '=', 5)
                                        ->get()
                                        ->first();

                                    $this->email($subject, $body, $link, $rep);
                                }
                            }
                            break;
                        case 'sent for signatures':
                            if ($lead->salesPacket->solar_agreement_signed && $lead->salesPacket->cpuc_doc_signed
                                && $lead->salesPacket->credit_doc_signed && $lead->salesPacket->ach_doc_signed
                                && $lead->salesPacket->nem_doc_signed) {

                                $lead->status = 'close';
                                $lead->save();

                                $subject = 'All docs signed (' . $lead->id . ')';
                                $body = 'Congratulations all of your documents have been signed and have been submitted for approval
                             for ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ' ';
                                $link = URL::to('/') . "/dashboard/lead/" . $lead->id;

                                $rep = UserHasLead::where('lead_id', '=', $lead->id)
                                    ->where('position_id', '=', 5)
                                    ->get()
                                    ->first();

                                $this->email($subject, $body, $link, $rep);
                            }
                            break;
                        case 'close':
                            $siteSurvey = Appointment::where('lead_id', '=', $lead->id)
                                ->where('type_id', '=', 4)->get()->count();
                            if ($siteSurvey) {
                                $lead->status = 'site survey';
                                $lead->save();
                            }
                            break;
                        case 'SP sent for signatures':
                            if ($lead->salesPacket->site_plan) {
                                $lead->status = 'design approved';
                                $lead->save();
                            }
                            break;
                        case 'design approved';
                            $installDate = Appointment::where('lead_id', '=', $lead->id)
                                ->where('type_id', '=', 5)->get()->count();
                            if ($installDate && $lead->salesPacket->converted) {
                                $lead->status = 'pending install';
                                $lead->save();
                            }
                            break;
                        case 'pending install':
                            $install = Appointment::where('lead_id', '=', $lead->id)
                                ->where('type_id', '=', 5)
                                ->where('start_time', '<', Carbon::now()->toDateTimeString())
                                ->get()->count();
                            if ($install) {
                                $lead->status = 'installed';
                                $lead->save();
                            }
                            break;
                        case 'installed':
                            if ($lead->salesPacket->pto) {
                                $lead->status = 'PTO';
                                $lead->save();

                                $subject = $lead->customer->last_name . ' installed (' . $lead->id . ')';
                                $body = 'Congratulations ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ' Has installed';
                                $link = URL::to('/') . "/dashboard/lead/" . $lead->id;

                                $rep = UserHasLead::where('lead_id', '=', $lead->id)
                                    ->where('position_id', '=', 5)
                                    ->get()
                                    ->first();

                                $this->email($subject, $body, $link, $rep);


                            }
                            break;
                        default:
                            break;
                    }
                }
                break;
            case 'Sunrun':
                switch ($lead->status) {
                    case 'new lead':
                        if ($lead->integrations_approved) {
                            $lead->status = 'pending credit approval';
                            $lead->save();
                        } else if ($lead->integrations_approved === false) {
                            $lead->status = 'low usage';
                            $lead->save();
                        }
                        break;
                    case 'pending credit approval':
                        if ($lead->credit_status === 'pass' || $lead->credit_status === 'manual') {
                            $lead->status = 'pending paperwork';
                            $lead->save();
                        } else if ($lead->credit_status === 'fail') {
                            return $this->createNewLead($lead);

                        }
                        if ($lead->integrations_approved === false) {
                            $lead->status = 'low usage';
                            $lead->save();
                        }
                        break;
                    case 'pending paperwork':
                        if ($lead->credit_status === 'fail') {
                            return $this->createNewLead($lead);
                        }
                        break;
                    case 'close':
                        $siteSurvey = Appointment::where('lead_id', '=', $lead->id)
                            ->where('type_id', '=', 4)->get()->count();
                        if ($siteSurvey) {
                            $lead->status = 'site survey';
                            $lead->save();
                        }
                        break;
                    case 'site survey':
                        $install = Appointment::where('lead_id', '=', $lead->id)
                            ->where('type_id', '=', 5)->get()->count();

                        if ($install) {
                            $lead->status = 'pending install';
                            $lead->save();
                        }
                        break;
                    case 'pending install':
                        $now = Carbon::now()->toDateTimeString();
                        $install = Appointment::where('lead_id', '=', $lead->id)
                            ->where('type_id', '=', 5)
                            ->where('start_time', '<', Carbon::now()->toDateTimeString())
                            ->get()->count();
                        if ($install) {
                            $lead->status = 'installed';
                            $lead->save();
                        }
                        break;

                    case 'installed':
                        if ($lead->salesPacket->pto) {
                            $lead->status = 'PTO';
                            $lead->save();


                            $subject = $lead->customer->last_name . ' installed (' . $lead->id . ')';
                            $body = 'Congratulations ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ' Has installed';
                            $link = URL::to('/') . "/dashboard/lead/" . $lead->id;

                            $reps = UserHasLead::where('lead_id', '=', $lead->id)
                                ->where('position_id', '=', 5)
                                ->get();

                            foreach ($reps as $rep) {
                                $this->email($subject, $body, $link, $rep);
                            }
                        }
                }
                break;
        }


    }

    public function startProposal($lead)
    {

        $sp1 = UserHasLead::where('lead_id', '=', $lead->id)
            ->where('position_id', '=', 2)
            ->with('user')
            ->first();
        if ($sp1)
            $body = 'Credit app has already been sent. Click the link to get started';
        $subject = '(' . $lead->id . ') Proposal Request';
        $queue = new Line();
        $queue->requested_user_id = $sp1->user_id;
        $queue->lead_id = $lead->id;
        $queue->type = 'Proposal Builder';
        $queue->save();

        $lead->save();
        $link = URL::to('/') . '/dashboard/lead/queue/';
        $reps = User::whereHas("positions", function ($q) use ($subject, $body) {
            $q->where("position_id", "=", 6);
        })
            ->get();

        foreach ($reps as $rep) {
            $this->email($subject, $body, $link, $rep);
        }
        event(new LeadEvent($lead));
    }

    public function showLead($lead)
    {

        if ($lead->editing) {
            return new LeadsResource(Lead::where('id', '=', $lead->id)
                ->with('reps', 'leadNote', 'user', 'appointments.user', 'userEdit', 'utility', 'proposal',
                    'system', 'salesPacket')
                ->first()
            );
        } else {
            return new LeadsResource(Lead::where('id', '=', $lead->id)
                ->with('reps', 'leadNote', 'user', 'appointments.user',
                    'utility', 'proposal', 'system', 'salesPacket')
                ->first());
        }
    }

    public function salesPacket($lead, $projectCoordinator)
    {
//        Sales Packet
        $sp = 0;
        $salesPacket = SalesPacket::where('id', '=', $lead->sales_packet_id)->first();
        $today = Carbon::now();


//sets the sales packet date of new contracts if its used. But it shouldnt be...
        if (!$salesPacket->test_contract && $projectCoordinator->test_contract) {
            $salesPacket->test_contract = $today;
        } elseif (!$projectCoordinator->test_contract) {
            $salesPacket->test_contract = null;
        }
        if (!$salesPacket->quote && $projectCoordinator->quote) {
            $salesPacket->quote = $today;
        } elseif (!$projectCoordinator->quote) {
            $salesPacket->quote = null;
        }
        if (!$salesPacket->solar_agreement_signed && $projectCoordinator->solar_agreement_signed) {
            $salesPacket->solar_agreement_signed = $today;
        } elseif (!$projectCoordinator->solar_agreement_signed) {
            $salesPacket->solar_agreement_signed = null;
        }
        if (!$salesPacket->credit_doc_signed && $projectCoordinator->credit_doc_signed) {
            $salesPacket->credit_doc_signed = $today;

            $sp++;
        } elseif (!$projectCoordinator->credit_doc_signed) {
            $salesPacket->credit_doc_signed = null;

        }
        if (!$salesPacket->nem_doc_signed && $projectCoordinator->nem_doc_signed) {
            $salesPacket->nem_doc_signed = $today;
            $sp++;
        } elseif (!$projectCoordinator->nem_doc_signed) {
            $salesPacket->nem_doc_signed = null;
        }
        if (!$salesPacket->cpuc_doc_signed && $projectCoordinator->cpuc_doc_signed) {
            $salesPacket->cpuc_doc_signed = $today;
            $sp++;
        } elseif (!$projectCoordinator->cpuc_doc_signed) {
            $salesPacket->cpuc_doc_signed = null;
        }
        if (!$salesPacket->ach_doc_signed && $projectCoordinator->ach_doc_signed) {
            $salesPacket->ach_doc_signed = $today;
            $sp++;
        } elseif (!$projectCoordinator->ach_doc_signed) {
            $salesPacket->ach_doc_signed = null;
        }
        if (!$salesPacket->proposal_doc_signed && $projectCoordinator->proposal_doc_signed) {
            $salesPacket->proposal_doc_signed = $today;
            $sp++;
        } elseif (!$projectCoordinator->proposal_doc_signed) {
            $salesPacket->proposal_doc_signed = null;
        }
        if (!$salesPacket->pto && $projectCoordinator->pto) {
            $salesPacket->pto = $today;
            $sp++;
        } elseif (!$projectCoordinator->pto) {
            $salesPacket->pto = null;
        }
        if (!$salesPacket->converted && $projectCoordinator->converted) {
            $salesPacket->converted = $today;
            $sp++;
        } elseif (!$projectCoordinator->converted) {
            $salesPacket->converted = null;
        }

        if (!$salesPacket->site_plan && $projectCoordinator->site_plan) {
            $salesPacket->site_plan = $today;
            $sp++;
        } elseif (!$projectCoordinator->site_plan) {
            $salesPacket->site_plan = null;
        }

        if (!empty($projectCoordinator->site_survey_date)) {
            $requestSiteSurveyDate = Carbon::parse($projectCoordinator->site_survey_date)->toDateTimeString();
            $end = Carbon::parse($projectCoordinator->site_survey_date)->addHours(3)->toDateTimeString();

            $currentAppointment = Appointment::where('lead_id', '=', $lead->id)
                ->where('type_id', '=', 4)
                ->first();

            if ($currentAppointment !== null) {
                $currentAppointment->start_time = $requestSiteSurveyDate;
                $currentAppointment->finish_time = $end;

                $currentAppointment->save();


            } else {
                $newAppointment = new Appointment();
                $newAppointment->lead_id = $lead->id;
                $newAppointment->subject = 'Site Survey @ ' . $lead->customer->first_name . ' ' . $lead->customer->last_name;
                $newAppointment->type_id = 4;
                $newAppointment->start_time = $requestSiteSurveyDate;
                $newAppointment->finish_time = $end;
                $newAppointment->user_id = 1;
                $newAppointment->save();
                $sp2Name = null;
                event(new LeadNewAppointment($newAppointment, $sp2Name));
            }
        }

        if (!empty($projectCoordinator->install_date->start_time)) {

            $currentAppointment = Appointment::where('lead_id', '=', $lead->id)
                ->where('type_id', '=', 5)
                ->get()
                ->first();

            $end = Carbon::parse($projectCoordinator->install_date['start_time'])->addHours(3)->toDateTimeString();
            $requestInstallDate = Carbon::parse($projectCoordinator->install_date['start_time'])->toDateTimeString();
            if ($currentAppointment !== null) {

                $currentAppointment->start_time = $requestInstallDate;
                $currentAppointment->finish_time = $end;
                $currentAppointment->save();

            } else {

                $newAppointment = new Appointment();
                $newAppointment->lead_id = $lead->id;
                $newAppointment->subject = 'Install @ ' . $lead->customer->first_name . ' ' . $lead->customer->last_name;
                $newAppointment->type_id = 5;
                $newAppointment->start_time = $requestInstallDate;
                $newAppointment->finish_time = $end;
                $newAppointment->user_id = 1;
                $newAppointment->save();
                $sp2Name = null;
                event(new LeadNewAppointment($newAppointment, $sp2Name));
            }

        }


        $salesPacket->site_survey_note = $projectCoordinator->site_survey_note;
        return $salesPacket->save();
    }

    public function createNewLead($lead)
    {

        $leads = Lead::where('customer_id', $lead->customer_id)->get()->count();
        if ($leads >= 2) {
            return null;
        }

//        Creates the lead items

        $newSalesPacket = new SalesPacket();
        $newSalesPacket->save();


        $newLead = new Lead();
        $newLead->status = 'Pending Credit Check';
        $newLead->epc_id = 2;
        $newLead->credit_status_id = 4;
        $newLead->customer_id = $lead->customer_id;
        $newLead->utility_id = $lead->utility_id;
        $newLead->integrations_approved = $lead->integrations_approved;
        $newLead->sales_packet_id = $newSalesPacket->id;
        $newLead->source = $lead->source;
        $newLead->office_id = $lead->office_id;
        $newLead->save();

        $newRequestedSystem = new RequestedSystem();
        $newRequestedSystem->lead_id = $newLead->id;
        $newRequestedSystem->save();


        $customer = Customer::where('id', '=', $lead->customer_id)
            ->get()
            ->first();

        $customer->save();


        $login = new LeadLogin();

        $login->lead_id = $newLead->id;
        $login->user_name = $lead->loginInfo->user_name;
        $login->password = $lead->loginInfo->password;

        $login->save();

        $note = new LeadNote();
        $note->note = 'New lead created for ' . $lead->customer->frist_name . ' due to failed credit.';
        $note->lead_id = $newLead->id;
        $note->user_id = 1;
        $note->save();

        $reps = UserHasLead::where('lead_id', '=', $lead->id)
            ->get();

        foreach ($reps as $rep) {

            $newLeadReps = new UserHasLead();
            $newLeadReps->lead_id = $newLead->id;
            $newLeadReps->user_id = $rep->user_id;
            $newLeadReps->position_id = $rep->position_id;
            $newLeadReps->save();

            $sp1Id = 1;
            if ($rep->position_id === 2) {
                $sp1Id = $rep->user_id;
            }

        }


        $pictures = LeadUpload::where('lead_id', '=', $lead->id)
            ->where(function ($query) {
                $query->where('type', '=', 'bill')
                    ->orWhere('type', '=', 'survey pictures');
            })
            ->get();

        foreach ($pictures as $picture) {
            $newBill = new LeadUpload();
            $newBill->path = $picture->path;
            $newBill->lead_id = $newLead->id;
            $newBill->type = $picture->type;
            $newBill->user_id = $picture->user_id;
            $newBill->size = $picture->size;
            $newBill->save();
        }

//        $appointments = Appointment::where('lead_id', '=', $lead->id)
//            ->where('type_id', 3)
//            ->orWhere('type_id', 6)
//            ->orWhere('type_id', 7)
//            ->get();
//
//        foreach ($appointments as $appointment) {
//            if ($appointment) {
//                $appointment->lead_id = $newLead->id;
//                $appointment->save();
//            }
//        }

//        $queue = new Line();
//        $queue->requested_user_id = $sp1Id;
//        $queue->lead_id = $newLead->id;
//        $queue->type = 'credit app';
//        $queue->save();
//
//        $lineResource = new LineResource($queue);
//
//        event(new ElevatorEvent($queue->type, 1));
//        event(new UpdateEvent($lineResource));
//        event(new NewQueueEvent($queue, 'assigned'));
//
//        $subject = 'Create credit app for sales rep lead(' . $newLead->id . ')';
//        $body = 'Can you please send a LA Solar credit application to the rep? ';
//
//        $proposalBuilders = User::whereHas("positions", function ($q) {
//            $q->where("position_id", "=", 6);
//        })->get();
//
//
//        $link = URL::to('/') . '/dashboard/lead/queue';
//
//        foreach ($proposalBuilders as $builder) {
//            Log::info('pb email', [$builder]);
//            $this->email($subject, $body, $link, $builder);
//        }

        $array = [
            'label' => 'La Solar',
            'value' => $newLead->id,
        ];

        event(new FailedCreditEvent($array, $lead->id));

        return $newLead;

    }

    public function email($subject, $body, $link, $rep)
    {
        Mail::to($rep->email)
            ->queue(new BaseMailable($subject, $body, $link, 'lead'));

        return 'Sms Sent';
    }

    public function sp1ListByOffice($officeId)
    {

        $sp1s = User::whereHas("positions", function ($q) {
            $q->where("position_id", "=", '2');
        })->get();
        $array = [];
        foreach ($sp1s as $sp1) {
            if ($sp1->office_id == $officeId && $sp1->phone_number != null) {
                array_push($array, $sp1->phone_number);
            }

        }
        return $array;
    }

    public function proposalBuilderEmails()
    {

        $builders = User::whereHas("positions", function ($q) {
            $q->where("position_id", "=", '6');
        })->get();
        $array = [];
        foreach ($builders as $builder) {
            if ($builder->phone_number) {
                array_push($array, $builder->email);
            }
        }
    }


}
