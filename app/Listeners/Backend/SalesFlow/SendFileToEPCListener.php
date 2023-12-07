<?php


namespace App\Listeners\Backend\SalesFlow;


use CURLFile;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class SendFileToEPCListener implements ShouldQueue
{

    public function handle($event)
    {

        if (env('APP_ENV') === 'production') {
            $url = 'https://heliotrack.completesolar.com/api/customers/documents';
        } else {
            $url = 'https://heliotrack.completesolar.biz/api/customers/documents';
        }
//        $client = new Client([
//            // Base URI is used with relative requests
//            'base_uri' => $url,
//            // You can set any number of default request options.
//            'timeout' => 500.0,
//        ]);

        switch ($event->upload->type) {
            case 'bill':
                $completeFileType = 'UTILITY_BILL';
                break;
            case '':

                break;
        }

        $storage = Storage::disk('s3')->get($event->upload->path);

        $file_content = $storage;
//        Log::critical($file_content);
//        $temp = fopen('php://temp', 'r+');
//        fwrite($temp, $storage);
//        Log::alert($event->lead->epc_owner_id);

        try {


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://heliotrack.completesolar.biz/api/customers/documents',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('uuid' => 'c77b08c9-c2cc-4482-93d3-d088dc41d7b1','type' => 'UTILITY_BILL','documents[]'=> new CURLFILE('/C:/Users/Chris/Pictures/sces/ads/03A.png')),
                CURLOPT_HTTPHEADER => array(
                    'x-api-key: m3IBkklzbb0v1JrrDD6jPmhPkFx6Snkh',
                    'Cookie: SERVERID=heliotrack_biz'
                ),
            ));

            $response = curl_exec($curl);

       curl_close($curl);
//            echo $response;
            Log::alert('Error upload to complete $e:' . $response);
            Log::alert('Error upload to complete json:' . json_encode([$response]));
//            dd($response);

        } catch (Exception $e) {
            $response = $e;

            Log::alert('Error upload to complete json:' . json_encode([$response]));

            Log::alert('Error upload to complete $e:' . $e);
            Log::alert('Error upload to complete [$e]:', [$e]);

        }

    }
}
