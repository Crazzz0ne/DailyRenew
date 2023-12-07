<?php


namespace App\Models\SalesFlow\Lead\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\Commission\CommissionLedgers;
use App\Models\Epc\CompleteSiteSurveyQuestions;
use App\Models\Epc\Epc;
use App\Models\Epc\EpcCreditStatus;
use App\Models\Office\Office;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Disposition;
use App\Models\SalesFlow\Lead\LeadLogin;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\LeadRoof;
use App\Models\SalesFlow\Lead\LeadStatus;
use App\Models\SalesFlow\Lead\LeadUpload;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\System\System;
use App\Models\SalesFlow\Outside\ReHash;
use App\RoofType;
use Queue;


trait LeadRelationship
{
    public function customer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function originOffice(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Office::class, 'id', 'origin_office_id');
    }

    public function loginInfo(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LeadLogin::class);
    }

    public function leadNote(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LeadNote::class);
    }

    public function notes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LeadNote::class);
    }

    public function leadUploads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LeadUpload::class, 'lead_id', 'id');
    }

    public function reps(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_has_leads')->where('user_has_leads.deleted_at', null)->withPivot('position_id');
    }
    public function rep()
    {
        return $this->belongsToMany(User::class, 'user_has_leads')->where('user_has_leads.deleted_at', null)->withPivot('position_id')->first();
    }

    public function office(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_has_leads');
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function proposal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RequestedSystem::class);
    }

    public function utility(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeadUtility::class);
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function salesPacket(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SalesPacket::class);
    }

    public function proposedSystem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
      return $this->belongsTo(ProposedSystem::class);
    }

    public function proposedSystems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProposedSystem::class);
    }

    public function commissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CommissionLedgers::class);
    }

    public function epc()
    {
        return $this->belongsTo(Epc::class, 'epc_id', 'id');
    }

    public function statusName()
    {
        return $this->belongsTo(LeadStatus::class, 'status_id', 'id');
    }

    public function jeopardyName()
    {
        return $this->belongsTo(LeadStatus::class, 'jeopardy_id', 'id');
    }

    public function lines()
    {
        return $this->hasMany(Line::class);
    }

    public function leadRoof()
    {
        //Get RoofType through LeadRoof using my ID
        return $this->hasOne(LeadRoof::class);
    }
    public function reHash()
    {

        return $this->hasOne(ReHash::class);
    }

    public function getRoofAttribute()
    {
            return $this->leadRoof;
    }
    public function appointment()
    {
        return $this->hasOne(Appointment::class, 'lead_id', 'id');
    }
    public function queues()
    {
        return $this->hasMany(Line::class);
    }

    public function creditStatus()
    {
        return $this->belongsTo(EpcCreditStatus::class, 'credit_status_id', 'id');
    }

    public function siteSurveyQuestions()
    {
        return $this->hasOne(CompleteSiteSurveyQuestions::class,);
    }

}
