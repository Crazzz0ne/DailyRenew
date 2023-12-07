<?php


namespace App\Http\Controllers\Webhook\Twilio;


use App\Events\Backend\SalesFlow\Customer\CustomerMessageEvent;
use App\Events\Backend\SalesFlow\Lead\Note\NewNoteEvent;
use App\Events\Backend\SalesFlow\LeadFileUploadEvent;
use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Http\Resources\Customer\CustomerMessageResource;
use App\Http\Resources\LeadUploadResource;
use App\Models\Auth\User;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Customer\CustomerMessages;
use App\Models\SalesFlow\Customer\CustomerMessagesImages;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\LeadUpload;
use App\Models\SalesFlow\Lead\UserHasLead;
use Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;
use Twilio\Rest\Client;
use Twilio\Twiml;
use Magyarjeti\MimeTypes\MimeTypeConverter;

use Twilio\TwiML\VoiceResponse;

class TwilioController
{

    /**
     * @var mixed
     */
//    private $account_sid;
    /**
     * @var mixed
     */
//    private $auth_token;

    /**
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function __construct()
    {
        // Twilio credentials
        $this->account_sid =  config('services.twilio.account_sid');
        $this->auth_token =config('services.twilio.password');

        //The twilio number you purchased
        $this->from = config('services.twilio.from');

        // Initialize the Programmable Voice API
//        $this->client = new Client($this->account_sid, $this->auth_token);
    }
    public function welcome()
    {

//        $twiml = new VoiceResponse();

//        return 'hello world';
        $phone_number = $_REQUEST['From'];
        $phone_number = substr($phone_number, 2);

        $leads = Lead::whereHas('customer', function ($q) use ($phone_number) {
            $q->where('cell_phone', $phone_number);
            $q->orWhere('home_phone', $phone_number);
        })->latest()->get();
        $response = new VoiceResponse();
        if ($leads->count() == 0) {
            $response->say('Sorry we are having a problem finding your number in our system, please call back from the number we texted you from.');
            $response->pause(['length' => 3]);
            $response->say('Sorry we are having a problem finding your number in our system, please call back from the number we texted you from.');
            $response->pause(['length' => 3]);
            $response->say('Sorry we are having a problem finding your number in our system, please call back from the number we texted you from.');
            $response->hangup();
            return $response;
        }


        if ($leads[0]->close_date) {
            $userHasLead = UserHasLead::where([
                'user_id' => 315,
            ])->first();
        } else {
            $userHasLead = UserHasLead::where([
                'lead_id' => $leads[0]->id,
                'position_id' => 3
            ])->first();

            if (!$userHasLead) {
                $userHasLead = UserHasLead::where([
                    'lead_id' => $leads[0]->id,
                    'position_id' => 2
                ])->first();

            }
        }
        $salesPerson = $userHasLead->user_id;

        $sp2 = User::where('id', $salesPerson)->first();

        $response->say('Connecting to ' . $sp2->first_name);
        $response->dial($sp2->phone_number, ['from' => '+18449453563']);
        $message = new CustomerMessages();

        $message->customer_id = $leads[0]->customer->id;
        $message->body = 'Called in';
        $message->name = $leads[0]->customer->fullName;
        $message->save();

        return $response;

    }

    public function message()
    {
        $converter = new MimeTypeConverter();
        $sms_body = request('Body');
        $from = substr(request('From'), 2);
        $customer = Customer::where('cell_phone', $from)->whereNull('deleted_at')->latest()->first();

        $message = CustomerMessages::create([
            'customer_id' => $customer->id,
            'body' => $sms_body ?? ' ',
            'name' => $customer->fullName,
        ]);

        $lead = Lead::where('customer_id', $customer->id)->first();
        $userHasLead = $this->getUserHasLead($lead);

        $this->handleMediaFiles($converter, $customer, $message, $lead);

        $note = $this->createLeadNote($lead, $customer, $sms_body);
        event(new NewNoteEvent($lead->id, $note->id, $customer->fullName . ':' . $sms_body, 1, 0));

        $this->notifyUser($userHasLead, $customer, $sms_body, $lead);

        $messageResource = new CustomerMessageResource(CustomerMessages::where('id', $message->id)->with('images')->first());
        event(new CustomerMessageEvent($messageResource, $customer->id));

        return null;
    }

    private function getUserHasLead($lead)
    {
        $userHasLead = $lead->close_date
            ? UserHasLead::where('user_id', 315)->first()
            : UserHasLead::where('lead_id', $lead->id)->whereIn('position_id', [3, 2])->first();

        return $userHasLead;
    }

    private function handleMediaFiles($converter, $customer, $message, $lead)
    {
        if (request('NumMedia')) {
            $NumMedia = (int)request('NumMedia');
            for ($i = 0; $i < $NumMedia; $i++) {
                $mediaUrl = request("MediaUrl$i");
                $MIMEType = request("MediaContentType$i");
                $fileExtension = $converter->toExtension($MIMEType);
                $mediaSid = basename($mediaUrl);
                $fileName = "$mediaSid.$fileExtension";
                $path = "customer/{$customer->id}/messages/{$fileName}";
                $this->handleImageFile($mediaUrl, $fileExtension, $path);

                $message->images()->create(['path' => $path]);
            }
        }
    }

    private function handlePdfFile($mediaUrl, $path, $lead)
    {
        $pdf = file_get_contents($mediaUrl);
        Storage::disk('s3')->put($path, $pdf);

        $upload = LeadUpload::create([
            'lead_id' => $lead->id,
            'user_id' => 1,
            'type' => 'bill',
            'path' => $path,
        ]);

        event(new LeadFileUploadEvent($lead->id, new LeadUploadResource($upload)));
    }

    private function handleImageFile($mediaUrl, $fileExtension, $path)
    {
        $image = Image::make($mediaUrl);
        Storage::disk('s3')->put($path, $image->encode());
    }

    private function createLeadNote($lead, $customer, $sms_body)
    {
        $note = LeadNote::create([
            'lead_id' => $lead->id,
            'user_id' => 1,
            'note' => $customer->fullName . ': ' . $sms_body,
        ]);
        broadcast(new LeadNewMessageEvent($lead->id, $note->id, $customer->fullName . ': ' . $sms_body, 1, 0));

        return $note;
    }

    private function notifyUser($userHasLead, $customer, $sms_body, $lead)
    {
        try {
            $user = User::findOrFail($userHasLead->user_id);
            $body = 'New message From ' . $customer->fullName . "\n" . $sms_body . "\nYou can respond @\n" . url('/') . '/dashboard/lead/' . $lead->id;
            TwilioSMSController::sendSms($user->phone_number, $body);
        } catch (\Exception $exception) {
            Log::error('Twilio controller', [$exception]);
        }
    }

    public function makeCall(Customer $customer)
    {


        $sid = config('services.twilio.account_sid');
        $mid = config('services.twilio.messaging_service_sid');
        $apiKey = config('services.twilio.password');
        $twilioNumber = $customer->twilio_number ?? '+18449453563';
        $customerNumber = $customer->cell_phone ?? $customer->home_phone;
        $response = new VoiceResponse();
        $response->say('Connecting to customer');
        $response->dial("+1" . $customerNumber);
        $client = new Client($sid, $apiKey);
        $call = $client->calls->create(
            "+1" . \Auth::user()->phone_number,
            $twilioNumber,
            array(
                "messagingServiceSid" => $mid,
                "url" => config('services.appUrl') . '/webhook/twilio/voice/call/customer/' . $customer->id,
            )
        );




        return response()->json(['message' => 'Call initiated']);
    }

    public function connectWithCustomer(Customer $customer): VoiceResponse
    {
        $response = new VoiceResponse();
        $response->say('Connecting to '.$customer->fullName.'. Please wait.');
        $response->dial("+1" . $customer->cell_phone);
        return $response;
    }


}
