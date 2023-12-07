<?php

namespace App\Http\Controllers\Backend\Office;

use App\HelperController;
use App\Http\Controllers\Controller;
use App\Http\Resources\OfficeStanding as OfficeStandingResource;
use App\Mail\Office\OfficeStandingsMailable;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\Office\OfficeStanding;
use App\Models\OfficeStanding\OfficeStandingData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OfficeStandingController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$months = [];
//        if (isset($_GET['year'])) {
//            $currentYear = $_GET['year'];
//        } else {
//            $currentYear = now()->year;
//        }
		$dt1      = Carbon::now()->toDateTimeString();
		$dt2      = Carbon::now()->subYears(1)->toDateTimeString();
		$approved = OfficeStanding::whereBetween('sdate', [$dt2, $dt1])->where('approved', '=', true)
			->get();

		$approvedOfficeMonth = $approved->groupBy(function ($date) {
			return Carbon::parse($date->sdate)->format('m');
		});

		if (count($approvedOfficeMonth) != 0) {
			$approvedMonths = HelperController::indexMonth($approvedOfficeMonth);
		} else {
			$approvedMonths = [];
		}

		//not approved
		$notApproved = OfficeStanding::whereBetween('sdate', [$dt2, $dt1])->where('approved', '=', false)
			->get();

		$notApprovedOfficeMonth = $notApproved->groupBy(function ($date) {
			return Carbon::parse($date->sdate)->format('m');
		});
//        return $notApprovedOfficeMonth;

		if (count($notApprovedOfficeMonth) != 0) {
			$disApprovedMonths = HelperController::indexMonth($notApprovedOfficeMonth);
		} else {
			$disApprovedMonths = [];
		}

		return view('backend.office.standings.index', compact('approvedMonths', 'disApprovedMonths'));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$date = Carbon::today()->toDateString();
		return view('backend.office.standings.create', compact('date'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		if ($request->csv) {
			$header = NULL;
			$data   = array();
			if (($handle = fopen($request->csv, 'r')) !== FALSE) {
				while (($row = fgetcsv($handle, 0, ',')) !== FALSE) {

					if (!$header) {
						$header = $row;
					} else {
						if (count($header) != count($row)) {
							continue;
						}

						$csvs[] = array_combine($header, $row);
					}
				}
				fclose($handle);

			}

			$officeStanding          = new OfficeStanding();
			$officeStanding->name    = $request->name;
			$officeStanding->user_id = Auth::user()->id;
			$officeStanding->sdate   = $request->sdate;
			$officeStanding->save();

			foreach ($csvs as $csv) {
				$i = 0;
				foreach ($csv as $name => $data) {
					$officeData                     = new \App\Models\Office\OfficeStandingData();
					$officeData->name               = $name;
					$officeData->data               = $data;
					$officeData->office_standing_id = $officeStanding->id;

					if (Office::find($csv['office_id']) != null) {
						$officeData->office_id = $csv['office_id'];
					} else {
						\App\Models\Office\OfficeStandingData::where('office_standing_id', '=', $officeStanding->id)->delete();
						$officeStanding->delete();
						return redirect()->route('dashboard.officestanding.create')->withErrors('Invalid office, We do not have an office with an id of ' . $csv['office_id']);
					}

					$officeData->save();
				}
			}
		} else {
			return redirect()->route('dashboard.officestanding.create')->withErrors('You Must Attach a CSV to Upload');
		}
		return redirect()->route('dashboard.officestanding.index')->withFlashSuccess(__('Office Standing Created'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param OfficeStanding $officeStanding
	 * @return Response
	 */

	public function apiShow($month)
	{
		$start = Carbon::parse($month)->startOfMonth()->toDateString();
		$end   = Carbon::parse($month)->endOfMonth()->toDateString();
//       return $start;

		$links = OfficeStanding::whereBetween('sdate', [$start, $end])
			->where('approved', '=', 1)
			->with(['data.office'])
			->get();

//        $links = OfficeStanding::whereBetween(['', '']);
		return OfficeStandingResource::collection($links);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param OfficeStanding $officeStanding
	 * @return Response
	 */
	public function show($month, $year)
	{
		$payload = null;
		$start   = Carbon::createFromDate($year, $month, 1);
		$end     = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
		$start->toDateString();

//        TODO: Its not finding the data on the 1st and probably not at the end of the month
		$standingsGroup = OfficeStanding::whereBetween('sdate', [$start, $end])
			->where('approved', '=', 1)
			->with(['data.office'])
			->get();

		$standingsGroupArray = [];

		$headers = [];
		$fail    = 0;
		foreach ($standingsGroup as $standing) {
			$header = [];
			foreach ($standing->data as $data) {
				$officeId = $data->office_id;
				if ($data->name != 'office_id') {

					$standingsGroupArray[$standing->name][$officeId][$data->name]   = $data->data;
					$standingsGroupArray[$standing->name][$officeId]['Office Name'] = $data->office->name;

					if (!in_array($data->name, $header)) {
						array_push($header, $data->name);
					}
				} else {
					if (!in_array('Office Name', $header)) {
						array_push($header, 'Office Name');
					}
				}

				if ($data->office['name'] == 'Monthly Rank') {
					$fail = 1;
				}
				$temp[$standing->name] = $header;
			}
		}

		$field = 'Monthly Rank';
//        return $standingsGroupArray;
		foreach ($standingsGroupArray as $key => $standing) {
			$payload[$key] = HelperController::sortArray($standing, $field);
		}
//        if (!$fail) {
//            return redirect()->route('dashboard.officestanding.index')->withErrors('Something went wrong');
//        }
//        return $payload;
		foreach ($payload as $key => $value) {
			array_unshift($payload[$key], $temp[$key]);
		}

		$standingsGroupArray = $payload;

//        $headers = json_encode($headers);
		$standingsGroupArray = json_encode($standingsGroupArray);
		return view('backend.office.standings.show', compact('standingsGroupArray'));

	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param OfficeStanding $officeStanding
	 * @return Response
	 */
	public function edit(OfficeStanding $officeStanding)
	{
		//
	}

	public function review(Request $request, $month, $year)
	{
		if (!isset($year) && !isset($month)) {
			return back()->withErrors('You must give a year and month to view this');
		}

		$dt1    = Carbon::now()->toDateTimeString();
		$dt2    = Carbon::now()->subYears(1)->toDateTimeString();
		$office = OfficeStanding::whereBetween('sdate', [$dt2, $dt1])->where('approved', '=', true)
			->get();

		$officeMonth = $office->groupBy(function ($date) {
			return Carbon::parse($date->sdate)->format('m');
		});


//        $months = HelperController::indexMonth($officeMonth);

		$offices   = Office::all();
		$months    = HelperController::OfficeStandingMonth($year);
		$years     = HelperController::OfficeStandingYear();
		$approvals = HelperController::OfficeStandingMonthCarbon($year, $month);

//        return $month;
//        return $approvals;


//        dd($request);
//        return $months;

		return view('backend.office.standings.review', compact('approvals', 'offices', 'month', 'year'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function update(Request $request)
	{
		$item = null;
		foreach ($request->approved as $officeStandingId => $isConfirmed) {
			$office           = OfficeStanding::find($officeStandingId);
			$office->approved = $isConfirmed;
			$office->save();
		}

		$users                        = User::role(['administrator', 'user', 'manager', 'executive'])->get();
		$announcement['subject']      = 'See who the real king is!';
		$announcement['currentMonth'] = $request->currentMonth;

		if ($request->sendEmail) {
			foreach ($users as $user) {
				Mail::to($user->email)->queue(new OfficeStandingsMailable($announcement));
			}
			return redirect()->back()->with('sucess', 'Standing confirmed and mail sent!');
		}
		return redirect()->back()->with('sucess', 'Standing Confirmed');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @return Response
	 */
	public function destroy($review)
	{
//        return 'stuff';
		OfficeStanding::find($review)->delete();
		return redirect()->back()->with('success', 'Standing Deleted');
	}
}
