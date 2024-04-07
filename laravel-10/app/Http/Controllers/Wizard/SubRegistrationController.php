<?php

namespace App\Http\Controllers\Wizard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\SubRegistrationRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Http\Controllers\Wizard\Requests\SubRegistration\QuestionRequest;
use App\Eventbuizz\Repositories\EventRepository;

class SubRegistrationController extends Controller
{
    public $successStatus = 200;

    protected $subRegistrationRepository;

    public function __construct(SubRegistrationRepository $subRegistrationRepository)
    {
        $this->subRegistrationRepository = $subRegistrationRepository;
    }

    public function listing(Request $request)
    {
        $response = $this->subRegistrationRepository->listing($request->all());
        
        return response()->json([
            'success' => true,
            'data' => $response
        ], $this->successStatus);
    }

    public function questions(Request $request)
    {
        $event = $request->event;

        $registration_form_id = $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0;

        $request->merge([
            'registration_form_id'=> $registration_form_id,
        ]);

        $sub_registration_id = \App\Models\EventSubRegistration::where('event_id', '=', $request->event_id)->where('registration_form_id', $registration_form_id)->value('id');

        $response = $this->subRegistrationRepository->questions($request->all(), $sub_registration_id);

        if ($request->question_id) {
            $event_total_submissions = $this->subRegistrationRepository->event_total_questions_submissions($request->all());
            $event_question_submissions = $this->subRegistrationRepository->event_question_submissions($request->all());
        }

        $settings = $this->subRegistrationRepository->getSettings($request->all());

        $module_setting = $this->subRegistrationRepository->get_module_setting($request->all());

        return response()->json([
            'success' => true,
            'data' => array(
                'question_type' => \App\Models\EventSubRegistrationQuestion::where('sub_registration_id', $sub_registration_id)->where('id', $request->question_id)->value('question_type'),
                'data' => $response,
                'event_total_submissions' => ($request->question_id ? $event_question_submissions . '/' . $event_total_submissions : ''),
                'sub_registration_id' => $sub_registration_id,
                'settings' => $settings,
                'module_setting' => $module_setting
            )
        ], $this->successStatus);

    }

    public function question_types(Request $request)
    {

        $response = array(
            "0" => "Select question type",
            "single" => "User can select one answer",
            "multiple" => "User can select multiple answers",
            "open" => "User can type a response",
            "number" => "User can type a number",
            "date" => "User can select a date",
            "date_time" => "User can select a date and time",
            "dropdown" => "User can select a value from dropdown"
        );

        return response()->json([
            'success' => true,
            'data' => $response
        ], $this->successStatus);
    }

    public function question_store(QuestionRequest $request, $id = 0)
    {
        //Decode answer list
        request()->merge([
            'answer' => (request()->has('answer') ? json_decode(request()->get('answer'), true) : []),
            'column' => (request()->has('column') ? json_decode(request()->get('column'), true) : [])
        ]);

        $sub_registration = \App\Models\EventSubRegistration::find($id);

        if (!$sub_registration) {
            $sub_registration = \App\Models\EventSubRegistration::create([
                "event_id" => $request->event_id,
                "status" => 1
            ]);
        }

        if ($sub_registration) {
            $this->subRegistrationRepository->question_store(request()->all(), $sub_registration);

            EventRepository::add_module_progress(request()->all(), "sub-registration");

            return response()->json([
                'success' => true,
                'message' => __('messages.create'),
            ], $this->successStatus);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.not_exist'),
            ], $this->successStatus);
        }
    }

    public function question_update(QuestionRequest $request, $id, $question_id)
    {
        $sub_registration = \App\Models\EventSubRegistration::find($id);
        if ($sub_registration) {
            $question = \App\Models\EventSubRegistrationQuestion::where('sub_registration_id', $sub_registration->id)->where('id', $question_id)->first();
            if ($question) {
                $this->subRegistrationRepository->question_update(request()->all(), $question);
                return response()->json([
                    'success' => true,
                    'message' => __('messages.update'),
                ], $this->successStatus);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.not_exist'),
                ], $this->successStatus);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.not_exist'),
            ], $this->successStatus);
        }
    }

    public function question_destroy(Request $request, $id, $question_id)
    {
        $sub_registration = \App\Models\EventSubRegistration::find($id);
        if ($sub_registration) {
            $this->subRegistrationRepository->question_destroy($question_id);
            return response()->json([
                'success' => true,
                'message' => __('messages.delete'),
            ], $this->successStatus);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.not_exist'),
            ], $this->successStatus);
        }
    }

    public function question_option_destroy(Request $request, $id)
    {
        $this->subRegistrationRepository->question_option_destroy($id);
        return response()->json([
            'success' => true,
            'message' => __('messages.delete'),
        ], $this->successStatus);
    }

    public function question_matrix_option_destroy(Request $request, $id)
    {
        $this->subRegistrationRepository->question_matrix_option_destroy($id);
        return response()->json([
            'success' => true,
            'message' => __('messages.delete'),
        ], $this->successStatus);
    }

    public function update_question_order(Request $request)
    {
        $this->subRegistrationRepository->update_question_order($request->list);

        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    public function question_results(Request $request)
    {
        $response = $this->subRegistrationRepository->question_results($request->all());
        return response()->json([
            'success' => true,
            'data' => array(
                'data' => $response,
            )
        ], $this->successStatus);
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('PUT')) {
            $this->subRegistrationRepository->updateSettings($request->all());
            return response()->json([
                'success' => true,
            ], $this->successStatus);
        }
    }

    public function update_module_setting(Request $request)
    {
        if ($request->isMethod('PUT')) {
            $this->subRegistrationRepository->update_module_setting($request->all());
            return response()->json([
                'success' => true,
            ], $this->successStatus);
        }
    }
}
