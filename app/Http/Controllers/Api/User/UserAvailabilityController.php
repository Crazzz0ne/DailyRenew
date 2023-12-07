<?php


namespace App\Http\Controllers\Api\User;


use App\Http\Controllers\Controller;
use App\Http\Resources\AvailabilityResource;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Availability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserAvailabilityController extends Controller
{

    public function index(Request $request, User $user)
    {
        return AvailabilityResource::collection(Availability::where('user_id', $user->id)
            ->where('end', '>', Carbon::now()->timezone('America/Los_Angeles'))
            ->orderBy('start', 'asc')
            ->get());
    }


    public function store(Request $request, User $user)
    {
        $startTime = Carbon::createFromFormat('d-m-Y h:i A', $request->startTime, $user->timezone);
        $startTime->setTimezone('UTC');

        $endTime = Carbon::createFromFormat('d-m-Y h:i A', $request->endTime, $user->timezone);
        $endTime->setTimezone('UTC');


//        return $user;
        $availability = Availability::create([
            'user_id' => $user->id,
            'start' => $startTime,
            'type' => $request->type,
            'end' => $endTime,
            'approved' => true]);

        return new AvailabilityResource($availability);
    }

    public function delete(User $user, Availability $availability)
    {
        $availability->delete();
    }
}
