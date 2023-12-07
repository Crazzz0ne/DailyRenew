<?php


namespace App\Models\SalesFlow\Lead;


use App\Models\SalesFlow\Lead\Traits\Relationship\SalesPacketRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class SalesPacket extends Model implements AuditableInterface
{
    use SalesPacketRelationship;
    use SoftDeletes;
    use Auditable;

    protected $guarded = [

    ];

    protected $dates = [
        'site_plan',
        'nem_doc_signed',
        'cpuc_doc_signed',
        'ach_doc_signed',
        'proposal_doc_signed',
        'solar_agreement_signed',
        'credit_doc_signed',
        'converted',
    ];

    protected $casts = [
        'site_survey_note' => 'string'
    ];
    protected $auditExclude = [

    ];

}
