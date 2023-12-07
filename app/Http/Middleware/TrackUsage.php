<?php


namespace App\Http\Middleware;

use App\Models\Auth\ApiTracking;
use Closure;
use Illuminate\Http\Request;
use App\Models\Office\ManagerEfficiency as ManagerEfficiencyModel;
use Carbon\Carbon;

class TrackUsage
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if($request->path() === 'api/salesflow/line' || $request->path() === 'api/salesflow/lead'){
            return $next($request);
        }

        $apiTracking = new ApiTracking();
        $apiTracking->user_id = \Auth::user()->id;
        $apiTracking->path =  $request->path();
        $apiTracking->save();

        return $next($request);
    }
}
