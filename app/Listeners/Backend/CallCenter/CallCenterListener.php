<?php

namespace App\Listeners\Backend\CallCenter;

use App\Mail\SalesFlow\BaseMailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

//implements ShouldQueue
class CallCenterListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public $count;

    public function __construct($count = 0)
    {
        $this->count = $count;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(object $event)
    {
        ini_set('max_execution_time', 99999);
        $i = 0;


        $propStream = collect($event->propStream);
        $row = 0;
        $z = 0;
        $propStreamArray = array();
        $newArrayKey = 0;
        foreach ($event->readyMode->chunk(100) as $key => $data) {

            $newNumber = null;
            if ($row == 0) {
                $row++;
                continue;
            }


            if ($data[14] === 'wrong info') {
                $newNumber = $this->findNextContact($propStream, $data[0]);
//                dd($newNumber);
            }
//80
            if ($data[20] > 8 && $data[14] === 'Not yet reached') {
                $newNumber = $this->findNextContact($propStream, $data[0]);
            }

            if ($newNumber) {
                $data[0] = $newNumber;
                $propStreamArray[$newArrayKey] = $data;
                $newArrayKey++;
            }

            $row++;
//            if ($key > 50) {
//                break;
//            }

        }

//        dump('count');
//        dump($this->count);
////        $fileName = 'file.csv';
//        dump('hello world');
//        dd($propStreamArray);
        $file = fopen('test.csv', 'w');


        foreach ($propStreamArray as $key => $data) {
            fputcsv($file, $data);
        }
        fclose($file);


        Mail::to('chris.furman@solcalenergy.com')
            ->queue(new BaseMailable('city stuff', 'something', 'nowhere', 'lead', 'test.csv'));


    }

    function findNextContact($propStream, $xencall)
    {

//        if (strlen($xencall) == 0){
//            dd('getting zero');
//            return 0;
//        }
        $i = 0;
//        dump($xencall);
//        dd($propStream[4]);
        foreach ($propStream->chunk(100) as $key => $value) {
            $i++;
            if ($key == 0) {
                continue;
            }
            $this->count++;
            $rowKey = array_search($xencall, $value);
//            if ($xencall != '3234232991') {
//                dump($rowKey);
//                dump($value);
//
//            }

            if ($rowKey) {

//                echo $xencall.'<br>';
//                dump($xencall);
//                die();
                $nextKey = $rowKey + 1;
//                $value[$nextKey];
//                echo strlen($value[$nextKey]);
                if (strlen($value[$nextKey]) == 10) {
                    return $value[$nextKey];
                } else {

                    if ($rowKey >= 43 && $rowKey >= 66) {
                        if ($value[67]) {
                            return $value[67];
                        } else if ($rowKey >= 67 && $rowKey >= 79) {
                            return $value[15];
                        }
                    } else if ($rowKey >= 67 && $rowKey >= 79) {
                        return $value[15];
                    }
                    return 0;
                }
            } else {
                if ($rowKey >= 43 && $rowKey >= 66) {
                    if ($value[67]) {
                        return $value[67];
                    } else if ($rowKey >= 67 && $rowKey >= 79) {
                        return $value[15];
                    }
                } else if ($rowKey >= 67 && $rowKey >= 79) {
                    return $value[15];
                }

            }
        }
        return 0;

        dd($i);
    }
}
