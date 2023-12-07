<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Exceptions\GeneralException;
use App\Helpers\Auth\AuthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\User\ManageUserRequest;
use App\Models\Auth\User;
use App\Models\Office\Office;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class UserAccessController.
 */
class UserAccessController extends Controller
{
    /**
     * @param ManageUserRequest $request
     * @param User $user
     *
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function loginAs(Request $request, User $user)
    {
//        debug log
        \Log::alert(\Auth::user()->first_name . ' '. \Auth::user()->last_name . ' ' . \Auth::user()->id .'Tried
        to login as ' . $user->first_name . ' ' . $user->last_name . ' ' . $user->id);
        if ($user->id === 1) {
            \Log::alert('Tried to log in as Admin' . \Auth::user());
            die();
        }


        // Overwrite who we're logging in as, if we're already logged in as someone else.
        if (session()->has('admin_user_id') && session()->has('temp_user_id')) {
            // Let's not try to login as ourselves.
            if ($request->user()->id === $user->id || (int)session()->get('admin_user_id') === $user->id) {
                throw new GeneralException('Do not try to login as yourself.');
            }
            if (\Auth::user()->isAdmin()) {

            } elseif (\Auth::user()->hasRole('regional manager')) {
                $userMarketId = Office::where('id', $user->office_id)->pluck('market_id')->first();
                $officeArray = Office::where('market_id', $userMarketId)->get()->pluck('id');
                if (in_array($user->office_id, $officeArray->toArray())) {

                } else {
                    \Log::alert('Tried to log in as another user' . \Auth::user());
                    return 'no';
                }


            } elseif (\Auth::user()->hasRole('manager')) {

                if (\Auth::user()->office_id === $user->office_id) {

                } else {
                    \Log::alert('Tried to log in as another user' . \Auth::user());
                    return 'no';
                }

            } else {
                \Log::alert('Tried to log in as another user' . \Auth::user());
                return 'no';
            }

            \Log::alert('Logged in as another User ' . \Auth::user());
            \Log::alert('Another User logged in as ' . $user);
            // Overwrite temp user ID.
            session(['temp_user_id' => $user->id]);

            // Login.
            auth()->loginUsingId($user->id);

            // Redirect.
            return redirect()->route(home_route());
        }

        resolve(AuthHelper::class)->flushTempSession();

        // Won't break, but don't let them "Login As" themselves
        if ($request->user()->id === $user->id) {
            throw new GeneralException('Do not try to login as yourself.');
        }

// Add new session variables
        session(['admin_user_id' => $request->user()->id]);
        session(['admin_user_name' => $request->user()->full_name]);
        session(['temp_user_id' => $user->id]);

// Login user
        auth()->loginUsingId($user->id);

// Redirect to frontend
        return redirect()->route(home_route());
    }
}
