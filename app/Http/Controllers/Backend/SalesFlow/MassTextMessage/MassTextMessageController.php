<?php

namespace App\Http\Controllers\Backend\SalesFlow\MassTextMessage;

use App\Events\Backend\SalesFlow\MailChimpDripAddEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\SalesFlow\MassText\MassText;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Newsletter;

class MassTextMessageController extends Controller
{

    public function index()
    {
//        $user = User::where('id', 56)->first();
//        event(new TextEvent($user->phone_number, 'Hello LMK you got this -- Chris'));
        $texts = MassText::all();
        return view('backend.salesflow.masstext.index', compact('texts'));
    }

    public function create()
    {
        return view('backend.salesflow.masstext.create');
    }

    public function store(Request $request)
    {


        request()->validate([
            'csv' => 'required|mimes:csv,txt'
        ]);

        $handle = fopen($request->csv, "r");
        $header = true;

        while ($csvLine = fgetcsv($handle, 1000, ",")) {

            if ($header) {
                $header = false;
            } else {
//                dd($csvLine);
                if ($request->type == 'mailchimp') {
                    $repName = $csvLine[3];
                    $customerName = $csvLine[0];
                } else {
                    $customerName = explode(' ', trim($csvLine[0]));
                    $repName = explode(' ', trim($csvLine[3]));
                    $repName = $repName[0];
                }


                $massText = new MassText();
                $massText->customer_name = $customerName;
                $massText->customer_number = preg_replace('/[^0-9]/', '', $csvLine[1]);
                $massText->rep_name = $repName;
                $massText->email = $csvLine[2];
                $massText->type = $request->type;

//                dd($massText->getAttribute());
                $massText->save();
            }
        }


        return $massText;
    }
}
