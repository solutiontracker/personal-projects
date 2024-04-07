<?php

namespace App\Http\Controllers\RegistrationSite;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Eventbuizz\Repositories\EventRepository;

class MetaInfoController extends Controller
{
    public $successStatus = 200;

    protected $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function index(Request $request, $slug)
    {
        //Fetch event ID
        $id = $this->eventRepository->fetchEventId($slug);

        $request->merge([
            "event_id" => $id
        ]);

        //Fetch Event
        $event = $this->eventRepository->getMetaInfo($id);
        $event = $event ? $event->toArray() : $event;
        $info=[];
        foreach ($event['info'] as $key => $item) {
            $info[$item['name']] = $item['value'];
        }
        $event['info'] = $info;
       
        $settings=[];
        foreach ($event['settings'] as $key => $item) {
            $settings[$item['name']] = $item['value'];
        }
        $event['settings'] = $settings;

        return response()->json([
            'event' => $event,
        ]);
    }
}
