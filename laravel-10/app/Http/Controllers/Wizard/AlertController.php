<?php

namespace App\Http\Controllers\Wizard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\AlertRepository;
use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\ProgramRepository;
use App\Eventbuizz\Repositories\SurveyRepository;
use App\Eventbuizz\Repositories\SponsorsRepository;
use App\Eventbuizz\Repositories\ExhibitorRepository;
use App\Http\Controllers\Wizard\Requests\Alert\AlertRequest;

class AlertController extends Controller
{
    protected $attendeeRepository;

    public $successStatus = 200;

    public function __construct(AlertRepository $alertRepository)
    {
        $this->alertRepository = $alertRepository;
    }

    public function listing(Request $request)
    {
        $attendees = AttendeeRepository::getEventAttendees($request->all());
        $groups = EventRepository::getGroups($request->all());
        $programs = ProgramRepository::getAllPrograms($request->all());
        $workshops = ProgramRepository::getAllWorkshops($request->all());
        $surveys = SurveyRepository::fetchAllSurvey($request->all());
        $sponsors = SponsorsRepository::getSponsors($request->all());
        $exhibitors = ExhibitorRepository::getExhibitors($request->all());
        $response = $this->alertRepository->listing($request->all());
        $attendeeTypes = AttendeeRepository::getAttendeeTypes($request['event_id'], $request['language_id']);

        return response()->json([
            'success' => true,
            'data' => array(
                "records" => $response,
                "attendees" => $attendees,
                "groups" => $groups,
                "programs" => $programs,
                "workshops" => $workshops,
                "surveys" => $surveys,
                "sponsors" => $sponsors,
                "exhibitors" => $exhibitors,
                "attendeeTypes" => $attendeeTypes,
            ),
        ], $this->successStatus);
    }

    public function store(AlertRequest $request)
    {
        $this->alertRepository->store($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.create'),
        ], $this->successStatus);
    }

    public function update(AlertRequest $request, $id)
    {
        $alert = $this->alertRepository->getById($id);
        if ($alert) {
            if ($request->isMethod('PUT')) {
                $this->alertRepository->edit($request->all(), $alert);
                return response()->json([
                    'success' => true,
                    'message' => __('messages.update'),
                ], $this->successStatus);
            } else {
                if ($alert->sendto == "groups") {
                    $groups = \App\Models\EventAlertGroup::where('alert_id', $alert->id)->pluck('group_id')->toArray();
                    $group_id = EventRepository::getGroups($request->all(), $groups, 'custom');
                    return response()->json([
                        'success' => true,
                        'group_id' => $group_id,
                    ], $this->successStatus);
                } else if ($alert->sendto == "individuals") {
                    $attendees = \App\Models\EventAlertIndividual::where('alert_id', $alert->id)->pluck('attendee_id')->toArray();
                    $individual_id = AttendeeRepository::getEventAttendees($request->all(), $attendees, 'custom');
                    return response()->json([
                        'success' => true,
                        'individual_id' => $individual_id,
                    ], $this->successStatus);
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.not_exist'),
            ], $this->successStatus);
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->alertRepository->destroy($id);

        return response()->json([
            'success' => true,
            'message' => __('messages.delete'),
        ], $this->successStatus);
    }
}
