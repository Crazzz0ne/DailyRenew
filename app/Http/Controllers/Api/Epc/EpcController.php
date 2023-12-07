<?php

namespace App\Http\Controllers\Api\Epc;

use App\Http\Resources\Epc\EpcResource;
use App\Models\Epc\Epc;
use App\Http\Controllers\Controller;
use App\Models\Office\Market\RegionAPI;
use App\Models\SalesFlow\Lead\Lead;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EpcController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return EpcResource::collection(Epc::where('id', '!=', 0)->with('adders', 'creditStatus')->get());
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

    public function jwt(Lead $lead, Request $request)
    {

        if ($lead->origin_office_id === 5 ) {
            $key = 'lfRGNSEUyzL5wiIvdECHAAIsdSpBAy6J';
        }else{
            $key = $this->getKey($lead);
        }

        if (app()->environment('production')) {

            $url = 'https://heliotrack.completesolar.com/';
        } else {
            $url = 'https://heliotrack.completesolar.biz/';
        }

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $url,
            // You can set any number of default request options.
            'timeout' => 500.0,
        ]);

        $response = $client->request('GET', '/api/access-token', [
            'headers' => [
                'x-api-key' => $key
            ],

        ]);

        $body = json_decode($response->getBody()->getContents());
        $payload = collect($body);
        $payload['production'] = app()->environment('production') ?? false;
        return $payload;
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
     * @param Epc $epc
     * @return EpcResource
     */
    public function show(Epc $epc)
    {
        $payload = Cache::remember('epc', '60000', function () {
            return Epc::where('id', 1)->with('adders', 'solarInverters', 'solarModules', 'creditStatus', 'powerCompany', 'finance')->first();
        });


        return new EpcResource($payload);
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

    function getKey($lead)
    {
        $regionId = $lead->office()->first()->market_id;
//cache the region key
        return Cache::remember('regionKey' . $regionId, 600, function () use ($regionId) {
            return RegionAPI::where('region_id', $regionId)->first()->api_key;
        });
    }

    public function selfSiteSurvey(Lead $lead)
    {
//        Connect to complete solars API Post url /api/jobs/wizard-link/api-client
        $url = 'https://heliotrack.completesolar.com/';
        $key = $this->getKey($lead);
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $url,
            // You can set any number of default request options.
            'timeout' => 500.0,
        ]);

        $response = $client->request('POST', '/api/jobs/wizard-link/api-client', [
            'headers' => [
                'x-api-key' => $key
            ],
            'form_params' => [
                'action' => 'post_survey',
                'solar_expert_email' => 'admin@solvidagroup.com',
                'job_uuid' => $lead->epc_owner_id,
            ]
        ]);

        $res = $response->getBody()->getContents();
        $body = json_decode($res);
        $payload = collect($body);
        return $payload['wizard_link'];


    }
}
