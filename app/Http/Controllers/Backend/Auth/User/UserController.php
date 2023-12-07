<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Events\Backend\Auth\User\UserDeleted;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\User\ManageUserRequest;
use App\Http\Requests\Backend\Auth\User\StoreUserRequest;
use App\Http\Requests\Backend\Auth\User\UpdateUserRequest;
use App\Models\Auth\User;
use App\Models\Auth\UserHasPosition;
use App\Models\Auth\UserUpload;
use App\Models\Office\OfficeUser;
use App\Models\SalesFlow\Position\Position;
use App\Office;

use App\Repositories\Backend\Auth\PermissionRepository;
use App\Repositories\Backend\Auth\RoleRepository;
use App\Repositories\Backend\Auth\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Junaidnasir\Larainvite\Facades\Invite;
use Throwable;

/**
 * Class UserController.
 */
class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param ManageUserRequest $request
     *
     * @return Factory|View
     */
    public function index(ManageUserRequest $request)
    {
//        return dd(auth());
//        return $this->userRepository->getActivePaginatedOffice(25, 'id', 'asc');
        $office = \App\Models\Office\Office::all();
        return view('backend.auth.user.index')
            ->with('office', $office)
            ->withUsers($this->userRepository->getActivePaginatedOffice(25, 'id', 'asc'));
    }

    /**
     * @param ManageUserRequest $request
     * @param RoleRepository $roleRepository
     * @param PermissionRepository $permissionRepository
     *
     * @return mixed
     */
    public function create(ManageUserRequest $request, RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {

        $offices = \App\Models\Office\Office::all();
        $positions = Position::all();
//        return $positions;
        return view('backend.auth.user.create')
            ->withRoles($roleRepository->with('permissions')->get(['id', 'name']))
            ->withPermissions($permissionRepository->get(['id', 'name']))
            ->with(compact('offices', 'positions'));
    }

    /**
     * @param StoreUserRequest $request
     *
     * @return mixed
     * @throws Throwable
     */
    public function store(StoreUserRequest $request)
    {
        $user = Auth::user();
        $refCode = Invite::invite($request->email, $user->id);

        $user = $this->userRepository->create($request->only(
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'password',
            'active',
            'confirmed',
            'confirmation_email',
            'roles',
            'permissions',
            'office',
            'his_license'
        ));

        if ($request->his_file) {
            $path = Storage::disk('s3')->put('user/docs/' . $user->last_name . '-' . $user->id . '/pdf', $request->his_file, 'private');

            $userUpload = new UserUpload();
            $userUpload->user_id = $user->id;
            $userUpload->path = $path;
            $userUpload->type = 'hsi';
            $userUpload->size = $request->file('his_file')->getSize();
            $userUpload->save();
        }

        return redirect()->route('dashboard.auth.user.index')->withFlashSuccess(__('alerts.backend.users.created'));
    }

    public function inviteUserCreate()
    {
     return   view('backend.auth.user.invite');
    }


    public function inviteUser(Request $request)
    {

        $user = Auth::user();
        $refCode = Invite::invite($request->email, $user->id);

        return redirect()->back()->withFlashSuccess('user invited invite another?');
    }

    /**
     * @param ManageUserRequest $request
     * @param User $user
     *
     * @return mixed
     */
    public function show(ManageUserRequest $request, User $user)
    {
        $disk = Storage::disk('s3');
        $hsiFile = null;

        if ($user->uploads){
           $uploads = $user->uploads;
            foreach ($uploads as $load){
                if ($load->type === 'hsi'){
                    $hsiFile = $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), $load->path, Carbon::now()->addMinutes(5), []);
                    }
                }
            }

        return view('backend.auth.user.show')
            ->withUser($user)
            ->with('hsiFile', $hsiFile);
    }

    /**
     * @param ManageUserRequest $request
     * @param RoleRepository $roleRepository
     * @param PermissionRepository $permissionRepository
     * @param User $user
     *
     * @return mixed
     */
    public function edit(ManageUserRequest $request, RoleRepository $roleRepository, PermissionRepository $permissionRepository, User $user)
    {
        $offices = \App\Models\Office\Office::all();
        $positions = [];
//        return $user->id;
        $positionsob = UserHasPosition::where('user_id', '=', $user->id)->pluck('position_id');
        foreach ($positionsob as $p) {
            array_push($positions, $p);
        }
        return view('backend.auth.user.edit')
            ->with('offices', $offices)
            ->with('positions', $positions)
            ->withUser($user)
            ->withRoles($roleRepository->get())
            ->withUserRoles($user->roles->pluck('name')->all())
            ->withPermissions($permissionRepository->get(['id', 'name']))
            ->withUserPermissions($user->permissions->pluck('name')->all());
    }

    /**
     * @param UpdateUserRequest $request
     * @param User $user
     *
     * @return mixed
     * @throws Throwable
     * @throws GeneralException
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($request->his_file) {
            $path = Storage::disk('s3')->put('user/docs/' . $user->last_name . '-' . $user->id . '/pdf', $request->his_file, 'private');

            $userUpload = new UserUpload();
            $userUpload->user_id = $user->id;
            $userUpload->path = $path;
            $userUpload->type = 'hsi';
            $userUpload->size = $request->his_file->getSize();
            $userUpload->save();
        }

        $this->userRepository->update($user, $request->only(
            'first_name',
            'last_name',
            'phone_number',
            'email',
            'roles',
            'permissions',
            'office',
            'his_license'
        ));
//        return $user;


        return redirect()->route('dashboard.auth.user.index')->withFlashSuccess(__('alerts.backend.users.updated'));
    }

    /**
     * @param ManageUserRequest $request
     * @param User $user
     *
     * @return mixed
     * @throws Exception
     */
    public function destroy(ManageUserRequest $request, User $user)
    {

        $this->userRepository->deleteById($user->id);

        event(new UserDeleted($user));

        return redirect()->route('dashboard.auth.user.deleted')->withFlashSuccess(__('alerts.backend.users.deleted'));
    }
}
