<?php


namespace App\Models\Office\Market\PowerCompany;


use App\Models\Office\Market\PowerCompany\Traits\Relationships\ProgramRelationship;

class Program extends BaseProgram
{
    use ProgramRelationship;

    protected $table = 'power_company_programs';

}
