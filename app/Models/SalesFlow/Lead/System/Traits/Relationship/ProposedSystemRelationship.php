<?php


namespace App\Models\SalesFlow\Lead\System\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\Epc\SolarInverter;
use App\Models\Epc\SolarModule;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadUpload;

trait ProposedSystemRelationship
{

    public function lead(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function pbDesignApproved(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'pb_design_approved', 'id');
    }

    public function repDesignApproved(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'rep_design_approved', 'id');
    }

    public function testDoc(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeadUpload::class, 'test_doc_id', 'id');
    }

    public function proposalDoc(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeadUpload::class, 'proposal_doc_id', 'id');
    }

    public function sitePlanDoc(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeadUpload::class, 'site_plan_doc_id', 'id');
    }

    public function savingsBreakDown(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeadUpload::class, 'savings_doc_id', 'id');
    }


    public function lines()
    {
        return $this->morphToMany('App\Models\SalesFlow\Lead\ProposedSystem', 'queuetable');
    }

    public function line()
    {
        return $this->morphToMany('App\Models\SalesFlow\Lead\ProposedSystem', 'queuetable');
    }

    public function module()
    {
        return $this->hasOne(SolarModule::class, 'id', 'modules_id');
    }
    public function inverter()
    {
        return $this->hasOne(SolarInverter::class, 'id', 'inverter_id');
    }


}
