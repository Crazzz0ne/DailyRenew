<?php


namespace App\Models\Webhooks;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;

class SalesRabbit  extends WebhookCall
{
    protected $table = 'webhook_calls';
    public $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'exception' => 'array',
    ];

    public static function storeWebhook(WebhookConfig $config, Request $request): WebhookCall
    {

        return self::create([
            'name' => $config->name,
            'payload' => collect($request->json()),
        ]);
    }

//    public function saveException(Ex $exception): SalesRabbit
//    {
//        $this->exception = [
//            'code' => $exception->getCode(),
//            'message' => $exception->getMessage(),
//            'trace' => $exception->getTraceAsString(),
//        ];
//
//        $this->save();
//
//        return $this;
//    }

    public function clearException()
    {
        $this->exception = null;

        $this->save();

        return $this;
    }
}
