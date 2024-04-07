<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;
use \App\Models\EventMeetingHistory as model;

class EventMeetingRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request, Model $model)
    {
        $this->request = $request;
        $this->model = $model;
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function publish($formInput)
    {
        $meeting = \App\Models\EventMeetingHistory::where('event_id', $formInput['event_id'])->where('attendee_id', $formInput['attendee_id'])->where('channel', $formInput['channel'])->first();
        if (!$meeting) {
            $meeting = \App\Models\EventMeetingHistory::create([
                "event_id" => $formInput['event_id'],
                "attendee_id" => $formInput['attendee_id'],
                "channel" => $formInput['channel'],
                "plateform" => $formInput['plateform'],
                "audio" => 1,
                "video" => 1,
                "share" => 0,
            ]);
        }

        return $meeting;
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function updateControl($formInput)
    {
        if (in_array($formInput['control'], ["handle-mic", "handle-vid", "handle-share"])) {
            $meeting = \App\Models\EventMeetingHistory::where('event_id', $formInput['event_id'])->where('attendee_id', $formInput['attendee_id'])->where('channel', $formInput['channel'])->first();
            if ($formInput['control'] == "handle-mic") {
                if ($meeting) {
                    $meeting->audio = $formInput["value"];
                    $meeting->save();
                }
            } else if ($formInput['control'] == "handle-vid") {
                if ($meeting) {
                    $meeting->video = $formInput["value"];
                    $meeting->save();
                }
            } else if ($formInput['control'] == "handle-share") {
                if ($meeting) {
                    $meeting->share = $formInput["value"];
                    $meeting->save();
                }
            }
        }
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function fetchMeeting($formInput)
    {
        return \App\Models\AgendaVideo::where('id', $formInput['video_id'])->first();
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function createChannel($formInput)
    {
        $channel = \App\Models\AgoraChannel::where('channel', $formInput['channel'])->first();
        if (!$channel) {
            \App\Models\AgoraChannel::create([
                "channel" => $formInput['channel'],
            ]);
        } else {
            $channel->updated_at = \Carbon\Carbon::now();
            $channel->save();
        }
    }
}
