<?php
namespace App\Http\Controllers\Mobile\Agora;

use App\Eventbuizz\Repositories\EventMeetingRepository;
use App\Eventbuizz\Repositories\OrganizerRepository;
use App\Eventbuizz\Repositories\ProgramRepository;
use App\Http\Controllers\Controller;
use App\Libraries\Agora\RtcTokenBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class AgoraController extends Controller
{
    public $successStatus = 200;

    protected $organizerRepository;

    protected $eventMeetingRepository;

    protected $programRepository;

    /**
     * @param OrganizerRepository $organizerRepository
     * @param EventMeetingRepository $eventMeetingRepository
     * @param ProgramRepository $programRepository
     */
    public function __construct(OrganizerRepository $organizerRepository, EventMeetingRepository $eventMeetingRepository, ProgramRepository $programRepository)
    {
        $this->organizerRepository = $organizerRepository;
        $this->eventMeetingRepository = $eventMeetingRepository;
        $this->programRepository = $programRepository;
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function handleControl(Request $request, $event_url, $control)
    {
        if ($control == "meeting-publish") {
            $request->merge(["plateform" => "agora", "attendee_id" => $request->uid, "control" => $control]);
            $meeting = $this->eventMeetingRepository->publish($request->all());
            return response()->json([
                'success' => true,
                'data' => array(
                    "meeting" => $meeting,
                ),
            ], $this->successStatus);
        } else if (in_array($control, ["handle-screen-sharing", "start-live-streaming", "started-live-streaming", "failed-live-streaming", "stopped-live-streaming"])) {
            $socket_channel_name = 'event-streaming-common-actions-' . $request->event_id;

            $raw_data = array(
                "control" => $control,
                "uid" => $request->uid,
                "attendee_id" => $request->attendee_id,
            );

            $data = [
                'event' => $socket_channel_name,
                'data' => [
                    'data_info' => json_encode($raw_data),
                ],
            ];

            Redis::publish('event-buizz', json_encode($data));

            return response()->json([
                'uid' => $request->uid,
                'success' => true,
            ], $this->successStatus);

        } else if ($control == "close-streaming") {
            $socket_channel_name = 'event-streaming-common-actions-' . $request->event_id;

            $raw_data = array(
                "control" => $control,
            );

            $data = [
                'event' => $socket_channel_name,
                'data' => [
                    'data_info' => json_encode($raw_data),
                ],
            ];

            Redis::publish('event-buizz', json_encode($data));

            return response()->json([
                'success' => true,
            ], $this->successStatus);

        } else {
            $request->merge(["plateform" => "agora", "attendee_id" => $request->uid, "control" => $control]);

            if ($request->actionBy === "moderator") {
                $socket_channel_name = 'event-streaming-moderator-actions-' . $request->uid;
            } else {
                $socket_channel_name = 'event-streaming-actions-' . $request->event_id;
            }

            $this->eventMeetingRepository->updateControl($request->all());

            $raw_data = array(
                "control" => (in_array($control, ["handle-share"]) ? $control : ($control == "handle-presenter" ? "presenter" : ($control == "handle-mic" ? "audio" : "video"))),
                "uid" => $request->uid,
                "actionBy" => $request->actionBy,
                "value" => $request->value,
            );

            $data = [
                'event' => $socket_channel_name,
                'data' => [
                    'data_info' => json_encode($raw_data),
                ],
            ];

            Redis::publish('event-buizz', json_encode($data));

            return response()->json([
                'uid' => $request->uid,
                'actionBy' => $request->actionBy,
                'value' => $request->value,
                'success' => true,
            ], $this->successStatus);
        }
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function createToken(Request $request)
    {
        $appID = config("services.agora.appID");
        $appCertificate = config("services.agora.appCertificate");
        $channelName = $request->channel;
        $uid = 0;
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 86400;
        $currentTimestamp = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
        $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);

        if($request->video_id) {
            //Fetch meeting detail
            $video = $this->eventMeetingRepository->fetchMeeting($request->all());

            $request->merge(["program_id" => $video->agenda_id]);

            $attachedAttendees = $this->programRepository->attachedAttendees($request->all(), true);

            if ($video->thumbnail) {
                if (\App::environment('local')) {
                    $video['thumbnail'] = cdn('assets/program/videos/thumbnails/860-' . $video->thumbnail);
                } else {
                    $video['thumbnail'] = getS3Image('assets/program/videos/thumbnails/860-' . $video->thumbnail);
                }
            } else {
                $video['thumbnail'] = "";
            }

            $video['url'] = "";

            $video['attachedAttendees'] = $attachedAttendees;
        } else {
            $video = [];
        }


        //channel create 
        $meeting = $this->eventMeetingRepository->createChannel($request->all());
        
        return response()->json([
            'data' => array(
                "token" => $token,
                "appID" => $appID,
                "video" => $video,
            ),
            'success' => true,
        ], $this->successStatus);
        exit;
    }
}
