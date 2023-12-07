<?php


namespace App\Http\Controllers\Api\Commission;


use App\Http\Controllers\Controller;
use App\Http\Requests\CommissionUpdateRequest;
use App\Http\Resources\Commission\CommissionResource;
use App\Http\Resources\Commission\CommissionTypesResource;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\Commission\CommissionLedgers;
use App\Models\Commission\CommissionTypes;
use App\Models\Commission\Payroll;
use App\Models\Office\Office;
use App\Models\Office\OfficeCommissions;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use function MongoDB\BSON\toJSON;


class CommissionController extends Controller
{

    public function base()
    {
        return view('backend.commission.vue');
    }

    public function index()
    {
        return Payroll::all();
        $startOfDay = Carbon::now()->startOfDay();

        $lead = Lead::where('id', 12)->first();

        $canvasser = UserHasLead::where('lead_id', $lead->id)
            ->where('position_id', 1)
            ->first();

        $pastBonus = CommissionLedgers::where('type_id', 2)
            ->where('user_id', '!=', $canvasser->id)
            ->where('created_at', '>', $startOfDay)
            ->first();


        if ($pastBonus) {
            $commissions = CommissionLedgers::where('type_id', 1)
                ->where('office_id', $lead->office_id)
                ->where('created_at', '>', $startOfDay)
                ->select('user_id', DB::raw('count(*) as total'))
                ->groupBy('user_id')
                ->orderBy('total', 'desc')
                ->get();
            $i = 0;

            $userId = $canvasser->user_id;
            $pastBonusPosition = $commissions->where('user_id', 1);

            foreach ($pastBonusPosition as $c => $s) {
                foreach ($commissions as $commission) {

                    if ($i === $c) {
                        break;
                    }
                    if ($s->total < $commission->total) {
                        $officeCommissionBonus = OfficeCommissions::where('office_id', $lead->office_id)
                            ->where('type_id', '=', 2)
                            ->first();

                        $pastBonus->delete();

                        $newBonus = new CommissionLedgers();
                        $newBonus->user_id = $canvasser->user_id;
                        $newBonus->lead_id = $lead->id;
                        $newBonus->type_id = 2;
                        $newBonus->amount = $officeCommissionBonus->amount;
                        $newBonus->office_id = $lead->office_id;
                        $newBonus->save();

                    }

                    $i++;
                }
            }


        }


    }

    public function store(Request $request)
    {
        $user = \Auth::user();
        //check if they can do it;
        if ($user->can('administrate company') ||
            $user->can('administrate payroll') ||
            $user->can('administrate office')) {

            $officeId = User::where('id', $request->userId)->first();

            $commission = new CommissionLedgers();
            $commission->type_id = $request->typeId;
            $commission->user_id = $request->userId;
            $commission->lead_id = $request->leadId;
            $commission->amount = $request->amount;
            $commission->office_id = $officeId->office_id;
            $commission->manual = $user->id;
            $commission->save();

            $commission = CommissionLedgers::where('id', $commission->id)->with('user')->first();
            return new CommissionResource($commission);
        } else {
            Mail::to('chris.furman@solcalenergy.com')
                ->queue(new BaseMailable('Someone tried to hack payroll', 'hacker! Uid ' . $user->id, 'nowhere', 'lead'));
        }


    }

    public function update(CommissionUpdateRequest $request, CommissionLedgers $commission)
    {
        $user = \Auth::user();
        $amount = $request->input('amount');
        if ($user->can('edit commission')) {
            try {
                return $commission->update(['amount' => $amount]);
            } catch (\Exception $e) {
                \Log::error("Error updating Commission ID: {$commission->id}");
                return 0;
            }
        }
    }

    public function repTotal(Request $request, User $user)
    {

        $ytd = Carbon::now()->startOfYear();

        $mtd = Carbon::now()->startOfMonth();

        $wtd = Carbon::now()->startOfWeek(Carbon::THURSDAY);

        $dtd = Carbon::now()->startOfDay();

        $ytdTotal = CommissionLedgers::where('user_id', $user->id)
            ->where('created_at', '>', $ytd->toDateTimeString())
            ->sum('amount');

        $mtdTotal = CommissionLedgers::where('user_id', $user->id)
            ->where('created_at', '>', $mtd->toDateTimeString())
            ->sum('amount');

        $wtdTotal = CommissionLedgers::where('user_id', $user->id)
            ->where('created_at', '>', $wtd->toDateTimeString())
            ->sum('amount');

        $dtdTotal = CommissionLedgers::where('user_id', $user->id)
            ->where('created_at', '>', $dtd->toDateTimeString())
            ->sum('amount');

        $data = ['data' => [
            'ytd' => $ytdTotal,
            'mtd' => $mtdTotal,
            'wtd' => $wtdTotal,
            'dtd' => $dtdTotal]
        ];
        $payload = collect($data);
        return $payload;
    }

    public function repTransaction(Request $request, User $user)
    {
        $payload = CommissionLedgers::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return CommissionResource::collection($payload);

    }

    public function approve(Request $request, CommissionLedgers $commissionLedgers)
    {

        $apiKey = substr($request->header('Authorization'), -80);
        $user = User::where('api_token', $apiKey)
            ->first();

        if ($request->approved) {
            $commissionLedgers->approved = $user->id;

        } else {
            $commissionLedgers->approved = null;
        }
        $commissionLedgers->save();
        return $commissionLedgers;
    }

    public function officeTransaction(Request $request)
    {

        $commissionLedger = CommissionLedgers::query();

        if (\Auth::user()->pay_rate_id) {
            $payRate = \Auth::user()->pay_rate_id;
            $commissionLedger->whereHas('user', function ($q) use ($payRate) {
                $q->where('pay_rate_id', $payRate);
            });
        }

        if ($request->user_id) {
            $commissionLedger->where('user_id', $request->user_id);
        } else if ($request->office_id) {
            $commissionLedger->where('office_id', $request->office_id);
        } elseif ($request->market_id) {
            $regionId = $request->market_id;
            $commissionLedger->whereHas('office', function ($q) use ($regionId) {
                $q->where('market_id', $regionId);
            });
        }
        if ($request->user_id) {
            $commissionLedger->where('user_id', $request->user_id);
        } else if ($request->office_id) {
            $commissionLedger->where('office_id', $request->office_id);
        } else {
            $marketId = $request->market_id;
            $commissionLedger->wherehas('office', function ($q) use ($marketId) {
                $q->where('market_id', $marketId);
            });
        }
        $payload = $commissionLedger->with('user')
            ->orderBy('id', 'desc')
            ->paginate(100);

        return CommissionResource::collection($payload);

    }

    public function commissionTypes()
    {
        $commissionTypes = CommissionTypes::all();

        $commissionTypes->pluck('name', 'id');
        return CommissionTypesResource::collection($commissionTypes);
    }

    public function destroy(CommissionLedgers $commissionLedgers)
    {
        try {
            return $commissionLedgers->delete();
        } catch (\Exception $e) {
            \Log::error("Unable to delete commission ledger!", [$commissionLedgers->toArray()]);
            return 0;
        }
    }
}
