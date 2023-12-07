<?php

namespace App\Events\Backend\SalesFlow;


use App\Models\SalesFlow\Appointment\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Slack;

class AppointmentBookedEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The video instance.
     *
     * @var Appointment
     */
    public Appointment $appointment;

    /**
     * Create a new job instance.
     *
     * @param Appointment $appointment
     * @return void
     */

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $appointmentType = ucfirst($this->appointment->type->name);
        $appointmentDate = Carbon::parse($this->appointment->start_time);
    }

}
