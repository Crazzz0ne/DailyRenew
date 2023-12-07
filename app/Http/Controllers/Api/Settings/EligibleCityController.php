<?php


namespace App\Http\Controllers\Api\Settings;


use App\Events\Backend\Settings\EligibleCityAddedEvent;
use App\Http\Controllers\Controller;
use App\Models\Office\Office;
use Illuminate\Http\Request;
use Spatie\Tags\Tag;

class EligibleCityController extends Controller
{
    public function index()
    {
        $tags = Tag::getWithType('EligibleCity');
        $array = [];
        foreach ($tags as $tag) {
            array_push($array, ['label' => $tag->name, 'value' => $tag->name]);
        }
        return $array;
    }

    public function show(Office $office)
    {

        $tags = Tag::getWithType('EligibleCity');
        $array = [];
        foreach ($tags as $tag) {
            array_push($array, ['label' => $tag->name, 'value' => $tag->name]);
        }
        return $array;
    }

    public function store(Request $request)
    {

        $tag = Tag::findOrCreate($request->city, 'EligibleCity');
        event(new EligibleCityAddedEvent($tag));
        return 'good';
    }

    public function upload(Request $request)
    {
        $propStream = $request->file('file');
        if (($handle = fopen($propStream, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 30000, ",")) !== FALSE) {
                $tag = Tag::findOrCreate($data[0], 'EligibleCity');
                event(new EligibleCityAddedEvent($tag));
            }
        }

        return 'good';
    }
    public function delete(Request $request)
    {
        $tag = Tag::find($request->city);
        $tag->delete();
        return 'good';
    }

}
