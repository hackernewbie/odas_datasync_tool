<?php

namespace App\Console;

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
        // $schedule->command('inspire')->hourly();
        //$schedule->call('App\Http\Controllers\FacilityController@GetFacilities')->everyMinute();

        //$schedule->call('App\Http\Controllers\OxygenDataController@FetchOxygenData')->everyMinute();
        $schedule->call('App\Http\Controllers\BulkUpdatesController@BulkUpdateFacilityO2Infra')->everyMinute();

        // $schedule->call('App\Http\Controllers\BulkUpdatesController@BulkUpdateFacilityO2Infra')
        //                         ->dailyAt('19:46')
        //                         ->timezone('Asia/Kolkata');
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
