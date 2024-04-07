<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\EventRepository;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public $successStatus = 200;

    protected $eventRepository;

    /**
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function index(Request $request, $event_url)
    {
        $event = $request->event;

        $event['event_contact_persons'] = $this->eventRepository->getAllEventContactPersons($event['id']);

        $event['event_opening_hours'] = $this->eventRepository->getAllEventOpeningHours($event['id']);

        $event['country'] = getCountryName($event['country_id']);

        $request->merge([
            "event" => $event
        ]);

        return response()->json([
            'success' => true,
            'data' => array(
                "event" => $request->event,
            ),
        ], $this->successStatus);
    }
}
