<?php

use App\Jobs\ArchiveDetteJob;
use App\Jobs\SendPaymentReminderJob;
use Illuminate\Foundation\Application;
use App\Jobs\SendWeeklyDebtNotificationsJob;
use App\Services\Interfaces\SmsServiceInterface;
use App\Http\Middleware\SenderResponseMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;  // Correct import


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SenderResponseMiddleware::class);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(new SendWeeklyDebtNotificationsJob(
            app(SmsServiceInterface::class)
        ))->weeklyOn(7, '00:00');
        $schedule->job(new SendPaymentReminderJob())->daily();
        $schedule->job(new ArchiveDetteJob())->dailyAt('00:00');
        // $schedule->job(new ArchiveDetteJob())->everyMinute();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
