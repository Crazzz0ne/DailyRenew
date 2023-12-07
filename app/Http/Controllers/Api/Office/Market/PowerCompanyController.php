<?php

namespace App\Http\Controllers\Api\Office\Market;

use App\Http\Controllers\Controller;
use App\Http\Resources\Market\PowerCompany\PowerCompanyResource;
use App\Models\Office\Market\Market;
use App\Models\Office\Market\PowerCompany\PowerCompany;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PowerCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return PowerCompanyResource::collection(Market::where('name','=', $request->market)->with('powerCompany.program')->get());
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
//        return 'this ran';
//        return $request;
        return PowerCompanyResource::collection(PowerCompany::with('program')->get());
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
