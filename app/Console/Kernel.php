<?php

namespace App\Console;

use App\BankAccount;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Auth;

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
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Test method to see whether the scheduler is working
//        $schedule->call(function () {
//            $bank_account = new BankAccount();
//            $bank_account->name = 'Another one' . time();
//            $bank_account->user_id = 1;
//            $bank_account->save();
//        })->everyMinute();
        $schedule
            ->command('handle:scheduledpayments')
            ->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
