<?php


namespace App\Models\SalesFlow\Appointment;


use App\Models\SalesFlow\Appointment\Traits\Relationship\AvailabilityRelationship;

class Availability extends BaseAvailability
{
    use AvailabilityRelationship;
}
