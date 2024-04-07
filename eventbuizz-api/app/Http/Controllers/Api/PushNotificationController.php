<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendee;
use App\Models\EventAttendee;
use App\Models\EventChatMessage;
use App\Models\EventChatMessageReadState;
use App\Models\EventChatThread;
use App\Models\OrganizerAPNS;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function sendIosPushNotificaiton(Request $request)
    {
        $organizer_id     = $request->organizer_id;
        $device_token     = $request->device_token;
        $payload = json_encode($request->payload);
        $apns_handler = new \App\Helpers\IOS\APNS();
        $response = $apns_handler->send($organizer_id, $device_token, $payload);
        return [
            "response" => $response,
            "payload" => $payload,
            "organizer_id" => $organizer_id,
            "device_token" => $device_token,
        ];
    }

    public function sendIosChatPushNotificaiton(Request $request)
    {
        $data         = $request->data;
        $sender_id    = $request->sender_id;

        $data = json_decode($data, true);

        $receivers     = $data['receivers'];
        $receivers_ids = explode(',', $receivers);
        $event_id      = $data['event_id'];
        $message       = $data['message'];
        $thread_id     = $data['thread_id'];
        $organizer_id  = $data['organizer_id'];

        $sender = Attendee::find($sender_id);
        $responses = [];
        foreach ($receivers_ids as $receiver) {

            $event_attendee = EventAttendee::where('event_id', $event_id)->where('attendee_id', $receiver)->first();

            if (!$event_attendee || ($event_attendee->device_type != 'ios' && !$event_attendee->device_token)) {
                continue;
            }

            $unread_count = $this->getAttendeeThreadsUnreadCount($receiver, $event_id);

            $apns = OrganizerAPNS::all()->toArray();
            $key  = array_search($organizer_id, array_column($apns, 'organizer_id'));

            $payload = json_encode([
                'aps' => [
                    'badge' => $unread_count,
                    "alert" => [
                        "title" => $sender->first_name . ' ' . $sender->last_name,
                        "body"  => $message,
                    ]
                ],
                'custom data' => [
                    'text'         => $message,
                    'type'         => 'chat',
                    'detail'       => '',
                    'id'           => $thread_id,
                    'sender_name'  => $sender->first_name . ' ' . $sender->last_name,
                    'sender_image' => (isset($sender_detail->image)) ? url('/assets/attendees/' . $sender_detail->image) : ''
                ]
            ]);

            $apns_handler = new \App\Helpers\IOS\APNS();
            $responses[] = $apns_handler->send($organizer_id, $event_attendee->device_token, $payload);
        }
        return $responses;
    }

    public function getAttendeeThreadsUnreadCount($attendee_id, $event_id) : int
    {

        $unread = 0;
        $thread_ids = EventChatThread::where('event_id', $event_id)->whereHas('participants', function(Builder $query) use ($attendee_id, $event_id){
            $query->where('user_id', '!=', $attendee_id);
        })->pluck('id');

        if($thread_ids->count() > 0) {
            $message_id = EventChatMessage::whereIn('thread_id', $thread_ids)->where('sender_id', '!=', $attendee_id)->pluck('id');
            $read_state = EventChatMessageReadState::whereIn('message_id', $message_id)->where('user_id', $attendee_id)->get();

            $total_count  = $message_id->count();
            $read_message = $read_state->count();
            $unread       = $total_count - $read_message;
        }

        return $unread;
    }
}
