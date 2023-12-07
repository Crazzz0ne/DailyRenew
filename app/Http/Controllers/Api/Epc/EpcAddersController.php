<?php


namespace App\Http\Controllers\Api\Epc;

use App\Http\Controllers\Controller;
use App\Models\Epc\EpcAdders;
use Illuminate\Http\Request;

class EpcAddersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new EpcAddersController(EpcAdders::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Epc $epc
     * @return \Illuminate\Http\Response
     */
    public function show(EpcAddersController $epc)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Epc $epc
     * @return \Illuminate\Http\Response
     */
    public function edit(Epc $epc)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Epc $epc
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Epc $epc)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Epc $epc
     * @return \Illuminate\Http\Response
     */
    public function destroy(Epc $epc)
    {
        //
    }

}
