<?php

namespace App\Http\Controllers\Zoom;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Models\AgendaVideo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Support\Facades\Validator;

class MeetingController extends Controller
{
    protected $attendeeRepository;
    const MEETING_TYPE_SCHEDULE = 2;
    public $successStatus = 200;

    public function __construct(AttendeeRepository $attendeeRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
    }

    public function create(Request $request)
    {
        $moderator = Input::input('moderator');
        $agenda_video_id = Input::input('agenda_video_id');
        $title = Input::input('title');
        $start_time = Input::input('start_time');
        $start_time = $this->toZoomTimeFormat($start_time);
        $accessToken = $this->generateZoomToken();
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
        $response = $client->request('POST', '/v2/users/me/meetings', [
            "headers" => [
                "Authorization" => "Bearer $accessToken"
            ],
            'json' => [
                "topic" => $title,
                "type" => 2,
                "start_time" => $start_time,
            ],
        ]);
        $data = json_decode($response->getBody());
        $join_url = $data->join_url;
        AgendaVideo::where('id',$agenda_video_id)->update(['url'=>$join_url]);
        return \Response::json([
            'status'=>1,
            'message'=>'Meeting created successfully',
        ]);
    }

    private function generateZoomToken()
    {
        $key = env('ZOOM_API_KEY', '');
        $secret = env('ZOOM_API_SECRET', '');
        $payload = [
            'iss' => $key,
            'exp' => strtotime('+1 minute'),
        ];
        return \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
    }

    public function toZoomTimeFormat(string $dateTime)
    {
        $date = new \DateTime($dateTime);
        return $date->format('Y-m-d\TH:i:s');
    }

}
