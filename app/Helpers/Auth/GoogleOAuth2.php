<?php

namespace App\Helpers\Auth;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar\Event;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;


class GoogleOAuth2
{


    public function getClient($subject)
    {
        /************************************************
         * Make an API request authenticated with a service
         * account.
         ************************************************/

        $client = new Client();

        /************************************************
         * ATTENTION: Fill in these values, or make sure you
         * have set the GOOGLE_APPLICATION_CREDENTIALS
         * environment variable. You can get these credentials
         * by creating a new Service Account in the
         * API console. Be sure to store the key file
         * somewhere you can get to it - though in real
         * operations you'd want to make sure it wasn't
         * accessible from the webserver!
         * Make sure the Books API is enabled on this
         * account as well, or the call will fail.
         ************************************************/

        if ($credentials_file = $this->checkServiceAccountCredentialsFile()) {
            // set the location manually
            $client->setAuthConfig($credentials_file);
        } elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
            // use the application default credentials
            $client->useApplicationDefaultCredentials();
        } else {
            echo $this->missingServiceAccountDetailsWarning();
            return;
        }
        $client->setApplicationName("Scout");
        $client->setScopes(['https://mail.google.com/',
            'https://www.googleapis.com/auth/calendar']);
        $client->setSubject($subject);
        return $service = new Google_Service_Calendar($client);

    }

    public function createEvent($service, $start, $end, $location, $subject, $attendees, $calendarId)
    {
//return $start->timezone('America/Los_Angeles')->format('c');

        $event = new Event();
        $event->setSummary('Energy Savings Program');
        $calendarDateTimeStart = new \Google_Service_Calendar_EventDateTime();
        $calendarDateTimeStart->setDateTime($start->format('c'));
        $calendarDateTimeStart->setTimeZone('America/Los_Angeles');
        $calendarDateTimeEnd = new \Google_Service_Calendar_EventDateTime();
        $event->setLocation($location);
        $calendarDateTimeEnd->setDateTime($end->format('c'));
        $calendarDateTimeEnd->setTimeZone('America/Los_Angeles');
        $event->setStart($calendarDateTimeStart);
        $event->setEnd($calendarDateTimeEnd);
        $event->setDescription('A conversation to remove Carbon');
        $attendees = array(array('email' => $attendees));
        $event->setAttendees($attendees);
        $event->sendNotifications();


        $calendarId = 'chris.furman@solcalenergy.com';
        $event = $service->events->insert($calendarId, $event);
//        printf('Event created: %s\n', $event->htmlLink);
    }


    public function getFutureCalendar($service, $calendarId)
    {

//        2011-06-03T10:00:00-07:00


        $optParams = array(
            'maxResults' => 30,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
            'timeMax' => Carbon::now()->addWeeks(2)->timezone('America/los_angeles')->format('c')
        );


        $results = $service->events->listEvents($calendarId, $optParams);
        return $results;

    }

    private function checkServiceAccountCredentialsFile()
    {
        // service account creds
        $application_creds = storage_path('app/google/service-account-credentials.json');

        return file_exists($application_creds) ? $application_creds : false;
    }

    private function missingServiceAccountDetailsWarning(): string
    {
        $ret = "
    <h3 class='warn'>
      Warning: You need download your Service Account Credentials JSON from the
      <a href='http://developers.google.com/console'>Google API console</a>.
    </h3>
    <p>
      Once downloaded, move them into the root directory of this repository and
      rename them 'service-account-credentials.json'.
    </p>
    <p>
      In your application, you should set the GOOGLE_APPLICATION_CREDENTIALS environment variable
      as the path to this file, but in the context of this example we will do this for you.
    </p>";

        return $ret;
    }
}
