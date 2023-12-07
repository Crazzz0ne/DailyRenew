<?php

namespace App\Notifications\Slack;

class SlackLink
{
    protected String $url = '';
    protected String $text = '';

    public function __construct($url, $text)
    {
         $this->url = $url;
         $this->text = $text;

         return $this->__toString();
    }

    public function __toString(){
        return "<{$this->url}| {$this->text}>";
    }
}
