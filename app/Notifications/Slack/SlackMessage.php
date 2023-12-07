<?php

namespace App\Notifications\Slack;

use Illuminate\Support\Facades\Http;

class SlackMessage
{
    protected string $message = '';
    protected $fallback = null;
    protected $pretext = null;
    protected $color = null;
    protected array $fields;

    /**
     * @param $message
     */
    public function __construct($message, $options)
    {
        $this->message = $message;
        $this->fallback = $options['fallback'] ?? null;
        $this->pretext = $options['pretext'] ?? null;
        $this->color = $options['color'] ?? null;
        $this->fields = $options['fields'] ?? [];
    }

    /**
     * Returns array of options.
     *      fallback: Required text summary of the attachment that is shown by clients that understand attachments but choose not to show them.
     *      pretext: Optional text that should appear above the formatted data.
     *      color: Can either be one of 'good', 'warning', 'danger', or any hex color code.
     *      * Fields are displayed in a table on the message
     *      fields needs to be formatted like so:
     *          fields => [
     *          title => The title may not contain markup and will be escaped for you.
     *          value => Text value of the field. May contain standard message markup and must be escaped as normal. May be multi-line.
     *          short => Optional flag indicating whether the `value` is short enough to be displayed side-by-side with other values.
     *          ]
     * @return array
     */
    private function options(): array
    {
        return [
            "text" => $this->message,
            "fallback" => $this->fallback,
            "pretext" => $this->pretext,
            "color" => $this->color,
            "fields" => $this->fields
        ];
    }

    public function send($channel)
    {
        \Log::info('Sending Slack Message Via WebHook...', [$this->options()]);
        try {
            return Http::post($channel, $this->options());
        } catch (\Exception $e) {
            \Log::error("Send Slack Message Error: ", [$e]);
            return $e;
        }
    }
}
