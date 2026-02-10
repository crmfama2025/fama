<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:expire-agreements')
            ->daily() // Runs every day at midnight
            ->withoutOverlapping()
            ->before(function () {
                \Log::info('app:expire-agreements command started');
            })
            ->after(function () {
                \Log::info('app:expire-agreements command finished');
            });

        // $schedule->command('profit:update-monthly-pending')
        //     ->monthlyOn(1, '00:05')
        //     ->withoutOverlapping();
        $schedule->command('profit:update-monthly-pending')
            ->monthlyOn(1, '00:05')
            ->withoutOverlapping()
            ->before(function () {
                \Log::info('profit:update-monthly-pending command started');
            })
            ->after(function () {
                \Log::info('profit:update-monthly-pending command finished');
            });
        // $schedule->command('profit:update-monthly-pending')
        //     ->everyMinute()
        //     ->withoutOverlapping()
        //     ->before(function () {
        //         \Log::info('profit:update-monthly-pending command started');
        //     })
        //     ->after(function () {
        //         \Log::info('profit:update-monthly-pending command finished');
        //     });
        $schedule->command('app:create-agreements-from-contracts')
            ->everyMinute()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
