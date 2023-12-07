<?php

namespace App\Repositories\Backend\Auth;

use App\Events\Backend\Auth\User\UserConfirmed;
use App\Events\Backend\Auth\User\UserCreated;
use App\Events\Backend\Auth\User\UserDeactivated;
use App\Events\Backend\Auth\User\UserPasswordChanged;
use App\Events\Backend\Auth\User\UserPermanentlyDeleted;
use App\Events\Backend\Auth\User\UserReactivated;
use App\Events\Backend\Auth\User\UserRestored;
use App\Events\Backend\Auth\User\UserUnconfirmed;
use App\Events\Backend\Auth\User\UserUpdated;
use App\Exceptions\GeneralException;
use App\Models\Announcement\Announcement;
use App\Models\Announcement\AnnouncementHasUser;
use App\Models\Auth\User;
use App\Models\Auth\UserHasPosition;
use App\Models\Office\ManagerEfficiency;
use App\Models\Office\OfficeUser;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Models\SalesFlow\Position\Position;
use App\Notifications\Backend\Auth\UserAccountActive;
use App\Notifications\Frontend\Auth\UserNeedsConfirmation;
use App\Office;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * @return mixed
     */
    public function getUnconfirmedCount(): int
    {
        return $this->model
            ->where('confirmed', false)
            ->count();
    }

    public function getUnconfirmedCountManager(): int
    {

        $i = 0;
        $count = $this->model
            ->where('confirmed', false)
            ->with('office')
            ->get();
//        dd($count[0]->officeHasUser[0]->id);
        foreach ($count as $c) {
//            dd($c->officeHasUser->user_id);
            if ($c->officeHasUser != null && auth()->user()->office[0] == $c->officeHasUser[0]->id) {
                $i++;
            }
        }
        return $i;


    }

    /**
     * @param int $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */
    public function getActivePaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->with('roles', 'permissions', 'providers')
            ->active()
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    public function getActivePaginatedOffice($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->with('roles', 'permissions', 'providers', 'homeOffice')
//            ->scopeHasOffice([1])
            ->active()
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * @param int $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return LengthAwarePaginator
     */
    public function getInactivePaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->with('roles', 'permissions', 'providers')
            ->active(false)
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * @param int $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return LengthAwarePaginator
     */
    public function getDeletedPaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->with('roles', 'permissions', 'providers')
            ->onlyTrashed()
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * @param array $data
     *
     * @return User
     * @throws Throwable
     * @throws Exception
     */
    public function create(array $data): User
    {


        return DB::transaction(function () use ($data) {
            $user = parent::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'api_token' => Str::random(60),
                'phone_number' => $data['phone_number'],
                'office_id' => $data['office'],
                'his_license' => $data['his_license'],
                'active' => isset($data['active']) && $data['active'] === '1',
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'confirmed' => 1,
            ]);
            $userid = DB::getPdo()->lastInsertId();;
            // See if adding any additional permissions
            if (!isset($data['permissions']) || !count($data['permissions'])) {
                $data['permissions'] = [];
            }


            if ($user) {
                // User must have at least one role
                if (!count($data['roles'])) {
                    throw new GeneralException(__('exceptions.backend.access.users.role_needed_create'));
                }


                // Add selected roles/permissions
                $user->syncRoles($data['roles']);
                $user->syncPermissions($data['permissions']);


                //Send confirmation email if requested and account approval is off
                if (isset($data['confirmation_email'])) {
                    $user->notify(new UserNeedsConfirmation($user->confirmation_code));
                }

                event(new UserCreated($user));

                return $user;
            }

            throw new GeneralException(__('exceptions.backend.access.users.create_error'));
        });
    }

    /**
     * @param User $user
     * @param array $data
     *
     * @return User
     * @throws Exception
     * @throws Throwable
     * @throws GeneralException
     */
    public function update(User $user, array $data): User
    {
        $this->checkUserByEmail($user, $data['email']);
        if (!isset($data['permissions']) || !count($data['permissions'])) {
            $data['permissions'] = [];
        }
        return DB::transaction(function () use ($user, $data) {
            if ($user->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone_number' => $data['phone_number'],
                'email' => $data['email'],
                'office_id' => $data['office'],
                'his_license' => $data['his_license']
            ])) {
                // Add selected roles/permissions
                $user->syncRoles($data['roles']);

                $user->syncPermissions($data['permissions']);
                event(new UserUpdated($user));

                return $user;
            }

            throw new GeneralException(__('exceptions.backend.access.users.update_error'));
        });
    }

    /**
     * @param User $user
     * @param      $email
     *
     * @throws GeneralException
     */
    protected function checkUserByEmail(User $user, $email)
    {
        // Figure out if email is not the same and check to see if email exists
        if ($user->email !== $email && $this->model->where('email', '=', $email)->first()) {
            throw new GeneralException(trans('exceptions.backend.access.users.email_error'));
        }
    }

    /**
     * @param User $user
     * @param      $input
     *
     * @return User
     * @throws GeneralException
     */
    public function updatePassword(User $user, $input): User
    {
        if ($user->update(['password' => $input['password']])) {
            event(new UserPasswordChanged($user));

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.update_password_error'));
    }

    /**
     * @param User $user
     * @param      $status
     *
     * @return User
     * @throws GeneralException
     */
    public function mark(User $user, $status): User
    {
        if ($status === 0 && auth()->id() === $user->id) {
            throw new GeneralException(__('exceptions.backend.access.users.cant_deactivate_self'));
        }

        $user->active = $status;

        switch ($status) {
            case 0:
                $user->confirmed = 0;
                $user->terminated = Carbon::now()->toDateTimeString();
                event(new UserDeactivated($user));
                break;
            case 1:
                $user->terminated = null;
                $user->confirmed = 1;
                event(new UserReactivated($user));
                break;
        }


        if ($user->save()) {
            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.mark_error'));
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws GeneralException
     */
    public function confirm(User $user): User
    {
        if ($user->confirmed) {
            throw new GeneralException(__('exceptions.backend.access.users.already_confirmed'));
        }

        $user->confirmed = true;
        $user->api_token = Str::random(60);
        $confirmed = $user->save();

        if ($confirmed) {
            event(new UserConfirmed($user));

            // Let user know their account was approved
            if (config('access.users.requires_approval')) {
                $user->notify(new UserAccountActive);
            }

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.cant_confirm'));
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws GeneralException
     */
    public function unconfirm(User $user): User
    {
        if (!$user->confirmed) {
            throw new GeneralException(__('exceptions.backend.access.users.not_confirmed'));
        }

        if ($user->id === 1) {
            // Cant un-confirm admin
            throw new GeneralException(__('exceptions.backend.access.users.cant_unconfirm_admin'));
        }

        if ($user->id === auth()->id()) {
            // Cant un-confirm self
            throw new GeneralException(__('exceptions.backend.access.users.cant_unconfirm_self'));
        }

        $user->confirmed = false;
        $unconfirmed = $user->save();

        if ($unconfirmed) {
            event(new UserUnconfirmed($user));

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.cant_unconfirm'));
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws Exception
     * @throws Throwable
     * @throws GeneralException
     */
    public function forceDelete(User $user): User
    {
        if ($user->deleted_at === null) {
            throw new GeneralException(__('exceptions.backend.access.users.delete_first'));
        }

        return DB::transaction(function () use ($user) {
            // Delete associated relationships
            Announcement::where('user_id', '=', $user->id)->delete();
            AnnouncementHasUser::where('user_id', '=', $user->id)->delete();

//            TODO: Manager efficiency does not delete. Also should reassain announcment and manager efficiency to a vacant number
            ManagerEfficiency::where('user_id', '=', $user->id)->delete();
            OfficeUser::where('user_id', '=', $user->id)->delete();
            UserHasPosition::where('user_id', '=', $user->id)->delete();
            UserHasLead::where('user_id', '=', $user->id)->delete();

            $user->passwordHistories()->delete();
            $user->providers()->delete();

            if ($user->forceDelete()) {
                event(new UserPermanentlyDeleted($user));

                return $user;
            }

            throw new GeneralException(__('exceptions.backend.access.users.delete_error'));
        });
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws GeneralException
     */
    public function restore(User $user): User
    {
        if ($user->deleted_at === null) {
            throw new GeneralException(__('exceptions.backend.access.users.cant_restore'));
        }

        if ($user->restore()) {
            event(new UserRestored($user));

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.restore_error'));
    }

    public function userByPosition($type, $officeId = null)
    {
        switch ($type) {
            case $type === 'sp1':
            {
                $users = User::where('office_id', $officeId)
                    ->role('sp1')
                    ->where("terminated", null)
                    ->get();
                break;
            }
            case $type === 'build_proposal':
            {
                $users = User::permission('accept proposal builder')
                    ->where("terminated", null)
                    ->get();
                break;
            }
            case $type === 'sp2':
            {
                $users = User::where('office_id', $officeId)
                    ->role('sp2')
                    ->where("terminated", null)
                    ->get();
                break;
            }
            case $type === 'integrations':
            {
                $users = User::role('integrations')
                    ->where("terminated", null)
                    ->get();
                break;
            }
            case $type === 'NTS':
            {
                $users = User::permission('edit NTS')
                    ->where("terminated", null)
                    ->get();
                break;
            }
            case $type === 'credit_app':
            {
                $users = User::permission('accept credit runner')
                    ->where("terminated", null)
                    ->get();
                break;
            }
            case $type === 'sun_run_runner':
            {
                $users = User::permission('accept sales force runner')
                    ->where("terminated", null)
                    ->get();
                break;
            }
            case $type === 'd2d_call_center':
            {
                $users = User::permission('accept d2d call center')
                    ->where("terminated", null)
                    ->get();
                break;
            }
            case $type === 'roof':
            {
                $users = User::permission('accept roof assessor')
                    ->where("terminated", null)
                    ->get();
                break;
            }
            case $type === 'change_order':
            {
                $users = User::permission('accept change order')
                    ->where("terminated", null)
                    ->get();
                break;
            }
            default :
            {
                $users = 1;
            }
        }
//        Log::info('Users to notify: ' . json_encode([$users]));
        return $users;
    }
}
