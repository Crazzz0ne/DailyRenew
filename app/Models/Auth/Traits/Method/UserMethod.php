<?php

namespace App\Models\Auth\Traits\Method;

use App\Models\Office\Market\Market;
use Carbon\Carbon;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

/**
 * Trait UserMethod.
 */
trait UserMethod
{
    /**
     * @return mixed
     */
    public function canChangeEmail()
    {
        return config('access.users.change_email');
    }

    /**
     * @return bool
     */
    public function canChangePassword()
    {
        return !app('session')->has(config('access.socialite_session_name'));
    }

    public function getOfficeAttribute()
    {
        return null;
    }

    /**
     * @param bool $size
     *
     * @return bool|UrlGenerator|mixed|string
     * @throws EntryNotFoundException
     */
    public function getPicture($size = false)
    {
        switch ($this->avatar_type) {
            case 'gravatar':
                if (!$size) {
                    $size = config('gravatar.default.size');
                }

                return gravatar()->get($this->email, ['size' => $size]);

            case 'storage':
                $disk = Storage::disk('s3');

                return  $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), $this->avatar_location, Carbon::now()->addMinutes(30), []);
        }

        $social_avatar = $this->providers()->where('provider', $this->avatar_type)->first();

        if ($social_avatar && strlen($social_avatar->avatar)) {
            return $social_avatar->avatar;
        }

        return false;
    }

    /**
     * @param $provider
     *
     * @return bool
     */
    public function hasProvider($provider)
    {
        foreach ($this->providers as $p) {
            if ($p->provider == $provider) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function isAdmin()
    {
        if ($this->hasRole(config('access.users.admin_role'))
            || $this->hasRole(config('access.users.executive_role'))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */

    public function isExecutive()
    {
        return $this->hasRole(config('access.users.executive_role'));
    }

    /**
     * @return mixed
     */

    public function isSuper()
    {
        return $this->hasRole(config('access.users.super_admin_role'));
    }

    public function isManager()
    {
        return $this->hasRole(config('access.users.manager_role'));
    }

    /**
     * @return mixed
     */

    public function isUser()
    {
        return $this->hasRole(config('access.users.default_role'));
    }

    public function canCreateAnnouncement()
    {
        return $this
            ->hasRole(config('access.users.admin_role'))
            ->hasPermission('');

    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return config('access.users.requires_approval') && !$this->confirmed;
    }

    /**
     * This gets all permissions for VUE
     */

    public function getAllPermissionsAttribute()
    {
        $permissions = [];
        foreach (Permission::all() as $permission) {
            if (Auth::user()->can($permission->name)) {
                $permissions[] = $permission->name;
            }
        }
        return $permissions;
    }

    /**
     * Pulls the market for the user. This will need a refactor when we refactor office.
     */
    public function getMarketNameAttribute()
    {


        return Market::whereHas('office', function (Builder $q) {
            $q->where('id', '=', $this->office_id);
        })->get()->first()->name;


    }
}
