<?php

namespace App\Http\Controllers\Api\Micro;

use App\Http\Controllers\Controller;
use App\Services\GeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeoLocationController extends Controller
{

    protected $geoService;

    public function __construct(GeoService $geoService)
    {
        $this->geoService = $geoService;
    }


    public function index(Request $request)
    {
        try {
            $data = $this->geoService->sendData($request->lat, $request->long, $request->radius, $request->filter);
            return ['data' => $data];
        } catch (\Exception $e) {
            dump($e->getMessage());
            dump($request->lat, $request->long, $request->radius);
            // Handle exceptions, maybe return a custom error page or message.
        }
    }

}
