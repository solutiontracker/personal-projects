<?php
namespace App\Http\Controllers\Mobile\Zoom;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\OrganizerRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ZoomController extends Controller
{
    public $successStatus = 200;

    protected $organizerRepository;
    protected $attendeeRepository;

    /**
     * @param OrganizerRepository $organizerRepository
     * @param AttendeeRepository $attendeeRepository
     */
    public function __construct(OrganizerRepository $organizerRepository, AttendeeRepository $attendeeRepository)
    {
        $this->organizerRepository = $organizerRepository;
        $this->attendeeRepository = $attendeeRepository;
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function join(Request $request, $event_url, $attendee_id, $meeting_id, $password, $role)
    {
        $event = $request->event;
        $request->merge([
            "organizer_id" => $event['organizer_id'],
            "attendee_id" => $attendee_id,
        ]);
        $credentials = $this->organizerRepository->zoomCredentials($request->all());
        $attendee = $this->attendeeRepository->getAttendeeDetail($request->all());
        $api_key = $credentials->jwt_zoom_api_key;
        $api_secret = $credentials->jwt_zoom_api_secret;
        $signature = generate_zoom_signature($api_key, $api_secret, $meeting_id, $role);
        return \View::make('mobile.zoom.join-meeting', compact('api_key', 'signature', 'meeting_id', 'password', 'role', 'attendee'));
    }
}
