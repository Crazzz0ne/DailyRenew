<?php

namespace App\Listeners\Backend\SalesFlow\Customer;

use App\Models\Auth\User;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Customer\CustomerMessages;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

class CustomerAppointmentListener implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {

        $customerMessageCount = CustomerMessages::where('customer_id', $event->customerId)->count();
        $customer = Customer::where('id', $event->customerId)->first();

        if ($customerMessageCount === 0) {

            if ($customer->language === 'english') {
                $optOut = '
            Reply stop to end communication';
            } else {
                $optOut = '
            Responder detener para finalizar la comunicación';
            }
        } else {
            $optOut = '';
        }
        $timezone = $this->timeZone($customer);

        $startTime = Carbon::parse($event->appointmentTime);
        $startTime->setTimezone($timezone);

        $rep = User::where('id', $event->repId)->first();
        if ($customer->language === 'english') {
            $body = 'Hello ' . $customer->first_name . ', thank you for setting an appointment with ' . $rep->first_name . '. We look forward to consulting you on your power needs. Your Appointment is on '
                . $startTime->locale('es_ES')->format('D M jS') . ' @ ' . $startTime->locale('es_ES')->format(' g:i a') . '. If you have any questions you can reply to this message.
                ' . $optOut;



        } else {
            $body = 'Hola ' . $customer->first_name . ', Gracias por agendar una cita con  ' . $rep->first_name . '. Esperamos poder consultarle sobre sus necesidades de energía. Su cita es el '
                . $startTime->locale('es_ES')->format('D M j g:i a') . '
                 ' . $optOut;
        }


        $options['body'] = $body;
        $options['MessagingServiceSid'] = config('services.twilio.messaging_service_sid');
        $message = null;
//        check if in production or not env('APP_ENV') === 'production'
        if (app()->environment('production')) {
            try {

                $client = new Client(config('services.twilio.account_sid'),
                    config('services.twilio.password'));
                $message = $client->messages->create($event->customerPhone, $options);

            } catch (RestException $e) {
                Log::warning($e);
            } catch (ConfigurationException $e) {
            }
        }


        if ($message) {

            $customerMessage = new CustomerMessages();
            $customerMessage->customer_id = $event->customerId;
            $customerMessage->body = $body;
            $customerMessage->name = "Scout";
            $customerMessage->message_sid = $message->sid;
            $customerMessage->save();

        }
    }

    function timeZone($customer)
    {
        switch ($customer->state) {
            case 'CA':
                $timeZone = 'America/Los_Angeles';
                break;
            case 'TX':
                $timeZone = 'America/Chicago';
                break;
            case 'FL':
                $timeZone = 'America/New_York';
                break;
            case 'NV':
                $timeZone = 'America/Los_Angeles';
                break;
            case 'NM':
                $timeZone = 'America/Denver';
                break;
            default:
                $timeZone = 'America/Chicago';
                break;
        }

        return $timeZone;
    }

}
