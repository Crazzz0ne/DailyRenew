<?php

namespace App\Http\Controllers\Api\SalesFlow\Customer;

use App\Events\Backend\SalesFlow\Customer\CustomerMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerMessageResource;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Customer\CustomerMessages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

class CustomerMessageController extends Controller
{
    public function index(Customer $customer)
    {
        return CustomerMessageResource::collection(CustomerMessages::where('customer_id', $customer->id)->with('images')->get());
    }

    public function store(Request $request, Customer $customer)
    {
        if (!$request->body && !$request->images) {
            return true;
        }

        // Validate the incoming images
        $request->validate([
            'images.*' => 'mimes:jpeg,png,gif',
        ]);

        $images = $request->file('images');
        $user = \Auth::user();

        $customerMessage = new CustomerMessages();
        $customerMessage->body = $request->body;
        $customerMessage->name = $user->fullName;
        $customerMessage->customer_id = $customer->id;
        $customerMessage->save();

        $imagePaths = [];

        // Upload images from $request->images
        if ($images) {
            foreach ($request->images as $image) {
                try {
                    $uploadFile = UploadedFile::createFromBase($image);

                    $fileHash = str_replace('.' . $uploadFile->extension(), '', $uploadFile->hashName());
                    $fileName = $fileHash . '.' . $image->getClientOriginalExtension();

                    // Set the Content-Type metadata
                    $options = [
                        'ContentType' => $image->getMimeType(),
                        'ACL' => 'public-read',
                    ];

                    // Store the image in S3
                    $path = Storage::disk('s3')->putFileAs(
                        'customer/' . $customer->id . '/messages',
                        $uploadFile,
                        $fileName,
                        $options
                    );

                    // Generate a temporary URL that lasts for 24 hours
                    $url = Storage::disk('s3')->temporaryUrl(
                        $path,
                        now()->addDay()
                    );

                    $imagePaths[] = $url;

                    $customerMessage->images()->create(['path' => $path]);
                } catch (Throwable $e) {
                    $collection = [
                        'status' => 69,
                        'error' => $e,
                    ];
                    $payload = collect($collection);
                    return $payload;
                }
            }
        }

        $options['body'] = $request->body;
        $options['MessagingServiceSid'] = config('services.twilio.messaging_service_sid');
        if (count($imagePaths) > 0) {
            $options['mediaUrl'] = $imagePaths;
        }

        try {

            $client = new Client( config('services.twilio.account_sid'),
                config('services.twilio.password'));
            $message = $client->messages->create($customer->cell_phone, $options);

            $customerMessage->message_sid = $message->sid;
            $customerMessage->save();
        } catch (RestException $e) {
            Log::error('Twilio Error: ' . $e->getMessage());
        }


        $messageResource = new CustomerMessageResource($customerMessage);
        event(new CustomerMessageEvent($messageResource, $customer->id));

        return new CustomerMessageResource($customerMessage->with('images')->find($customerMessage->id));
    }


}
