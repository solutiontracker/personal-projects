<?php

namespace App\Listeners\Mobile;

use App\Events\Mobile\Event;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

class Listener
{
    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public $connection = 'database';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'mobile';

    public $tries = 3;

    /**
     * @param Event $event
     */

    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param mixed $request
     *
     * @return [type]
     */
    public function AttendeeActivityInstaller($request)
    {
        \App\Models\AttendeeActivityLog::create([
            'user_id' => $request['user_id'],
            'event_id' => $request['event_id'],
            'ip' => $request['ip'],
            'browser' => $request['browser'],
            'os' => $request['os'],
            'platform' => $request['platform'],
            'history_type' => $request['history_type'],
        ]);
    }

    /**
     * @param mixed $request
     *
     * @return [type]
     */
    public function AttendeeLoginHistoryInstaller($request)
    {
        \App\Models\LoginHistory::create([
            'attendee_id' => $request['attendee_id'],
            'event_id' => $request['event_id'],
            'platform' => $request['platform'],
            'browser' => $request['browser'],
            'ip' => $request['ip'],
            'user_agent' => $request['user_agent'],
            'history_type' => $request['history_type'],
        ]);

        return;
    }

    /**
     * @param mixed $request
     * 
     * @return [type]
     */
    public function smsHistoryInstaller($request)
    {
        \App\Models\EventSmsHistory::create([
            'organizer_id' => $request['organizer_id'],
            'attendee_id' => $request['attendee_id'],
            'event_id' => $request['event_id'],
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'status' => $request['status']['status'],
            'status_msg' => $request['status']['status_msg'],
            'sms' => $request['sms'],
            'type' => $request['type'],
            'date_sent' => \Carbon\Carbon::now(),
        ]);

        return;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            Event::AttendeeActivityInstaller,
            'App\Listeners\Mobile\Listener@AttendeeActivityInstaller'
        );

        $events->listen(
            Event::AttendeeLoginHistoryInstaller,
            'App\Listeners\Mobile\Listener@AttendeeLoginHistoryInstaller'
        );

        $events->listen(
            Event::smsHistoryInstaller,
            'App\Listeners\Mobile\Listener@smsHistoryInstaller'
        );
    }

    /**
     * @return [type]
     */
    public function failed()
    {
        // Need to handle there
    }
}
