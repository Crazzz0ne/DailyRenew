<?php

namespace App\Http\Controllers\Backend\Office;

use App\Http\Controllers\Controller;
use App\Models\Office\ManagerEfficiency;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ManagerEfficiencyController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
//        This will have a problem with multiple years
		$months = [];

		$dt1      = Carbon::now()->toDateTimeString();
		$dt2      = Carbon::now()->subYears(1)->toDateTimeString();
		$managerE = ManagerEfficiency::whereBetween('created_at', [$dt2, $dt1])
			->get();

		$managerEMonth = $managerE->groupBy(function ($date) {
			return Carbon::parse($date->created_at)->format('m');
		});

		$managerEMonth;

		foreach ($managerEMonth as $key => $month) {
			$year = explode('-', $month[0]->created_at);

			$dateObj      = DateTime::createFromFormat('!m', $key);
			$monthName    = $dateObj->format('F');
			$key          = $key . '/' . $year[0];
			$months[$key] = $monthName;
		}
//        return $months;
		return view('backend.office.managerefficiency.index', compact('months'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('backend.office.managerefficiency.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
//        return $request;

		$efficiency                                  = new ManagerEfficiency();
		$efficiency->canvassaers_openers_closers_avg = $request->employeeCount;
		$efficiency->manager_avg                     = $request->managerCount;
		$efficiency->office_id                       = Auth::user()->office[0];
		$efficiency->user_id                         = Auth::user()->id;
		$efficiency->other                           = $request->partTime;
		$efficiency->truth                           = $request->truth;
		$efficiency->save();

//        return $efficiency;

		return redirect()->route('dashboard.dashboard')->withFlashSuccess(__('Record Recorded'));

	}


	public function show($month, $year)
	{
		$start = Carbon::createFromDate($year, $month, 1);
		$end   = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
//       return $start;

		$managerEfficiency = ManagerEfficiency::whereBetween('created_at', [$start, $end])
			->with('user', 'office')
			->get();

		$managerEfficiency = $managerEfficiency->sortBy('office.id');

//        return $managerEfficiency;

		return view('backend.office.managerefficiency.show', compact('managerEfficiency'));

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\ManagerEfficiency $managerEfficiency
	 * @return Response
	 */
	public function edit(ManagerEfficiency $managerEfficiency)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param \App\ManagerEfficiency $managerEfficiency
	 * @return Response
	 */
	public function update(Request $request, ManagerEfficiency $managerEfficiency)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\ManagerEfficiency $managerEfficiency
	 * @return Response
	 */
	public function destroy(ManagerEfficiency $managerEfficiency)
	{
		//
	}
}
