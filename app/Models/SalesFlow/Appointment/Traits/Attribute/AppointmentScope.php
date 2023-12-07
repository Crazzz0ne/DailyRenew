<?php


namespace App\Models\SalesFlow\Appointment\Traits\Attribute;


trait AppointmentScope
{
    public function scopeOfType($query, $type)
    {
        return $query->where('type_id', $type);
    }

    public function scopeOfOffice($query, $officeId)
    {
        return $query->whereHas('lead', function ($q) use ($officeId) {
            $q->where('office_id', $officeId)
                ->orWhere('origin_office_id', $officeId);
        });
    }

//    public function scopeOfOffices($query, $officeId)
//    {
//        if ($officeId === 9) {
//            $query->whereHas('lead', function ($q) {
//                $q->where('source', 'call center');
//            });
//        } else {
//            $query->whereHas('lead', function ($q) use ($officeId) {
//                $q->whereIn('office_id', $officeId);
//            });
//        }
//    }
}
