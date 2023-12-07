<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationNotFoundException extends Exception
{
    public function report()
    {
        Log::debug('Custom Notification Exception Triggered.');
    }

    public function render(Request $request, Exception $exception)
    {
        if($exception instanceof ModelNotFoundException){
            return response()->json([
                'message' => "Notification has already been deleted."
            ]);
        }
        return parent::render($request, $exception);
    }
}
