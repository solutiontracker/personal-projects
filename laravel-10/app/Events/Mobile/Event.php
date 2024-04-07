<?php

namespace App\Events\Mobile;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\Request;

class Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    const AttendeeActivityInstaller = 'AttendeeActivityInstaller';
    const AttendeeLoginHistoryInstaller = 'AttendeeLoginHistoryInstaller';
    const smsHistoryInstaller = 'smsHistoryInstaller';

    private $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}
