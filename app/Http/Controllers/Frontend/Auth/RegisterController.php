<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Events\Frontend\Auth\UserRegistered;
use App\Helpers\Auth\SocialiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Auth\RegisterRequest;
use App\Models\Auth\UserHasPosition;
use App\Models\Office\Office;
use App\Models\Office\OfficeOptions;
use App\Models\SalesFlow\Position\Position;
use App\Repositories\Frontend\Auth\UserRepository;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Junaidnasir\Larainvite\Facades\Invite;
use Throwable;
use Torann\GeoIP\Facades\GeoIP;

/**
 * Class RegisterController.
 */
class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * RegisterController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Show the application registration form.
     *
     * @param Request $request
     * @return Response
     */
    public function showRegistrationForm(Request $request)
    {


        abort_unless(config('access.registration'), 404);

        $token = $request->token;

        $invitation = Invite::get($request->token); //retrieve invitation modal
        $invitedEmail = $invitation->email;
        $referralUser = $invitation->user;

        return view('frontend.auth.register', compact('invitedEmail', 'referralUser', 'token'))
            ->withSocialiteLinks((new SocialiteHelper)->getSocialLinks());
    }

    /**
     * @param RegisterRequest $request
     *
     * @return RedirectResponse|Redirector
     * @throws Throwable
     */
    public function register(RegisterRequest $request)
    {
        abort_unless(config('access.registration'), 404);

        $code = $request->iToken;
        $email = $request->email;
        if (Invite::isAllowed($code, $email)) {

            $invitation = Invite::get($request->token);
            $referralUser = $invitation->user;


            $officeOptions = OfficeOptions::where('office_id', $referralUser->office_id)->first();
            $user = $this->userRepository->create($request->only('first_name', 'last_name', 'email', 'phone_number', 'password'));
            $token = Str::random(80);
            $location = GeoIP::getLocation(request()->ip());
            $user->api_token = $token;
            $user->office_id = $referralUser->office_id;
            $user->last_login_ip = $request->ip();
            $user->timezone = $location->timezone;
            $user->save();

            $office = Office::where('id', $user->office_id)->with('Market')->first();
            $user->syncRoles([$officeOptions->default_role]);


            switch ($officeOptions->default_role) {
                case 'canvasser':
                    $positionId = 1;
                    break;
                case 'sp1':
                    $positionId = 2;
                    break;
                case 'sales rep':
                    $positionId = 5;
                    break;
                case 'integrations':
                    $positionId = 4;
                    break;
                case 'opener':
                    $positionId = 1;
                    break;
                default:
                    $positionId = 1;
                    break;
            }

            $position = new UserHasPosition();
            $position->user_id = $user->id;
            $position->position_id = $positionId;
            $position->save();

            if (isset($officeOptions->permissions)) {
                $officePermissions = $officeOptions->permissions;
            } else {
                $officePermissions = [];
            }
            if (isset($office->market->permissions)) {
                $marketPermissions = $office->market->permissions;
            } else {
                $marketPermissions = [];
            }


            $array = array_merge($marketPermissions, $officePermissions);
            $user->syncPermissions($array);

            // Register this user
            Invite::consume($code);
            auth()->login($user);
//            event(new UserRegistered($user));

            return redirect()->route('dashboard.dashboard')->withFlashSuccess('Welcome, You got this!');
        } else {
            return redirect()->back();
            // either refCode is inavalid, or provided email was not invited against this refCode
        }


        // adds a user to their Office


        // If the user must confirm their email or their account requires approval,
        // create the account but don't log them in.
//		if (config('access.users.confirm_email') || config('access.users.requires_approval')) {
//			event(new UserRegistered($user));
//
//			return redirect($this->redirectPath())->withFlashSuccess(
//				config('access.users.requires_approval') ?
//					__('exceptions.frontend.auth.confirmation.created_pending') :
//					__('exceptions.frontend.auth.confirmation.created_confirm')
//			);
//		}


    }

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    public function redirectPath()
    {
        return route(home_route());
    }
}
