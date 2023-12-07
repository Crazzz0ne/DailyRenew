<?php


namespace App\Handler\Webhook\Profiles;

use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class CompleteProcessProfile implements WebhookProfile
{

    function shouldProcess(Request $request): bool
    {

        if ($request->event_type === 'STATUS_UPDATE') {

            return true;
        }

        return false;

    }
}
