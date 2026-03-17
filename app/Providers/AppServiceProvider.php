<?php

namespace App\Providers;

use App\Console\Commands\CheckAlertRulesCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Jalankan pengecekan alert rules setiap 1 menit
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command(CheckAlertRulesCommand::class)->everyMinute()
                     ->withoutOverlapping()
                     ->runInBackground();
        });
    }
}
