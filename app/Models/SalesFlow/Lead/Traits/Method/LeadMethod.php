<?php


namespace App\Models\SalesFlow\Lead\Traits\Method;


trait LeadMethod
{

    public function isClosed(): bool
    {
        $closedId = [7,8,9,10,11,12,13];

        if (in_array($this->status_id, $closedId, true)) {

            return true;
        } else {

            return false;
        }
    }

    public function isCreditPass(): bool
    {
        $passedIsh = [2];

        if ($this->credit_status_id === 2) {
            return true;
        } else {
            return false;
        }
    }

}
