<?php


namespace App\Http\Controllers\Webhook\EPC;


use App\Mail\SalesFlow\BaseMailable;
use App\Models\SalesFlow\Appointment\Availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CompleteEPCController
{


    public function index(Request $request)
    {

        $payload =  json_decode($request);
        $headers = $request->header();

        $dump = json_decode($payload);
        $sendIt = [$dump, $headers];
        \Log::debug('complete', [$dump, $headers]);



        Mail::to('chris.furman@solcalenergy.com')
            ->queue(new BaseMailable('complete webhook', $dump, 'nowhere', 'lead'));


        return 'thank you';

    }

    public function store(Request $request)
    {

        $payload = $request;
        $headers = $request->header();

        $dump = json_decode($payload);
        $sendIt = [$dump, $headers];
        \Log::debug('complete', [$dump, $headers]);



        Mail::to('chris.furman@solcalenergy.com')
            ->queue(new BaseMailable('complete webhook', $dump, 'nowhere', 'lead'));


        return 'thanks';


    }

    public function update(Request $request, Availability $availability)
    {

    }


    public function show(Availability $availability)
    {

    }

}
