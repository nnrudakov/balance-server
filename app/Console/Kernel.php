<?php

declare(strict_types=1);

namespace App\Console;

use Storage;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SyncYandex;

/**
 * Class Kernel.
 *
 * @package    App\Console
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SyncYandex::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $this->scheduleSyncYandex($schedule);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        /** @noinspection PhpIncludeInspection */
        require base_path('routes/console.php');
    }

    /**
     * Schedule for synchronization between Yandex and Drebedengi.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    private function scheduleSyncYandex(Schedule $schedule): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $schedule->command(SyncYandex::class)
            ->description('Yandex synchronization')
            //->everyTenMinutes()
            ->everyMinute()
            ->sendOutputTo(Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . 'yandex.log')
            ->withoutOverlapping();
    }
}
