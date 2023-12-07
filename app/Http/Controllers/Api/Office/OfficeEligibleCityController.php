<?php


namespace App\Http\Controllers\Api\Office;


use App\Http\Controllers\Controller;
use App\Models\Office\Office;

use Illuminate\Http\Request;
use Spatie\Tags\Tag;

class OfficeEligibleCityController extends Controller
{

    public function index(Office $office)
    {
        $tags = Tag::getWithType('EligibleCity');
        $array = [];
        foreach ($tags as $tag) {
            array_push($array, ['label' => $tag->name, 'value' => $tag->name]);
        }
        sort($array);
        return $array;
    }


    public function masterList(){
        $tags = Tag::getWithType('EligibleCity');
        $array = [];
        foreach ($tags as $tag) {
            array_push($array, ['label' => $tag->name, 'value' => $tag->name]);
        }
        sort($array);
        return $array;
    }

    public function store(Office $office, Request $request)
    {

        $office->syncTagsWithType($request->city, 'EligibleCity');
        return $office->tagsWithType('EligibleCity');
        return 'good';
    }



}
