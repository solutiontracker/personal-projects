<?php

namespace App\Http\Controllers\Wizard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Wizard\Requests\ProgramRequest;
use App\Eventbuizz\Repositories\ProgramRepository;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    protected $programRepository;

    protected $successStatus = 200;

    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }


    /**
     * @param Request $request
     * @param int $page
     * @return \Illuminate\Http\JsonResponse
     * POST
     * route program/listing/{page?}
     */
    public function listing(Request $request, int $page = 1)
    {
        $request->merge(['page' =>  $page]);

        $programs = $this->programRepository->listing($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.fetch'),
            'data' => $programs
        ], $this->successStatus);
    }

    public function store(ProgramRequest $request)
    {
        $this->programRepository->createProgram($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.create'),
        ], $this->successStatus);
    }

    public function update(ProgramRequest $request, $id)
    {
        $program = \App\Models\EventAgenda::find($id);

        if ($program) {
            $this->programRepository->updateProgram($request->all(), $program);
            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ], $this->successStatus);
        }

        return response()->json([
            'success' => false,
            'message' => __('messages.not_exist'),
        ], $this->successStatus);
    }

    public function destroy(Request $request, $id)
    {
        $program = \App\Models\EventAgenda::find($id);

        if ($program) {

            $programIsLinkedWithItem = \App\Models\BillingItem::where('link_to_id', '=', $id)
                ->where('event_id', $request->get('event_id'))->where('is_archive', 0)->first();

            if ($programIsLinkedWithItem) {
                $itemLabel = 'billing';
                if ($programIsLinkedWithItem->is_free == 1) {
                    $itemLabel = 'registration';
                }
                return response()->json([
                    'success' => false,
                    'message' => __('messages.on_program_delete', ['item_labels' => $itemLabel]),
                ], $this->successStatus);
            }

            $programIsLinkedWithSubReg = \App\Models\EventSubRegistrationAnswer::where('link_to', '=', $id)->whereNull('deleted_at')->first();

            if ($programIsLinkedWithSubReg) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.on_program_delete_if_link_with_sub_registration'),
                ], $this->successStatus);
            }

            $this->programRepository->deleteProgram($request->all(), $id);

            return response()->json([
                'success' => true,
                'message' => __('messages.delete'),
            ], $this->successStatus);
        }

        return response()->json([
            'success' => false,
            'message' => __('messages.not_exist'),
        ], $this->successStatus);
    }

    public function getAllPrograms(Request $request)
    {
        $programs = $this->programRepository->getAllPrograms($request->all());
        
        return response()->json([
            'success' => true,
            'message' => __('messages.fetch'),
            'data' => $programs
        ], $this->successStatus);
    }

    public function download_pdf(Request $request)
    {
        $programs_array = array();
        $programs = $this->programRepository->listing($request->all());
        $programs_array = group_by("date", $programs['data']);
        $pdf = \PDF::loadView('admin.programs.pdf.programs', compact('programs_array'));
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('javascript-delay', 5000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        return $pdf->download('chart.pdf');
    }

    public function assignSpeakers(Request $request)
    {
        if ($request->isMethod('PUT') && $request->action == "assign") {
            if(count($request->is_l_check) > 0) {
                foreach($request->is_l_check as $attendee_id) {
                    $this->programRepository->attachProgram(['attendee_id' => $attendee_id, 'agenda_id' => $request->id, 'event_id' => $request->event_id]);
                }
            }
            return response()->json([
                'success' => true,
            ], $this->successStatus);
        } else if ($request->isMethod('PUT') && $request->action == "unassign") {
            if(count($request->is_l_check) > 0) {
                foreach($request->is_l_check as $attendee_id) {
                    $this->programRepository->detachedProgram(['attendee_id' => $attendee_id, 'agenda_id' => $request->id, 'event_id' => $request->event_id]);
                }
            }
            return response()->json([
                'success' => true,
            ], $this->successStatus);
        } else {
            
            $request->merge(['page' =>  (int) $request->page, 'program_id' => $request->id]);

            $program_attendees = $this->programRepository->getProgramSpeakers($request->all());
            
            $program_data = $this->programRepository->getProgram($request->all());

            return response()->json([
                'success' => true,
                'data' => array(
                    'program_attendees' => $program_attendees,
                    'program_data' => $program_data
                )
            ], $this->successStatus);
        }
    }
}
