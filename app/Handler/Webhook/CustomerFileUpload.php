<?php


namespace App\Handler\Webhook;


use App\Events\Backend\SalesFlow\LeadFileUploadEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Http\Resources\LeadUploadResource;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadUpload;
use Illuminate\Support\Facades\Storage;
use Spatie\WebhookClient\ProcessWebhookJob;

class CustomerFileUpload extends ProcessWebhookJob
{

    public function handle()
    {

        $payload = $this->webhookCall->payload;

        $customer =         Customer::where('cell_phone', substr($payload['phone'], 2))->latest()->first();



        if (!$customer){
            $number = substr($payload['phone'], 2);

            event(new TextEvent('7023020492', 'Could not find phone number '.$number.' upload failed' ));
            return 'no';
        }

        $lead = Lead::where('customer_id', $customer->id)->first();

        $urls = $payload['Click here to upload your bill. *Remember, We\'ll need the 12- month useage graph, usually between pages 3-5'];
        foreach($urls as $url) {
            $goodUrl = str_replace('\\', '', $url);
            $contents = file_get_contents($goodUrl);
            $file_name = basename($url);
            $path = 'lead/' . $lead->id . '/bill/'.$file_name;
        Storage::disk('s3')->put($path, $contents, ['visibility' => 'private']);

            $upload = new LeadUpload();
            $upload->lead_id = $lead->id;
            $upload->user_id = 1;
            $upload->type = 'bill';
            $upload->size = 0;
            $upload->path = $path;
            $upload->save();
            $something = new LeadUploadResource($upload);
            event(new LeadFileUploadEvent($lead->id, $something));

        }
//        dump($path);
    }

}
