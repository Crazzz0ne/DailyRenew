<?php

namespace App\Http\Controllers;

use App\Mail\SalesFlow\BaseMailable;
use App\Models\Office\OfficeStanding;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use DateTime;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Spatie\Tags\Tag;

class HelperController extends Controller
{


    public static function email($subject, $body, $link, $rep, $linkTitle)
    {
        Mail::to($rep->email)
            ->queue(new BaseMailable($subject, $body, $link, $linkTitle));


        return 'Sms Sent';
    }
    // TODO Move this to the backend where it is being used
    public static function embedYoutube($url)
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        $youtube_id = $match[1];
        return 'https://www.youtube.com/embed/' . $youtube_id;
    }


    public static function averageWaitTime($arrayTime){
        $WaitTimeCalculated = [];
        foreach ($arrayTime as $key => $value){
            $startTime = Carbon::parse($value);
            $finishTime = Carbon::parse($key);
            array_push($WaitTimeCalculated, $finishTime->diffInSeconds($startTime) );

        }
        if (count($WaitTimeCalculated)){
     return   $intavg = array_sum($WaitTimeCalculated)/count($WaitTimeCalculated);
        }else{
            return null;
        }
    }

    public static function states()
    {
        return $state_list = array(
            'All' => 'All',
            'AL' => "Alabama",
            'AK' => "Alaska",
            'AZ' => "Arizona",
            'AR' => "Arkansas",
            'CA' => "California",
            'CO' => "Colorado",
            'CT' => "Connecticut",
            'DE' => "Delaware",
            'DC' => "District Of Columbia",
            'FL' => "Florida",
            'GA' => "Georgia",
            'HI' => "Hawaii",
            'ID' => "Idaho",
            'IL' => "Illinois",
            'IN' => "Indiana",
            'IA' => "Iowa",
            'KS' => "Kansas",
            'KY' => "Kentucky",
            'LA' => "Louisiana",
            'ME' => "Maine",
            'MD' => "Maryland",
            'MA' => "Massachusetts",
            'MI' => "Michigan",
            'MN' => "Minnesota",
            'MS' => "Mississippi",
            'MO' => "Missouri",
            'MT' => "Montana",
            'NE' => "Nebraska",
            'NV' => "Nevada",
            'NH' => "New Hampshire",
            'NJ' => "New Jersey",
            'NM' => "New Mexico",
            'NY' => "New York",
            'NC' => "North Carolina",
            'ND' => "North Dakota",
            'OH' => "Ohio",
            'OK' => "Oklahoma",
            'OR' => "Oregon",
            'PA' => "Pennsylvania",
            'RI' => "Rhode Island",
            'SC' => "South Carolina",
            'SD' => "South Dakota",
            'TN' => "Tennessee",
            'TX' => "Texas",
            'UT' => "Utah",
            'VT' => "Vermont",
            'VA' => "Virginia",
            'WA' => "Washington",
            'WV' => "West Virginia",
            'WI' => "Wisconsin",
            'WY' => "Wyoming");
    }

    public static function phone_number_format($number)
    {
        // Allow only Digits, remove all other characters.
        $number = preg_replace("/[^\d]/", "", $number);

        // get number length.
        $length = strlen($number);

        // if number = 10
        if ($length == 10) {
            $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "--", $number);
        } elseif ($length < 10) {
            $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "--", $number);
        }

        return $number;

    }

    public static function OfficeStandingMonth($currentMonth)
    {
        $months = [];


        $dt1 = Carbon::create($currentMonth)->firstOfYear()->toDateTimeLocalString();
        $dt2 = Carbon::create($currentMonth)->addMonth(12)->toDateTimeString();
        $office = OfficeStanding::whereBetween('sdate', [$dt1, $dt2])
            ->get();

        $officeMonth = $office->groupBy(function ($date) {
            return Carbon::parse($date->sdate)->format('m');
        });
//        return $officeMonth;
        foreach ($officeMonth as $key => $month) {
            $dateObj = DateTime::createFromFormat('!m', $key);
            $monthName = $dateObj->format('F');
            array_push($months, $monthName);
        }

        return $months;
    }

    public static function OfficeStandingYear()
    {
        $years = [];


        $office = OfficeStanding::where('id', '>', 0)
            ->get();

        $officeYear = $office->groupBy(function ($date) {
            return Carbon::parse($date->sdate)->format('Y');
        });
//        return $officeYear;
        foreach ($officeYear as $key => $month) {

            array_push($years, $key);
        }

        return $years;
    }

    public static function OfficeStandingMonthCarbon($currentYear, $currentMonth)
    {

        $dt1 = Carbon::createFromDate($currentYear, $currentMonth, '1')->firstOfMonth()->toDateTimeString();
        $dt2 = Carbon::createFromDate($currentYear, $currentMonth, '1')->endOfMonth()->toDateTimeString();
        $office = OfficeStanding::whereBetween('sdate', [$dt1, $dt2])
            ->get();

        return $office;
    }

    public static function stripedHtml($contents)
    {
        foreach ($contents as $content) {
            $content->description = strip_tags($content->description);
        }
        return $contents;

    }

    public static function sortArray($data, $field)
    {
        if (!is_array($field)) $field = array($field);
        usort($data, function ($a, $b) use ($field) {
            $retval = 0;
            foreach ($field as $fieldname) {
                if ($retval == 0) $retval = strnatcmp($a[$fieldname], $b[$fieldname]);
            }
            return $retval;
        });
        return $data;
    }

    public static function indexMonth($monthgroup)
    {
        foreach ($monthgroup as $key => $month) {
            $year = explode('-', $month[0]->sdate);

            $dateObj = DateTime::createFromFormat('!m', $key);
            $monthName = $dateObj->format('F');
            $key = $key . '/' . $year[0];
            $months[$key] = $monthName;
        }
        return $months;
    }

    public static function removeHtml($string)
    {

        $output = regex('^(?:(http[s]?|ftp[s]):\/\/)?([^:\/\s]+)(:[0-9]+)?((?:\/\w+)*\/)([\w\-\.]+[^#?\s]+)([^#\s]*)?(#[\w\-]+)?$', $string);
        return $output;
    }

    public static function addTags($request, $type)
    {
        $tagstring = $request->tags;
        $tags = explode(',', $tagstring);

        $tagArray = [];
        foreach ($tags as $tag) {
            array_push($tagArray, ltrim($tag));
        }

        Tag::findOrCreate($tagArray, $type);
        return $tagArray;
//        $content->attachTags($tagArray);
    }


    public static function key_compare_func($key1, $key2)
    {
        if ($key1 == $key2)
            return 0;
        else if ($key1 > $key2)
            return 1;
        else
            return -1;
    }

    public static function paginate(Collection $results, $pageSize)
    {
        $page = Paginator::resolveCurrentPage('page');

        $total = $results->count();

        return self::paginator($results->forPage($page, $pageSize), $total, $pageSize, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected static function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }



}

