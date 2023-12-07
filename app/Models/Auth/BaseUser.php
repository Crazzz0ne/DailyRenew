<?php

namespace App\Models\Auth;

use App\Models\Auth\Traits\SendUserPasswordReset;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Junaidnasir\Larainvite\InviteTrait;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Tags\HasTags;

/**
 * Class User.
 */
class BaseUser extends Authenticatable implements AuditableInterface
{
	use Auditable,
		HasRoles,
		Notifiable,
        HasTags,
		SendUserPasswordReset,
		SoftDeletes,
		Uuid,
        InviteTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'avatar_type',
		'avatar_location',
		'phone_number',
		'password',
		'password_changed_at',
		'active',
		'manager_efficiency',
		'confirmation_code',
		'confirmed',
		'timezone',
		'last_login_at',
		'last_login_ip',
		'to_be_logged_out',
        'office_id',
        'his_license',
        'remote_option',
        'languages',
        'terminated',
        'avatar_type',
        'pay_rate_id'
	];

//protected $guarded =[];
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Attributes to exclude from the Audit.
	 *
	 * @var array
	 */
	protected $auditExclude = [
		'id',
		'password',
		'remember_token',
		'confirmation_code',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'active' => 'boolean',
		'manager_efficiency' => 'boolean',
		'confirmed' => 'boolean',
		'to_be_logged_out' => 'boolean',
        'remote_option' => 'boolean',
        'languages' => 'array',
        'auto_assign_rr' => 'boolean'
	];

	/**
	 * @var array
	 */
	protected $dates = [
		'last_login_at',
		'password_changed_at',
	];

	/**
	 * The dynamic attributes from mutators that should be returned with the user object.
	 * @var array
	 */
	protected $appends = [
		'full_name',
		'office',
	];

    protected static function boot()
    {
        parent::boot();
//
//        static::addGlobalScope('terminated', function (Builder $builder) {
//            $builder->where('terminated', null);
//        });
    }


}
