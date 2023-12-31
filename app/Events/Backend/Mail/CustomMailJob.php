<?php

namespace App\Events\Backend\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CustomMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $configuration;
    public $to;
    public $mailable;

    /**
     * Create a new job instance.
     *
     * @param array $configuration
     * @param string $to
     * @param Mailable $mailable
     */
    public function __construct(array $configuration, string $to, Mailable $mailable)
    {
        $this->configuration = $configuration;
        $this->to = $to;
        $this->mailable = $mailable;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailer = app()->makeWith('user.mailer', $this->configuration);
       $mailer->to($this->to)->send($this->mailable);
        Log::info('yuasjdsssf');
    }
}
