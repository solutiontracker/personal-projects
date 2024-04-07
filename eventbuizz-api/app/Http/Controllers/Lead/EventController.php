<?php

namespace App\Http\Controllers\Lead;

use App\Eventbuizz\Repositories\LeadRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public $successStatus = 200;

    protected $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }
    
    /**
     * getEventDetails
     *
     * @param  mixed $request
     * @return void
     */
    public function getEventDetails(Request $request, $event_id)
    {
        $eventDetails = $this->leadRepository->getEventDetails($request->all(), $event_id);
        return response()->json($eventDetails, $this->successStatus);
    }
    
    /**
     * getEventDetails
     *
     * @param  mixed $request
     * @return void
     */
    public function getEventSettings($event_id)
    {
        $eventSettings = $this->leadRepository->getEventSettings($event_id);
        return response()->json($eventSettings, $this->successStatus);
    }

    
}
