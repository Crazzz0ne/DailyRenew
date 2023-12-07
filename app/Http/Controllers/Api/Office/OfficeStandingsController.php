<?php

namespace App\Http\Controllers\Api\Office;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfficeStandingResource;
use App\Models\Office\OfficeStanding;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OfficeStandingsController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param String
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param String $month
	 * @return Response
	 */
	public function showMonth(String $month)
	{
		$start = Carbon::parse($month)->startOfMonth()->toDateString();
		$end   = Carbon::parse($month)->endOfMonth()->toDateString();

		$links = OfficeStanding::whereBetween('sdate', [$start, $end])
			->where('approved', '=', 1)
			->with(['data.office'])
			->get();

		return OfficeStandingResource::collection($links);

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
}
