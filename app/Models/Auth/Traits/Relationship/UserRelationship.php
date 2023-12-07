<?php

namespace App\Models\Auth\Traits\Relationship;

use App\Models\Auth\PasswordHistory;
use App\Models\Auth\SocialAccount;
use App\Models\Auth\UserApi;
use App\Models\Auth\UserUpload;
use App\Models\Collateral\CollateralContent;
use App\Models\Office\ManagerEfficiency;
use App\Models\Office\Office;
use App\Models\Office\Team;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Appointment\Availability;
use App\Models\SalesFlow\Commissions\PayRate;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Position\Position;
use App\Models\VendorLink\Link;


/**
 * Class UserRelationship.
 */
trait UserRelationship
{
    /**
     * @return mixed
     */
    public function providers()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * @return mixed
     */
    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class);
    }

    public function managerEfficiency()
    {
        return $this->hasMany(ManagerEfficiency::class);
    }

    public function officeHasUser()
    {
        return $this->belongsToMany(Office::class);
    }

    public function userHasAnnouncement()
    {
        return $this->belongsToMany('App\Models\Announcement\Announcement');
    }

    public function images()
    {
        return $this->hasMany(CollateralContent::class, 'auth_by')->latest();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class)->latest();
    }

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'user_has_position');
    }

    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'user_has_leads')->withPivot('position_id');
    }

    public function availability()
    {
        return $this->hasMany(Availability::class);
    }


    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');
    }

    public function homeOffice()
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');
    }

    public function uploads()
    {
        return $this->hasMany(UserUpload::class, 'user_id', 'id');
    }

    public function partnerLink()
    {
        return $this->hasMany(Link::class, 'email', 'email');
    }
    public function apikey()
    {
        return $this->hasMany(UserApi::class, 'user_id', 'id');
    }

    public function payRate()
    {
        return $this->belongsTo(PayRate::class, 'pay_rate_id', 'id');
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function createdBy()
    {
        return $this->hasMany(Appointment::class, 'created_by', 'id');
    }
//    public function rep()
//    {
//        return $this->hasMany(Lead::class);
//    }
}
