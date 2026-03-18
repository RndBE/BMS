<?php

namespace App\Providers;

use App\Console\Commands\CheckAlertRulesCommand;
use App\Console\Commands\UpdateRoomStatusCommand;
use App\Models\Alert;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ── View Composer: share unread alert count ke semua layout ──
        View::composer('layouts.app', function ($view) {
            $unreadAlertCount = auth()->check()
                ? Alert::where('is_read', false)->count()
                : 0;
            $view->with('unreadAlertCount', $unreadAlertCount);
        });

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
