<?php


namespace App\Models\SalesFlow\Appointment\Traits\Attribute;


use Carbon\Carbon;

trait AppointmentAttribute
{

    public function getAppointmentBlock()
    {
        $startTime = Carbon::parse($this->start_time);
             return $startTime;
        return $this->last_name ? $this->first_name . ' ' . $this->last_name
            : $this->first_name;
    }
}
