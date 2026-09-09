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

        // expire agreements daily comment enable after data entry
        // $schedule->command('app:expire-agreements')
        //     ->daily() // Runs every day at midnight
        //     ->withoutOverlapping()
        //     ->before(function () {
        //         \Log::info('app:expire-agreements command started');
        //     })
        //     ->after(function () {
        //         \Log::info('app:expire-agreements command finished');
        //     });


        // Contract expiry: once daily at 1:00 AM. enable after data entry
        // $schedule
        //     ->command('app:contract-expiry')
        //     ->dailyAt('01:00')
        //     ->withoutOverlapping()
        //     ->appendOutputTo(
        //         storage_path('logs/expire-contracts.log')
        //     );


        // Contract approval-status update: once daily at midnight.
        $schedule
            ->command('contracts:update-contract-approval-status')
            ->dailyAt('00:00')
            ->withoutOverlapping()
            ->appendOutputTo(
                storage_path('logs/approve-status-contracts.log')
            );


        // investment payout update monthly
        $schedule->command('profit:update-monthly-pending')
            ->monthlyOn(1, '00:05')
            ->withoutOverlapping()
            ->before(function () {
                \Log::info('profit:update-monthly-pending command started');
            })
            ->after(function () {
                \Log::info('profit:update-monthly-pending command finished');
            });



        // auto create ff agreements from contracts (now modified tenant side)
        // $schedule->command('app:create-agreements-from-contracts')
        //     ->everyMinute()
        //     ->withoutOverlapping();


        // Process queued PDF and default jobs.
        $schedule
            ->command(
                'queue:work --queue=pdfs,default ' .
                    '--stop-when-empty --tries=3 ' .
                    '--timeout=120 --max-time=300'
            )
            ->everyMinute()
            ->withoutOverlapping(10)
            ->runInBackground()
            ->appendOutputTo(
                storage_path('logs/queue-worker.log')
            );


        // auto renew profit records investment
        $schedule
            ->command('investments:process-auto-renewals')
            ->dailyAt('00:30')
            ->withoutOverlapping()
            ->appendOutputTo(
                storage_path('logs/investment-auto-renewals.log')
            );


        // $schedule->call(function () {

        //     $expiryMinutes = config('session.lifetime'); // e.g. 120

        //     $deleted = \App\Models\FcmToken::where('last_active_at', '<', now()->subMinutes($expiryMinutes))
        //         ->delete();
        //     \Log::info("FCM cleanup ran at " . now() . ". Deleted $deleted tokens.");
        // })->everyFiveMinutes();

        $schedule->call(function () {
            $expiryMinutes = (int) config('session.lifetime', 120);

            $deleted = \App\Models\FcmToken::query()
                ->where(
                    'last_active_at',
                    '<',
                    now()->subMinutes($expiryMinutes)
                )
                ->delete();

            \Log::info('FCM cleanup completed.', [
                'deleted_tokens' => $deleted,
            ]);
        })
            ->name('cleanup-expired-fcm-tokens')
            ->everyFiveMinutes()
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
