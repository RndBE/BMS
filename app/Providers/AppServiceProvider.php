<?php

namespace App\Providers;

use App\Console\Commands\CheckAlertRulesCommand;
use App\Console\Commands\UpdateRoomStatusCommand;
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
            // Cek alert rules setiap 1 menit
            $schedule->command(CheckAlertRulesCommand::class)->everyMinute()
                     ->withoutOverlapping()
                     ->runInBackground();

            // Update status ruangan setiap 2 menit
            $schedule->command(UpdateRoomStatusCommand::class)->everyTwoMinutes()
                     ->withoutOverlapping()
                     ->runInBackground();
        });
    }
}
