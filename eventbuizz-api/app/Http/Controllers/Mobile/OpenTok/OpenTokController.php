<?php
namespace App\Http\Controllers\Mobile\OpenTok;

use App\Eventbuizz\Repositories\EventMeetingRepository;
use App\Eventbuizz\Repositories\OrganizerRepository;
use App\Eventbuizz\Repositories\ProgramRepository; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenTok\OpenTok;

class OpenTokController extends Controller
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
    public function createToken(Request $request)
    {
        $apiKey = config("services.vonage.apiKey");

        $apiSecret = config("services.vonage.apiSecret");

        $opentok = new OpenTok($apiKey, $apiSecret);
        
        if ($request->video_id) {

            $event = $request->event;

            //Fetch meeting detail
            $video = $this->eventMeetingRepository->fetchMeeting($request->all());

            if($video->sessionId) {
                $sessionId = $video->sessionId;
            } else {
                //Create session 
                $opentok = new OpenTok($apiKey, $apiSecret);

                if(in_array($video->type, ['agora-realtime-broadcasting-custom', 'agora-rooms', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar', 'agora-panel-disscussions'])) {
                    $session = $opentok->createSession(array('mediaMode' => \OpenTok\MediaMode::ROUTED));
                } else {
                    $session = $opentok->createSession(); 
                }

                // Store this sessionId in the database for later use
                $sessionId = $session->getSessionId();

                //Assign sessionId to video
                ProgramRepository::saveVonageSession(["video_id" => $request->video_id, "sessionId" => $sessionId]);
            }
            
            // Generate a Token from just a sessionId (fetched from a database)
            $token = $opentok->generateToken($sessionId);

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

            $event = $request->event;

            $params = explode("-", $request->channel);

            $agenda_id = $params[2];

            //Event agenda wise streaming
            $event_agenda = \App\Models\EventAgenda::where('event_id', $event['id'])->where('id', $agenda_id)->first();
            
            if(!$event_agenda->vonageSessionId) {
                //Create session 
                $opentok = new OpenTok($apiKey, $apiSecret);

                $session = $opentok->createSession();

                // Store this sessionId in the database for later use
                $sessionId = $session->getSessionId();

                // Generate a Token from just a sessionId (fetched from a database)
                $token = $opentok->generateToken($sessionId);

                //Assign sessionId to event
                ProgramRepository::saveProgramVonageSession(["event_id" => $event['id'], "vonageSessionId" => $sessionId, "agenda_id" => $agenda_id]);

            } else {
                $sessionId =  $event_agenda->vonageSessionId;

                // Generate a Token from just a sessionId (fetched from a database)
                $token = $opentok->generateToken($sessionId);
            }

        }

        return response()->json([
            'data' => array(
                "token" => $token,
                "sessionId" => $sessionId,
                "apiKey" => $apiKey,
                "video" => $video,
            ),
            'success' => true,
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function createSession(Request $request)
    {
        $event = $request->event;

        $apiKey = config("services.vonage.apiKey");

        $apiSecret = config("services.vonage.apiSecret");

        $opentok = new OpenTok($apiKey, $apiSecret);

        //Fetch meeting detail
        $video = $this->eventMeetingRepository->fetchMeeting($request->all());
        
        if(in_array($video->type, ['agora-realtime-broadcasting-custom', 'agora-rooms', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar', 'agora-panel-disscussions'])) {
            $session = $opentok->createSession(array('mediaMode' => \OpenTok\MediaMode::ROUTED));
        } else {
            $session = $opentok->createSession(); 
        }

        // Store this sessionId in the database for later use
        $sessionId = $session->getSessionId();

        if($video->type == "agora-realtime-broadcasting-custom") {
            //Amazon Interactive Video Service (Amazon IVS) 
            $client = \App::make('aws')->createClient('ivs');

            $channel = $client->createChannel([
                'authorized' => false,
                'latencyMode' => 'LOW',
                "tags" => ["Event-id" =>  (string) $request->event_id, "Video-id" => (string) $video->id],
                'name' => 'Channel-'.$request->event_id.'-'.$video->id,
            ]);

            return response()->json([
                'data' => array(
                    "playbackUrl" => $channel['channel']['playbackUrl'],
                    "streamKey" => $channel['streamKey']['value'],
                    "sessionId" => $sessionId,
                ),
                'success' => true,
            ], $this->successStatus);

        } else {
            return response()->json([
                'data' => array(
                    "sessionId" => $sessionId,
                ),
                'success' => true,
            ], $this->successStatus);
        } 
    }
    
    /**
     * startRecording
     *
     * @param  mixed $request
     * @return void
     */
    public function startRecording(Request $request)
    {
        try {

            $event = $request->event;

            if($event['enable_storage'] == 1) {

                $apiKey = config("services.vonage.apiKey");

                $apiSecret = config("services.vonage.apiSecret");
                
                $opentok = new OpenTok($apiKey, $apiSecret);
                
                //Fetch meeting detail
                $video = $this->eventMeetingRepository->fetchMeeting(['video_id' => $request->video_id]);

                // Create an archive using custom options
                $archiveOptions = array(
                    'name' => $event['id'].'-'.$request->video_id,     // default: null
                    'hasAudio' => true,                     // default: true
                    'hasVideo' => true,                     // default: true
                    'outputMode' => \OpenTok\OutputMode::COMPOSED,   // default: OutputMode::COMPOSED
                    'resolution' => '1280x720'              // default: '640x480'
                );

                if(!$video->archiveId) {
                    // Create a simple archive of a session
                    $archive = $opentok->startArchive($video->sessionId, $archiveOptions);
                    
                    $video->archiveId = $archive->id;

                    $video->save();
                } else {
                    $archive = $opentok->getArchive($video->archiveId);
                    
                    if(!$archive || ($archive && $archive->status != "started")) {
                        // Create a simple archive of a session
                        $archive = $opentok->startArchive($video->sessionId, $archiveOptions);

                        $video->archiveId = $archive->id;

                        $video->save();
                    }
                }
                
                return \Response::json([
                    "archive" => $archive->id,
                    "success" => true
                ]);

            } else {
                return \Response::json([
                    "archive" => "Recording is disabled",
                    "success" => true
                ]); 
            }

        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }

   /**
     * startRecording
     *
     * @param  mixed $request
     * @return void
     */
    public function stopRecording(Request $request)
    {
        try {

            $apiKey = config("services.vonage.apiKey");
            $apiSecret = config("services.vonage.apiSecret");
            $opentok = new OpenTok($apiKey, $apiSecret);

            //Fetch meeting detail
            $video = $this->eventMeetingRepository->fetchMeeting($request->all());

            if($video->archiveId) {
                $archive = $opentok->getArchive($video->archiveId);
    
                if($archive) {
                    $opentok->stopArchive($video->archiveId);
    
                    $video->archiveId = "";
    
                    $video->save();
                }
            }

            return \Response::json([
                "success" => true
            ]);

        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }
}
