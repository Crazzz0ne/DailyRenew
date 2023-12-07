<?php

namespace App\Console;


use App\Console\Commands\changeOfficeSp2;

use App\Console\Commands\Crons\ChangeAPIKeyCommand;
use App\Console\Commands\Crons\CronAppointmentReminder;
use App\Console\Commands\Crons\CronReminderToUpdateScout;
use App\Console\Commands\Crons\FollowUps\NotClosedAfterAppointment;
use App\Console\Commands\Crons\MakeThemSit;
use App\Console\Commands\Crons\Payroll\CallCenterWeeklyPayroll;
use App\Console\Commands\Crons\Payroll\CreateBiWeeklyPayroll;
use App\Console\Commands\Crons\RoundRobin\CronCallCenterRoundRobin;
use App\Console\Commands\Crons\RoundRobin\GoBack;
use App\Console\Commands\Crons\RoundRobin\UpdateAvalablity;
use App\Console\Commands\Crons\SendBatchedJobs;
use App\Console\Commands\Crons\TwilioOptOut;
use App\Console\Commands\Crons\ZapCommission;
use App\Console\Commands\IdeHelperHandler;
use App\Console\Commands\CreditPassIfStatus;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Your previous registered commands...
        IdeHelperHandler::class,
//        CronTextInvite::class,
//        CronReleaseLeadEdit::class,
        GoBack::class,
        CreditPassIfStatus::class,
        ZapCommission::class,
        CronAppointmentReminder::class,
        TwilioOptOut::class,
        changeOfficeSp2::class,
        CronReminderToUpdateScout::class,
        CronCallCenterRoundRobin::class,
        MakeThemSit::class,
        CreateBiWeeklyPayroll::class,
        NotClosedAfterAppointment::class,
        UpdateAvalablity::class,
        ChangeAPIKeyCommand::class,
        CallCenterWeeklyPayroll::class,
        SendBatchedJobs::class,


    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('command:cronAppointmentReminder')->everyMinute();
        $schedule->command('command:cronAppointmentFollowUp')->everyMinute();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
//        $schedule->command('command:callCenterAssign')->timezone('America/los_angeles')->everyFifteenMinutes()->between('8:00', '21:00');
        $schedule->command('command:twilioOptOut')->quarterly();
//        $schedule->command('command:makeThemSit')->dailyAt(2)->timezone('America/los_angeles');
//        $schedule->command('command:emailNotClosedOne')->dailyAt(16)->timezone('America/los_angeles');
        $schedule->command('command:requestUpdate')->mondays()->at(13)->timezone('America/los_angeles');
        $schedule->command('command:syncCompleteSiteSurvey')->hourly()->timezone('America/los_angeles')->between('8:00', '21:00');
        $schedule->command('command:clearOldNotifications')->daily();

        $schedule->command('command:sendBatchedJobs')->wednesdays()->at(23)->timezone('America/los_angeles');
        $schedule->command('command:sendBatchedJobs')->mondays()->at(23)->timezone('America/los_angeles');
        $schedule->command('command:sendBatchedJobs')->fridays()->at(23)->timezone('America/los_angeles');
//        $schedule->command('command:WeeklyPayroll')->fridays()->at(1)->timezone('America/los_angeles');
//        $schedule->command('command:goBack')->everyFifteenMinutes();
//        $schedule->command('command:sendMassText30')->dailyAt('13:30')->timezone('America/los_angeles');
//        $schedule->command('command:mailChimpMassAdd')->dailyAt('13:35')->timezone('America/los_angeles');
//        $schedule->command('command:sendMassText30')->dailyAt('20:00')->timezone('America/los_angeles');
//        $schedule->command('command:sendCommZap')->days('Thursday')->at('01:00')->timezone('America/los_angeles');

        $this->backupDatabase($schedule);

    }

    /**
     * Run commands pertaining to database backups using https://github.com/spatie/laravel-backup
     * @param Schedule $schedule
     */
    private function backupDatabase(Schedule $schedule)
    {
        $schedule
            ->command('backup:run')->dailyAt('9:00')
            ->onFailure(function () {
                \Log::critical('Backup has to run!');
            })
            ->onSuccess(function () {
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

}
