<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('overtime:reset')->monthlyOn(1, '00:00');
        // $schedule->command('overtime:reset')->cron('0 0 15,30 * *');
        // $schedule->command('overtime:reset')->everyMinute();
        // $schedule->command('credits:add')->everyMinute();
        // $schedule->command('credits:add')->cron('0 0 15,30 * *');
        // $schedule->command('overtime:reset')->dailyAt('11:51');
        
        // Run the 'overtime:reset' command on the 10th and 25th of every month at midnight
        $schedule->command('overtime:reset')->monthlyOn(10, '00:00');
        $schedule->command('overtime:reset')->monthlyOn(25, '00:00');
    
        // Run the 'credits:add' command on the 10th and 25th of every month at midnight
        $schedule->command('credits:add')->monthlyOn(10, '00:00');
        $schedule->command('credits:add')->monthlyOn(25, '00:00');
        
        // Geofencing Expiration Command
        $schedule->command('geofencing:update-expired')->everyMinute(); 
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    
        // $this->command(OvertimeReset::class);
        require base_path('routes/console.php');
    }
}
