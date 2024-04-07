<?php

namespace App\Http\Controllers\Wizard;

use App\Eventbuizz\Repositories\RegistrationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventRepository;

class RegistrationController extends Controller
{
    protected $registrationRepository;

    protected $attendeeRepository;

    public $successStatus = 200;

    public function __construct(RegistrationRepository $registrationRepository, AttendeeRepository $attendeeRepository)
    {
        $this->registrationRepository = $registrationRepository;
        $this->attendeeRepository = $attendeeRepository;
    }
    
    public function listing(Request $request, $alias)
    {
        $sectionAlias = \App\Models\BillingField::where('event_id', $request->get('event_id'))
            ->where('type', 'section')
            ->where('field_alias', $alias)
            ->first();
        if ($sectionAlias) {
            $fields = $this->registrationRepository->getRegistrationFields($alias);

            return response()->json([
                'success' => true,
                'data' => $fields,
                'attendee_types' => ($alias == "attendee_type_head" ? $this->attendeeRepository->attendee_types($request->all()) : []),
                'message' => __('messages.fetch'),
            ], $this->successStatus);
        }
        return response()->json([
            'success' => false,
            'message' => "Record not exist.",
        ], $this->successStatus);
    }

    public function update(Request $request, $alias)
    {
        $sectionAlias = \App\Models\BillingField::where('event_id', $request->get('event_id'))
            ->where('type', 'section')
            ->where('field_alias', $alias)
            ->first();
        if ($sectionAlias) {

            if ($alias == "attendee_type_head") {
                $this->attendeeRepository->update_attendee_types($request->all());
            } else {
                $this->registrationRepository->updateRegistrationFields($alias);
            }

            EventRepository::add_module_progress($request->all(), $alias);

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ], $this->successStatus);
        }
        return response()->json([
            'success' => false,
            'message' => __('messages.not_found'),
        ], $this->successStatus);
    }
}
