<?php


namespace App\Http\Controllers\Api\Payroll;


use App\Http\Controllers\Controller;
use App\Http\Resources\Commission\PayrollResource;
use App\Models\Auth\User;
use App\Models\Commission\CommissionLedgers;
use App\Models\Commission\Payroll;
use App\Models\Epc\EpcCreditStatus;
use App\Models\Office\Office;
use App\Models\Office\OfficeCommissions;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isEmpty;


class PayrollController extends Controller
{

    public function upload(Request $request)
    {

        $path = $request->file('file')->getPathname();


        $row = 1;
        $propStreamArray = array();
        $startDate = Carbon::parse($request->selectedDate);
//        dump('start date');
//        dump($startDate->toDateString());

        if (($handle = fopen($path, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000000, ",")) !== FALSE) {

                if ($row == 1) {
                    $row++;
                    continue;
                }
//                echo "<p> $num fields in line $row: <br /></p>\n";
                $row++;
                $date = Carbon::parse($data[22]);
                if (!$date->equalTo($startDate)) {
                    continue;
                }

                if ($data[18] == 'SALES PACKET' || $data[18] == 'INSTALL COMPLETE') {

                    $lead = Lead::whereHas('customer', function ($q) use ($data) {
                        $q->where('street_address', $data[1]);
                    })
                        ->where('origin_office_id', 10)
                        ->where('epc_owner_id', '!=', null)->with('reps')->first();

                    if (!$lead) {
                        continue;
                    }
//                    dump($lead->id . ' Lead ID from Sales Packet Or install');
//                    dump('customer Address '.$data[1]);
//                    dump($date->toDateString());

                    $opener = $lead->reps->filter(function ($value, $key) {
                        if ($value->pivot->position_id === 1) {
                            return true;
                        }
                    });
                    $opener = $opener->first();

                    $sp1 = $lead->reps->filter(function ($value, $key) {
                        if ($value->pivot->position_id === 2) {
                            return true;
                        }
                    });
                    $sp1 = $sp1->first();

                    if ($data[18] == 'SALES PACKET') {
                        $commissionType = 4;
                    } elseif ($data[18] === 'INSTALL COMPLETE') {
                        $commissionType = 5;
                    }
                    $officeCommissionRegular = OfficeCommissions::where('office_id', 10)
                        ->where('type_id', $commissionType)
                        ->first();


                    if ($opener) {
                        if ($sp1->id !== $opener->id) {
                            $commission = new CommissionLedgers();
                            $commission->type_id = $commissionType;
                            $commission->lead_id = $lead->id;
                            $commission->amount = $officeCommissionRegular->amount;
                            $commission->office_id = 10;
                            $commission->user_id = $opener->id;
                            $commission->save();
                        }
                    }

                    if ($data[18] == 'SALES PACKET') {
                        $commissionType = 6;
                    } elseif ($data[18] === 'INSTALL COMPLETE') {
                        $commissionType = 7;
                    }

                    $officeCommissionRegular = OfficeCommissions::where('office_id', 10)
                        ->where('type_id', 5)
                        ->first();
                    if ($sp1) {
                        if ($sp1->id) {
                            $commission = new CommissionLedgers();
                            $commission->type_id = $commissionType;
                            $commission->lead_id = $lead->id;
                            $commission->amount = $officeCommissionRegular->amount;
                            $commission->office_id = 10;
                            $commission->user_id = $sp1->id;
                            $commission->save();
                        }
                    }

                } elseif ($data[18] === 'OPEN/CANCELED') {
                    $lead = Lead::whereHas('customer', function ($q) use ($data) {
                        $q->where('street_address', $data[1]);
                    })->with('commissions', 'reps')->first();

                    if (!$lead) {
                        continue;
                    }
//                    Not sure what this did...?
//                    if (!$lead->commissions) {
//                        continue;
//                    }
                    dump($lead->id . ' Lead ID from OPEN/CANCELED');
                    dump($date->toDateString());
                    dump('customer Address ' . $data[1]);
                    $commissionsToRefund = $lead->commissions->filter(function ($value, $key) {
                        $commissionTypes = [4, 5];
                        if (in_array($value->type_id, $commissionTypes)) {
                            return true;
                        }
                    });
                    $commissionsToRefund = $commissionsToRefund->all();
                    if ($commissionsToRefund) {
                        foreach ($commissionsToRefund as $oldCommission) {
                            dump('refunding found ' . $lead->id);
                            if ($oldCommission->amount > 0) {
                                $commission = new CommissionLedgers();
                                $commission->type_id = $oldCommission->type_id;
                                $commission->lead_id = $lead->id;
                                $commission->amount = $oldCommission->amount * -1;
                                $commission->office_id = 10;
                                $commission->user_id = $oldCommission->user_id;
                                $commission->save();
                            }
                        }
                    } else {
                        dump('refunding not found ' . $lead->id);
                        $opener = $lead->reps->filter(function ($value, $key) {
                            if ($value->pivot->position_id === 1) {
                                return true;
                            }
                        });
                        $opener = $opener->first();

                        $sp1 = $lead->reps->filter(function ($value, $key) {
                            if ($value->pivot->position_id === 2) {
                                return true;
                            }
                        });
                        $sp1 = $sp1->first();

                        $officeCommission4 = OfficeCommissions::where('office_id', 10)
                            ->where('type_id', 4)
                            ->first();

                        $officeCommission6 = OfficeCommissions::where('office_id', 10)
                            ->where('type_id', 6)
                            ->first();

                        if ($opener) {
                            $commission = new CommissionLedgers();
                            $commission->type_id = 4;
                            $commission->lead_id = $lead->id;
                            $commission->amount = $officeCommission4->amount * -1;
                            $commission->office_id = 10;
                            $commission->user_id = $opener->id;
                            $commission->save();
                        }


                        if ($sp1) {
                            $commission = new CommissionLedgers();
                            $commission->type_id = 6;
                            $commission->lead_id = $lead->id;
                            $commission->amount = $officeCommission6->amount * -1;
                            $commission->office_id = 10;
                            $commission->user_id = $sp1->id;
                            $commission->save();
                        }
                    }


                }
            }

            fclose($handle);
        }

        return 'nope';
    }

    public function show(Request $request, Payroll $payroll)
    {
        $apiUser = Auth::user();

        $payroll = $payroll->where('id', $payroll->id)->with('User')->first();
        if ($apiUser->can('administrate company') || $apiUser->can('administrate payroll')) {
            return new PayrollResource($payroll);
        }

        if ($apiUser->can('administrate office') && $apiUser->office_id === $payroll->user->office_id) {
            return new PayrollResource($payroll);
        }

        if ($apiUser->id === $payroll->user->id) {
            return new PayrollResource($payroll);
        }


    }

    public function office(Request $request, Office $office)
    {
        $apiUser = Auth::user();

        $payroll = Payroll::whereHas('User', function ($q) use ($office) {
            $q->where('office_id', $office->id);
        })->with('User')
            ->orderBy('id', 'desc')
            ->paginate(100);

        if ($apiUser->can('administrate company') || $apiUser->can('administrate payroll')) {
            return PayrollResource::collection($payroll);
        }

        if ($apiUser->can('administrate office') && $apiUser->office_id === $office->id) {
            return PayrollResource::collection($payroll);
        }


    }

    public function user(Request $request, User $user)
    {
        $apiUser = Auth::user();
        $payroll = Payroll::where('user_id', $user->id)->paginate(10);

        $pagination = PayrollResource::collection($payroll);

        if ($apiUser->can('administrate company') || $apiUser->can('administrate payroll')) {

            return $pagination;
        }

        if ($apiUser->can('administrate office')) {
            return $pagination;
        }

        if ($apiUser->id === $user->id) {
            return $pagination;
        }


    }

    public function destroy(Lead $lead, Request $request)
    {

    }

    public function preview(Request $request)
    {
        $searchDay = 'Thursday';
        $searchDate = new Carbon(); //or whatever Carbon instance you're using
        //TODO: Remove Hardcoded timezone?
        $lastThursday = Carbon::createFromTimeStamp(strtotime("last $searchDay", $searchDate->timestamp), 'America/los_angeles')->toDateString();
//$start        Carbon::now()->toDateTimeString();
//      $end = Carbon::now()->day
        if ($request->userId) {
            $commissions = CommissionLedgers::whereDate('created_at', '>', $lastThursday)
                ->where('user_id', $request->userId)
                ->get();
        } else {
             $commissions = CommissionLedgers::whereDate('created_at', '>', $lastThursday)
                ->where('office_id', $request->officeId)
                ->get();
        }


        $payload =
            [
                'amount' => $commissions->sum('amount'),
                'user' => ['fullName' => 'No User Data']
            ];
        return $payload;

    }

    public function creditPassAudit(Lead $lead)
    {


        $audit = $lead->audits()->latest()->get();
        $auditArray = [];
        $epcCreditStatus = EpcCreditStatus::where('epc_id', 1)->get();
        $creditStatusArray = [];
        foreach ($epcCreditStatus as $status) {
            $creditStatusArray[$status->id] = $status->name;
        }

        $tempAudit = [];
        foreach ($audit as $value) {


            $tempArray = array_keys($value['old_values']);
            foreach ($tempArray as $temp) {
                if ($temp === 'credit_status_id') {
                    $user_id = $value['user_id'];
//                      $value->old_values['credit_status_id'];
                    $old_value = $value->old_values['credit_status_id'];
                    $new_value = $value->new_values['credit_status_id'];
                    $dateTime = $value->created_at;

                    $tempAudit = [
                        'user' => $user = User::find($user_id)->full_name,
                        'oldStatus' => $creditStatusArray[$old_value],
                        'newStatus' => $creditStatusArray[$new_value],
                        'time' => $dateTime

                    ];
                    array_push($auditArray, $tempAudit);
                }


            }
            $tempAudit = [];


        }

        return $auditArray;
    }

    public function cellPhoneAudit(Customer $customer)
    {


        $audit = $customer->audits()->latest()->get();
        $auditArray = [];

        $phoneNumberArray = [];


        $tempAudit = [];
        foreach ($audit as $value) {


            $tempArray = array_keys($value['old_values']);
            foreach ($tempArray as $temp) {

                if ($temp === 'cell_phone') {
                    $user_id = $value['user_id'];
//                      $value->old_values['credit_status_id'];
//                    $old_value = $value->old_values['cell_phone'];
//                    $new_value = ;
//                    $dateTime = $value->created_at;

                    $tempAudit = [
                        'user' => $user = User::find($user_id)->full_name,
                        'oldStatus' => $value->old_values['cell_phone'],
                        'newStatus' => $value->new_values['cell_phone'],
                        'time' => $value->created_at

                    ];


                    array_push($auditArray, $tempAudit);
                }


            }
            $tempAudit = [];


        }

        return $auditArray;
    }

    public function previewUserPayroll($userId)
    {


//        return $payload;
    }


}
