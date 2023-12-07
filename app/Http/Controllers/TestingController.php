<?php

namespace App\Http\Controllers;



class TestingController extends Controller
{
    public function sendSlack()
    {
//        //Unformatted Data
//        $data = [
//            'keys' => [1, 2, 3, 10],
//            'values' => ['One', 'Two', 'Three', 'Ten'],
//            'shorts' => [true, true, false, true],
//        ];
//
//        //Formatted Data

        $appointment = Appointment::all()->random();

        $appointmentType = ucfirst($appointment->type->name);
        $appointmentDate = Carbon::parse($appointment->start_time);

        $options = [
            'color' => 'good',
            'fields' => [
                [
                    'title' => 'Congratulations!',
                    'value' => "{$appointment->user->name} has just booked an appointment for {$appointment->lead->customer->name}, on {$appointmentDate->toDateString()} for {$appointmentType}!",
                    'short' => false,
                ]
            ],
        ];

        Slack::compose("", config('slack.channels.testing'), $options);
    }

    public function getClient()
    {


        $client = new Google_Client();
        $client->setApplicationName('Scout');
        $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
        $client->setAuthConfig(storage_path('app/google/service-account-credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri(env('GOOGLE_REDIRECT'));
        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = storage_path('app/google-calendar/service-account-credentials.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

    public function sendMail()
    {

// Get the API client and construct the service object.


        /************************************************
         * Make an API request authenticated with a service
         * account.
         ************************************************/
        $googleAuth = new GoogleOAuth2();


        $client = $googleAuth->getClient('chris.furman@solcalenergy.com');

        $events = $googleAuth->getFutureCalendar($client);
        foreach ($events->getItems() as $event) {
            if ($event->start->dateTime) {
                dd($event);
                return dd($event->start->dateTime);
            }


        }
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
        dd($this->checkServiceAccountCredentialsFile());
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
        $client->setSubject('chris.furman@solcalenergy.com');

        $service = new Google_Service_Calendar($client);
//         dd($service->calendarList->get('chris.furman@solcalenergy.com'));
// Print the next 10 events on the user's calendar.
        $calendarId = 'chris.furman@solcalenergy.com';
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        );
        $results = $service->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();

        return collect($events);

        if (empty($events)) {
            print "No upcoming events found.\n";
        } else {
            print "Upcoming events:\n";
            foreach ($events as $event) {
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                }
                printf("%s (%s)\n", $event->getSummary(), $start);
            }
        }

        /************************************************
         * We're just going to make the same call as in the
         * simple query as an example.
         ************************************************/
        $query = 'Henry David Thoreau';
        $optParams = array(
            'filter' => 'free-ebooks',
        );
    }

    function checkServiceAccountCredentialsFile()
    {
        // service account creds
        $application_creds = storage_path('app/google/service-account-credentials.json');

        return file_exists($application_creds) ? $application_creds : false;
    }

    function missingServiceAccountDetailsWarning()
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

