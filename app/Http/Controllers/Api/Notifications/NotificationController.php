<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;


class NotificationController extends Controller {

    protected int $defaultCount = 100;

    public function count(User $user) {
        return count($user->unreadNotifications);
    }

    public function showAll(Request $request, User $user) {

        if($request->has('per_page'))
            $perPage = $request->input('per_page');
        else $perPage = $this->defaultCount;

        $paginator = Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', User::class)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        return response($paginator, 200);
    }

    public function read(Notification $notification) {
        $updated = $notification->update(['read_at' => Carbon::now()]);
        return response($updated, 200);
    }

    public function unread(Notification $notification) {
        $updated = $notification->update(['read_at' => null]);
        return response($updated, 200);
    }

    public function readAll(User $user) {
        $updated = Notification::where('notifiable_id', $user->id)->where('notifiable_type', User::class)->update(['read_at' => Carbon::now()]);

        return response($updated, 200);
    }

    public function unreadAll(User $user) {
        $updated = Notification::where('notifiable_id', $user->id)->where('notifiable_type', User::class)->update(['read_at' => null]);
        return response($updated, 200);
    }

    public function delete(Notification $notification) {
        $removed = $notification->delete();
        return response($removed, 200);
    }

    public function deleteAll(User $user) {
        $deleted = Notification::where('notifiable_id', $user->id)->where('notifiable_type', User::class)->delete();
        return response($deleted, 200);
    }
}
