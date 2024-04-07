<?php

namespace App\Http\Controllers\Zapier;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\ImportRepository;
use App\Eventbuizz\Repositories\LabelRepository;
use App\Http\Resources\Zapier\Attendee as AttendeeResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendeeController extends Controller
{
    protected $attendeeRepository;

    protected $importRepository;

    protected $labelRepository;

    public $successStatus = 200;

    public function __construct(AttendeeRepository $attendeeRepository, ImportRepository $importRepository, LabelRepository $labelRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->importRepository = $importRepository;
        $this->labelRepository = $labelRepository;
        JsonResource::withoutWrapping();
    }

    public function index(Request $request, $page){
        $attendees = $this->attendeeRepository->getZapierAttendees($request->all());
        return AttendeeResource::collection($attendees);
    }
}
