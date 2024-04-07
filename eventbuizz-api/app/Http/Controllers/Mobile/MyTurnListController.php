<?php

namespace App\Http\Controllers\Mobile;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Eventbuizz\Repositories\MyTurnListRepository;
use App\Eventbuizz\Repositories\ProgramRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyTurnListController extends Controller
{
    public $successStatus = 200;

    protected $programRepository;

    protected $eventSettingRepository;

    protected $myTurnListRepository;

    protected $attendeeRepository;

    /**
     * @param ProgramRepository $programRepository
     * @param EventSettingRepository $eventSettingRepository
     * @param MyTurnListRepository $myTurnListRepository
     * @param AttendeeRepository $attendeeRepository
     */
    public function __construct(ProgramRepository $programRepository, EventSettingRepository $eventSettingRepository, MyTurnListRepository $myTurnListRepository, AttendeeRepository $attendeeRepository)
    {
        $this->programRepository = $programRepository;
        $this->eventSettingRepository = $eventSettingRepository;
        $this->myTurnListRepository = $myTurnListRepository;
        $this->attendeeRepository = $attendeeRepository;
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function streamingActions(Request $request)
    {
        $agenda_id = ($request->agenda_id ? $request->agenda_id : '');
        $type = $request->type;
        $channel_type = $request->channel_type;
        $attendee_id = $request->attendee_id;
        $event = $request->event;
        $event_id = $event['id'];
        $channel_name = (isset($type) && $type == "external" ? "External-MyTurnList-$event_id-$attendee_id" : "MyTurnList-$event_id-$agenda_id-$attendee_id");
        if ($request->isMethod('POST') && $request->action == "send-chat-message") {
            $message = $request->message;
            $data = array(
                "event_id" => $event_id,
                "agenda_id" => $agenda_id,
                "attendee_id" => $attendee_id,
                "message" => $message,
                "sendBy" => 'attendee',
                "ChannelName" => $channel_name,
            );
            $chat = $this->myTurnListRepository->sendChatMessage($data);
            $chat = \View::make('admin.myturnlist.sections.streaming-projector-chat-message', compact('chat'))->render();

            //push stream data to socket
            $socket_channel_name = 'event-streaming-actions-' . $event_id . '-' . $attendee_id;
            $raw_data = array(
                "content" => $chat,
                "event_id" => $event_id,
                "attendee_id" => $attendee_id,
                "agenda_id" => $agenda_id,
                "action" => "send-chat-message",
                "channel_name" => $channel_name,
            );

            $data = [
                'event' => $socket_channel_name,
                'data' => [
                    'data_info' => json_encode($raw_data),
                ],
            ];

            \Redis::publish('event-buizz', json_encode($data));

            return response()->json([
                'success' => true,
                'data' => array(
                    "chat" => $chat,
                ),
            ], $this->successStatus);
        } else if ($request->isMethod('POST') && $request->action == "close-channel-connection") {
            if ($channel_type == "live") {
                $channel_name = $channel_name . '-Live';
            }

            //update viewer connection
            if ($type == "external") {
                $connection = $this->myTurnListRepository->speakerRequest($request->all());
                if ($connection) {
                    $connection->status = "stop";
                    $connection->save();
                }
            }

            //push stream data to socket
            $socket_channel_name = 'event-streaming-actions-' . $event_id . '-' . $attendee_id;
            $raw_data = array(
                "event_id" => $event_id,
                "attendee_id" => $attendee_id,
                "agenda_id" => $agenda_id,
                "action" => "close-channel-connection",
                "channel_name" => $channel_name,
            );

            $data = [
                'event' => $socket_channel_name,
                'data' => [
                    'data_info' => json_encode($raw_data),
                ],
            ];

            \Redis::publish('event-buizz', json_encode($data));

            if ($type == "external") {
                $data = [
                    'event' => 'event-streaming-actions-' . $event_id,
                    'data' => [
                        'data_info' => json_encode($raw_data),
                    ],
                ];

                \Redis::publish('event-buizz', json_encode($data));
            }

            return response()->json([
                'success' => true,
            ], $this->successStatus);

        } else if ($request->isMethod('POST') && $request->action == "connected-channel-connection") {
            //push stream data to socket
            $socket_channel_name = 'event-streaming-actions-' . $event_id;
            $raw_data = array(
                "event_id" => $event_id,
                "attendee_id" => $attendee_id,
                "agenda_id" => $agenda_id,
                "action" => "connected-channel-connection",
                "channel_name" => $channel_name,
            );

            $data = [
                'event' => $socket_channel_name,
                'data' => [
                    'data_info' => json_encode($raw_data),
                ],
            ];

            \Redis::publish('event-buizz', json_encode($data));

            //update viewer connection
            $connection = $this->myTurnListRepository->speakerRequest($request->all());
            if ($connection) {
                $connection->status = "live";
                $connection->save();
            }

            return response()->json([
                'success' => true,
            ], $this->successStatus);

        } else if ($request->isMethod('POST') && $request->action == "live-projector-reconnect-again") {
            //push stream data to socket
            $socket_channel_name = 'event-streaming-actions-' . $event_id . '-' . $attendee_id;
            $raw_data = array(
                "event_id" => $event_id,
                "attendee_id" => $attendee_id,
                "agenda_id" => $agenda_id,
                "action" => $request->action,
                "channel_name" => $channel_name,
            );

            $data = [
                'event' => $socket_channel_name,
                'data' => [
                    'data_info' => json_encode($raw_data),
                ],
            ];

            \Redis::publish('event-buizz', json_encode($data));

            return response()->json([
                'success' => true,
            ], $this->successStatus);
        }
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function speakingAttendee(Request $request)
    {
        $request->merge(['status' => 'inspeech']);
        $attendee = $this->myTurnListRepository->getTurnListData($request->all());
        return response()->json([
            'success' => true,
            'data' => array(
                "attendee" => $attendee,
            ),
        ], $this->successStatus);
    }
}
