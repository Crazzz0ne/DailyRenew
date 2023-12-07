<?php


namespace App\Models\Office\Market\PowerCompany;


use App\Models\Office\Market\PowerCompany\Traits\Relationships\PowerCompanyRelationship;

class PowerCompany extends BasePowerCompany
{
    use PowerCompanyRelationship;

    protected $table = 'power_companies';

}
