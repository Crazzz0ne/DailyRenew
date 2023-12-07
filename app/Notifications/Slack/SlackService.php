<?php

namespace App\Notifications\Slack;

use Exception;
use Illuminate\Http\Client\Response;

class SlackService
{
    /**
     * Compose a new Slack message in a specific channel, with or without a link, and additional array of options.
     * @param $text
     * @param $channel
     * @param SlackLink|null $link
     * @param array|null $options
     * @return Exception|Response
     */
    public function compose($text, $channel, ?array $options = [])
    {
        return null;
        $message = new SlackMessage($text, $options);
        return $message->send($channel);
    }

    /**
     * Creates a new SlackLink and returns it as a formatted string.
     * @param $url
     * @param $text
     * @return SlackLink
     */
    public function link($url, $text): SlackLink
    {
        return new SlackLink($url, $text);
    }

    /**
     * Converts separated arrays into one formatted array and returns the new array.
     * This array should be used for the fields of an options array, which gets sent to slack for displaying 'table' data.
     * @param array $arrays
     * @return array
     */
    public function mapTableFieldsFromSeparateData(array $arrays): array
    {
        $final = [];

        for ($i = 0; $i < count($arrays[array_key_first($arrays)]); $i++) {
            $rows = [];
            foreach ($arrays as $array) {
//                \Log::info("Array: ", $array);
                for ($j = 0; $j < count($array); $j++) {
                    $field = [];
                    array_push($field, $array[$i]);
                }
                array_push($rows, $field);
            }
            $rows = array_merge(...$rows);
            array_push($final, $rows);
        }

        return array_map(function($index) {
            return [
                'title' => $index[0],
                'value' => $index[1],
                'short' => $index[2]
            ];
        }, $final);
    }
}
