<?php

namespace App\Http\Controllers\Api\CallCenter;

use App\Events\Backend\CallCenter\CallCenterEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;

class RehashFilesController extends Controller
{

    public function store(Request $request)
    {
        $propStream = $request->file('propStream');
        $xenCall = $request->file('xenCall');
        $propStreamDataArray = array();
        $i = 0;
        if (($handle = fopen($propStream, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 30000, ",")) !== FALSE) {
                foreach ($data as $key => $value) {
                    $data[$key] = trim($value);;
                }

                $propStreamDataArray[$i] = $data;
                $i++;
            }
        }

        $xenCallDataArray = array();
        $i = 0;
        if (($handle = fopen($xenCall, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 30000, ",")) !== FALSE) {
                foreach ($data as $key => $value) {
                    $data[$key] = trim($value);;
                }
                $xenCallDataArray[$i] = $data;
                $i++;
            }
        }
        $propStreamDataArray = collect($propStreamDataArray);
        $xenCallDataArray = collect($xenCallDataArray);
        event(new CallCenterEvent($xenCallDataArray, $propStreamDataArray));
        dump(count($xenCallDataArray), count($propStreamDataArray));
        return 'yes';
//        ini_set('max_execution_time', 99999);
//        $propStream = $request->file('propStream');
//
//
//        $xenCall = $request->file('xenCall');
//        //Open the file once
//        $propStreamDataArray = array();
//        $i = 0;
//        if (($handle = fopen($propStream, 'r')) !== FALSE) {
//            while (($data = fgetcsv($handle, 10000000, ",")) !== FALSE) {
//
//                $propStreamDataArray[$i] = $data;
//                $i++;
//            }
//        }
//
//        $propStream = collect($propStreamDataArray);
//        $row = 0;
//        $z = 0;
//        $propStreamArray = array();
//        if (($handle = fopen($xenCall, 'r')) !== FALSE) {
//            while (($data = fgetcsv($handle, 20000, ",")) !== FALSE) {
//                $newNumber = null;
//                if ($row == 0) {
//                    $row++;
//                    continue;
//                }
////dump($data[0]);
////                echo "<p> $num fields in line $row: <br /></p>\n";
//                if ($data[15] === 'wrong info') {
//
//                    $newNumber = $this->findNextContact($propStream, $data[0]);
//                }
//
//                if ($data['20'] > 8 && $data['15'] === 'Not yet reached') {
//
//                    $newNumber = $this->findNextContact($propStream, $data[0]);
//
//
//                }
//
//                if ($newNumber) {
////                    return 'here';
//
////                    array_shift($data);
////                       array_unshift($data, $newNumber);
//                    $data[0] = $newNumber;
//                    $propStreamArray[$row] = $data;
//                }
//                $z++;
//                $row++;
//                if ($z == 2000){
//
//                    return $propStreamArray;
//                }
//
//            }
//
//            fclose($handle);
//        }
//
//        $headers = [
//            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
//            , 'Content-type' => 'text/csv'
//
//            , 'Content-Disposition' => 'attachment; filename=galleries.csv'
//            , 'Expires' => '0'
//            , 'Pragma' => 'public'
//        ];
//        $callback = function () use ($propStreamArray) {
//            $file = fopen('php://output', 'w');
//            $columns = array('Phone', 'Alt. Phone', 'Company', 'First Name', 'Last Name', 'Address City', 'State', 'Country', 'Zip', 'Email',
//                'Batch Name', 'Original File Name');
//            fputcsv($file, $columns);
//            foreach ($propStreamArray as $key => $data) {
//                fputcsv($file, $data);
//            }
//            fclose($file);
//        };
//
//        return response()->stream($callback, 200, $headers);


    }

    function findNextContact($propStream, $xencall)
    {

//        if (strlen($xencall) == 0){
//            dd('getting zero');
//            return 0;
//        }
        foreach ($propStream as $key => $value) {

            if ($key == 0) {
                continue;
            }
//            if ($xencall == '8057013165 ') {
//                dump($key, $value);
//                dd($value);
//                die();
//            }
            $rowKey = array_search($xencall, $value);

            if ($rowKey) {

                $nextKey = $rowKey + 1;
//                if ($xencall == '8057013165 ') {
//                    dd($rowKey);
//                }
//              dump($value[(int)$nextKey].' value ', $xencall.' xencall', $nextKey.' next key', $value);
//dump($value[44]);

                if (strlen($value[$nextKey]) == 10) {
//                    dd($value[$nextKey]);
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
            }
        }
    }


}
