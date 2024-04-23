<?php

namespace App\Console;
use App\Models\Event;
use Illuminate\Support\Facades\Log;
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
        $schedule->call(function () {
            // Retrieve all events that have ended
            $endedEvents = Event::where('end_time', '<=', now())->where('status', 'active')->get();

            // End each event
            foreach ($endedEvents as $event) {
                $event->endEvent();
                Log::info('Event ended: ' . $event->title);
            }
        })->everyMinute(); // You can adjust the frequency as needed
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
