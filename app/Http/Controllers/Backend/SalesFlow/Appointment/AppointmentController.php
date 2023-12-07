<?php


namespace App\Http\Controllers\Backend\SalesFlow\Appointment;


use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Lead;

class AppointmentController
{

    public function index()
    {
//       return Appointment::where('id', '=', 1)->with('user')->get();

        return view('backend.lead.calender');
    }
}
