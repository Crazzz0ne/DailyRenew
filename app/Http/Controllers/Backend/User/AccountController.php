<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Office\Office;
use Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;


/**
 * Class AccountController.
 */
class AccountController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {

        $user = $office = User::where('id', auth()->id())->first();
         $officeUser = User::where('office_id', '=', $user->office_id)
             ->where('terminated',null)
            ->with('roles')
            ->get();
        $office = Office::where('id', $user->office_id)->get()->first();
//        $office = [];
//        $officeUser = [];
        return view('backend.user.account', compact('office', 'officeUser'));
    }
}
