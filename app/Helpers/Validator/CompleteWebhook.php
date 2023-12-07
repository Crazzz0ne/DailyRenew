<?php


namespace App\Helpers\Validator;


use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class CompleteWebhook implements SignatureValidator
{

    public function isValid(Request $request, WebhookConfig $config): bool
    {
         return   true;
    }
}
