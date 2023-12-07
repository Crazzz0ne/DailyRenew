<?php

namespace App\Models\SalesFlow\Appointment;

use App\Models\SalesFlow\Appointment\Traits\Attribute\AppointmentAttribute;
use App\Models\SalesFlow\Appointment\Traits\Attribute\AppointmentScope;
use App\Models\SalesFlow\Appointment\Traits\Relationship\AppointmentRelationship;

class Appointment extends BaseAppointment
{
    use AppointmentRelationship,
        AppointmentScope,
        AppointmentAttribute;


}
