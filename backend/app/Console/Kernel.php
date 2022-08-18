<?php

namespace App\Console;

use App\Http\Controllers\Api\HistListCustomController;
use App\Http\Controllers\Api\SftpClients;
use App\Http\Controllers\Api\smsController;
use App\Http\Controllers\Api\SmsSendingProgramController;
use App\Http\Controllers\LibraryController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Clear record when event date is less than today
        $schedule->command('sms:clearhash')->everyMinute();

        // Seeks customer response by sending sms
        $schedule->call(function () {
            //$smsController = new smsController;
            //$smsController->responseKolmeya();
        })->everyTenMinutes()->name('responseKolmeya');

        // Send report to customer via sftp
        $schedule->call(function() {
            // LibraryController::uploadSSH(); // Modelo de envio antigo
            // LibraryController::newUploadSSH(); // Modelo de envio set envio igual a 1 (tabela record_sended_ml_gomes)
            //LibraryController::recentUploadSSH();
        })->daily()->at('06:00');

        // Update table hist_list_custom
        $schedule->call(function() {
            HistListCustomController::histdash();
        })->everyTenMinutes();

        // Generate database backup daily
        $schedule->command('database:backup')->daily()->at('23:30');

        // automatic import process
        $schedule->call(function () {
            //$SftpClients = new SftpClients();
            //$SftpClients->importMailingLocalCred();
        })->everyTenMinutes()->name('importMailingLocalCred');

        // automatic send reports process
        $schedule->call(function () {
           // $SftpClients = new SftpClients();
           // $SftpClients->exportReportsLocalCred();
        })->daily()->at('18:00')->name('exportReportsLocalCred');

        // update id_send_sms talkip block
        $schedule->call(function () {
            $library = new LibraryController();
            $library->justSmsUpdateIdSendSms();
        })->everyTwoMinutes()->name('justSmsUpdateIdSendSms');

        // Delete data MIgration Process
        $schedule->call(function () {
            $library = new LibraryController();
            $library->deleteMailingProcess();
        })->daily()->at('07:00')->name('deleteMailingProcess');

        // Send sms programmed
        $schedule->call(function () {
            $smsProgram = new SmsSendingProgramController();
            $smsProgram->sendSmsProgrammed();
        })->everyMinute()->name('sendSmsProgrammed');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
