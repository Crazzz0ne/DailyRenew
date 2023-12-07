<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\User\ManageUserRequest;
use App\Models\Auth\User;
use App\Repositories\Backend\Auth\UserRepository;
use Throwable;

/**
 * Class UserStatusController.
 */
class UserStatusController extends Controller
{
	/**
	 * @var UserRepository
	 */
	protected $userRepository;

	/**
	 * @param UserRepository $userRepository
	 */
	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * @param ManageUserRequest $request
	 *
	 * @return mixed
	 */
	public function getDeactivated(ManageUserRequest $request)
	{
		return view('backend.auth.user.deactivated')
			->withUsers($this->userRepository->getInactivePaginated(25, 'id', 'asc'));
	}

	/**
	 * @param ManageUserRequest $request
	 *
	 * @return mixed
	 */
	public function getDeleted(ManageUserRequest $request)
	{
		return view('backend.auth.user.deleted')
			->withUsers($this->userRepository->getDeletedPaginated(25, 'id', 'asc'));
	}

	/**
	 * @param ManageUserRequest $request
	 * @param User $user
	 * @param                   $status
	 *
	 * @return mixed
	 * @throws GeneralException
	 */
	public function mark(ManageUserRequest $request, User $user, $status)
	{
		$this->userRepository->mark($user, (int)$status);

		return redirect()->route(
			(int)$status === 1 ?
				'dashboard.auth.user.index' :
				'dashboard.auth.user.deactivated'
		)->withFlashSuccess(__('alerts.backend.users.updated'));
	}

	/**
	 * @param ManageUserRequest $request
	 * @param User $deletedUser
	 *
	 * @return mixed
	 * @throws Throwable
	 * @throws GeneralException
	 */
	public function delete(ManageUserRequest $request, User $deletedUser)
	{
		$this->userRepository->forceDelete($deletedUser);

		return redirect()->route('dashboard.auth.user.deleted')->withFlashSuccess(__('alerts.backend.users.deleted_permanently'));
	}

	/**
	 * @param ManageUserRequest $request
	 * @param User $deletedUser
	 *
	 * @return mixed
	 * @throws GeneralException
	 */
	public function restore(ManageUserRequest $request, User $deletedUser)
	{
		$this->userRepository->restore($deletedUser);

		return redirect()->route('dashboard.auth.user.index')->withFlashSuccess(__('alerts.backend.users.restored'));
	}
}
