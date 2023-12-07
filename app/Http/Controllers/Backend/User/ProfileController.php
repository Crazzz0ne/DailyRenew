<?php

namespace App\Http\Controllers\Backend\User;

use App\Events\Backend\SalesFlow\TextEvent;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\UpdateProfileRequest;
use App\Models\Auth\User;
use App\Repositories\Frontend\Auth\UserRepository;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Auth;

/**
 * Class ProfileController.
 */
class ProfileController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * ProfileController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function testText()
    {
        $body = Inspiring::quote();
        $body .= ' scout.solar';
        $user = Auth::user();
        event(new TextEvent($user->phone_number, $body));
//        event(new TextEvent($user->phone_number, 'Hello from Scout,
//             Reply START to receive future messages!
//             Reply QUIT to unsubscribe'));
        return redirect()->back()->withFlashSuccess('Text request Sent, If you did not receive in 30 seconds please
        contact Support');
    }

    /**
     * @param UpdateProfileRequest $request
     *
     * @return mixed
     * @throws GeneralException
     */
    public function update(UpdateProfileRequest $request)
    {
        $output = $this->userRepository->update(
            $request->user()->id,
            $request->only('first_name', 'last_name', 'phone_number', 'email', 'avatar_type', 'avatar_location', 'remote_option'),
            $request->has('avatar_location') ? $request->file('avatar_location') : false
        );

        // E-mail address was updated, user has to reconfirm
        if (is_array($output) && $output['email_changed']) {
            auth()->logout();

            return redirect()->route('frontend.auth.login')->withFlashInfo(__('strings.frontend.user.email_changed_notice'));
        }

        return redirect()->back()->withFlashSuccess(__('strings.frontend.user.profile_updated'));
    }
}
