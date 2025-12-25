<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('instances:remove-outdated')
            ->everyHour()
            ->before(function () {
                Log::info('SCHEDULED_STARTED_REMOVE_OUTDATED_INSTANCES');
            })
            ->onSuccess(function () {
                Log::info('SCHEDULED_SUCCESS_REMOVE_OUTDATED_INSTANCES');
            })
            ->onFailure(function () {
                Log::error('SCHEDULED_FAILED_REMOVE_OUTDATED_INSTANCES');
            });
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
