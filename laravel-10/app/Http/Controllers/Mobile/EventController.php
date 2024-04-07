<?php
namespace App\Http\Controllers\Mobile;

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
    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'event' => $request->event,
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $status
     *
     * @return [type]
     */
    public function cameraAccess(Request $request, $eventUrl, $camera)
    {
        $request->merge([
            "camera" => $camera,
        ]);

        $this->eventRepository->cameraAccess($request->all());
        return response()->json([
            'success' => true,
        ], $this->successStatus);
    }
}
