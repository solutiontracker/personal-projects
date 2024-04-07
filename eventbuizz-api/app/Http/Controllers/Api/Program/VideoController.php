<?php
namespace App\Http\Controllers\Api\Program;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenTok\OpenTok;
use App\Eventbuizz\Repositories\EventMeetingRepository;

class VideoController extends Controller
{
    public $successStatus = 200;

    protected $eventMeetingRepository;

    /**
     * @param EventMeetingRepository $eventMeetingRepository
     */
    public function __construct(EventMeetingRepository $eventMeetingRepository)
    {
        $this->eventMeetingRepository = $eventMeetingRepository;
    }

    /**
     * @param Request $request
     * @param mixed $id
     *
     * @return [type]
     */
    public function startVonageBroadcasting(Request $request)
    {
        try {
            $apiKey = config("services.vonage.apiKey");

            $apiSecret = config("services.vonage.apiSecret");

            $opentok = new OpenTok($apiKey, $apiSecret);

            $options = array(
                'outputs' => array(
                    'rtmp' => array(
                        [
                            "id" => "IVS",
                            "serverUrl" => $request->serverUrl.'/',
                            "streamName" => $request->streamName,
                        ],
                    ),
                ),
                'layout' => \OpenTok\Layout::getBestFit(),
            );

            $broadcast = $opentok->startBroadcast((string)$request->sessionId, $options);

            //Fetch meeting detail
            $video = $this->eventMeetingRepository->fetchMeeting($request->all());

            if($video) {
                $video->broadcasting_id = $broadcast->id;
                $video->save();
            }

            return \Response::json($broadcast);

        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }

    /**
     * @param Request $request
     * @param mixed $id
     *
     * @return [type]
     */
    public function stopVonageBroadcasting(Request $request)
    {
        try {
            $apiKey = config("services.vonage.apiKey");

            $apiSecret = config("services.vonage.apiSecret");

            $opentok = new OpenTok($apiKey, $apiSecret);

            $broadcast = $opentok->stopBroadcast($request->broadcasting_id);

            return \Response::json($broadcast);
        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }
}
