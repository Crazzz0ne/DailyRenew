<?php


namespace App\Http\Controllers\Backend\Line;


use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\UserHasLead;
use Illuminate\Support\Facades\Request;


class LineController extends Controller
{
    public function index()
    {

    }

    public function others(Line $line, Request $request)
    {

                return UserResource::collection(UserHasLead::where('lead_id', $line->lead->id)->where('position_id', 6)->with('user')->get()->pluck('user'));
    }
}
