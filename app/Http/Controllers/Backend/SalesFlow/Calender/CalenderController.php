<?php


namespace App\Http\Controllers\Backend\SalesFlow\Calender;


use App\Models\SalesFlow\Appointment\Appointment;

class CalenderController
{
    public function index()
    {
        return view('backend.lead.calender');
    }

    public function unsignedRep()
    {
        if (auth()->user()->hasAnyRole(['executive', 'administrator'])) {
            $list = Appointment::where('user_id', '=', null)->with('lead')->get();
            return view('backend.lead.viewUnassignedAppointment', compact('list'));
        }else if (auth()->user()->hasRole('manager')){
            $officeId = auth()->user()->office_id;
            $list = Appointment::where('user_id', '=', null)->whereHas('lead', function ($q) use ($officeId){
                $q->where('office_id', $officeId);
            })->with('lead')->get();
            return view('backend.lead.viewUnassignedAppointment', compact('list'));
        }
    }
}
