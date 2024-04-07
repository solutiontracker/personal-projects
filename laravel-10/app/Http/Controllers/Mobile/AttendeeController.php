<?php
namespace App\Http\Controllers\Mobile;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    public $successStatus = 200;

    protected $attendeeRepository;

    /**
     * @param AttendeeRepository $attendeeRepository
     */
    public function __construct(AttendeeRepository $attendeeRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function profile(Request $request, $event_url)
    {
        $attendee = request()->user();

        return response()->json([
            'success' => true,
            'redirect' => "dashboard",
            'data' => array(
                'user' => array(
                    'id' => $attendee->id,
                    'name' => $attendee->first_name . ' ' . $attendee->last_name,
                    'first_name' => $attendee->first_name,
                    'last_name' => $attendee->last_name,
                    'email' => $attendee->email,
                    'image' => $attendee->image,
                ),
            ),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $id
     *
     * @return [type]
     */
    public function detail(Request $request, $event_url, $id)
    {
        $request->merge(["attendee_id" => $id]);
        $attendee = $this->attendeeRepository->getAttendeeDetail($request->all());
        $meeting = ($attendee ? $attendee->agoraMeeting($request->channel) : null);
        return response()->json([
            'success' => true,
            'data' => array(
                "detail" => $attendee,
                "meeting" => $meeting,
            ),
        ], $this->successStatus);
    }
}
