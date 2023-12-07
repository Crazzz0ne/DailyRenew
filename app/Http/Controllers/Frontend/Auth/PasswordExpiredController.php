<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\UpdatePasswordRequest;
use App\Repositories\Frontend\Auth\UserRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Class PasswordExpiredController.
 */
class PasswordExpiredController extends Controller
{
	/**
	 * @return Factory|View
	 */
	public function expired()
	{
		abort_unless(config('access.users.password_expires_days'), 404);

		return view('frontend.auth.passwords.expired');
	}

	/**
	 * @param UpdatePasswordRequest $request
	 * @param UserRepository $userRepository
	 *
	 * @return mixed
	 * @throws GeneralException
	 */
	public function update(UpdatePasswordRequest $request, UserRepository $userRepository)
	{
		abort_unless(config('access.users.password_expires_days'), 404);

		$userRepository->updatePassword($request->only('old_password', 'password'), true);

		return redirect()->route('frontend.user.account')
			->withFlashSuccess(__('strings.frontend.user.password_updated'));
	}
}
