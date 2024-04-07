<?php

namespace App\Console\Commands\Eventcenter;

use Illuminate\Console\Command;

use App\Models\Event;

class EventAttendeeCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:EventAttendeesCount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update event attenndees count';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $events = Event::where('end_date', '<', \Carbon\Carbon::now()->format('Y-m-d'))->whereNull('end_event_total_attendee_count')->withCount(['attendees'])->paginate(500);
        foreach($events as $event) {
            Event::where('id', $event->id)->update([
                "end_event_total_attendee_count" => ($event->attendees_count > 0 ? $event->attendees_count : 0)
            ]); 
        }
       
        $this->info('Update event attendee count successfully!');
    }
}
