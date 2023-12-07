<?php

namespace App\Http\Controllers\Backend\User;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\UpdatePasswordRequest;
use App\Repositories\Frontend\Auth\UserRepository;

/**
 * Class UpdatePasswordController.
 */
class UpdatePasswordController extends Controller
{
	/**
	 * @var UserRepository
	 */
	protected $userRepository;

	/**
	 * ChangePasswordController constructor.
	 *
	 * @param UserRepository $userRepository
	 */
	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * @param UpdatePasswordRequest $request
	 *
	 * @return mixed
	 * @throws GeneralException
	 */
	public function update(UpdatePasswordRequest $request)
	{
		$this->userRepository->updatePassword($request->only('old_password', 'password'));

		return redirect()->route('dashboard.user.account')->withFlashSuccess(__('strings.frontend.user.password_updated'));
	}
}