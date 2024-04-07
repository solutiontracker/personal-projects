<?php

namespace App\Http\Controllers\Wizard;

use App\Eventbuizz\Repositories\ImportRepository;
use App\Models\EventGdprSetting;
use App\Models\EventSetting;
use App\Models\EventSurveyAttendeeResult;
use App\Models\EventSurveyResult;
use App\Models\EventSurveyResultScore;
use App\Models\PollSetting;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\SurveyRepository;
use App\Http\Controllers\Wizard\Requests\Survey\QuestionRequest;
use App\Http\Controllers\Wizard\Requests\Survey\SurveyRequest;
use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\LabelRepository;


class SurveyController extends Controller
{
    public $successStatus = 200;

    protected $surveyRepository;

    protected $labelRepository;

    protected $importRepository;
    /**
     * @param SurveyRepository $surveyRepository
     * @param LabelRepository $labelRepository
     */
    public function __construct(SurveyRepository $surveyRepository, LabelRepository $labelRepository, ImportRepository $importRepository)
    {
        $this->surveyRepository = $surveyRepository;
        $this->labelRepository = $labelRepository;
        $this->importRepository = $importRepository;
    }

    public function listing(Request $request, $page)
    {
        $request->merge(['page' =>  $page]);
        $response = $this->surveyRepository->listing($request->all());
        return response()->json([
            'success' => true,
            'data' => $response
        ], $this->successStatus);
    }

    public function store(SurveyRequest $request)
    {
        $response = $this->surveyRepository->store(request()->all());

        EventRepository::add_module_progress(request()->all(), "survey");

        return response()->json([
            'success' => true,
            'message' =>__('messages.create'),
            'data' => $response
        ], $this->successStatus);
    }

    public function update(SurveyRequest $request, $id)
    {
        $survey = \App\Models\EventSurvey::find($id);
        if ($survey) {
            $this->surveyRepository->edit(request()->all(), $survey);

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
    }

    public function fetch($id)
    {
        $survey = $this->surveyRepository->fetch($id);

        return response()->json([
            'success' => true,
            'data' => array(
                "result" => $survey
            )
        ], $this->successStatus);
    }

    public function destroy(Request $request, $id)
    {
        $this->surveyRepository->destroy($id, $request->all());
        return response()->json([
            'success' => true,
            'message' => __('messages.delete'),
        ], $this->successStatus);
    }

    public function questions(Request $request, $id)
    {
        $response = $this->surveyRepository->questions($request->all(), $id);
        return response()->json([
            'success' => true,
            'data' => array(
                "data" => $response
            )
        ], $this->successStatus);
    }

    public function question_store(QuestionRequest $request, $id)
    {
        //Decode answer list
        request()->merge([
            'answer' => (request()->has('answer') ? json_decode(request()->get('answer'), true) : []),
            'column' => (request()->has('column') ? json_decode(request()->get('column'), true) : [])
        ]);

        $survey = \App\Models\EventSurvey::find($id);
        if ($survey) {

            $this->surveyRepository->question_store(request()->all(), $survey);

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
        $survey = \App\Models\EventSurvey::find($id);
        if ($survey) {
            $question = \App\Models\EventSurveyQuestion::where('survey_id', $survey->id)->where('id', $question_id)->first();
            if ($question) {
                $this->surveyRepository->question_update(request()->all(), $question);
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
        $survey = \App\Models\EventSurvey::find($id);
        if ($survey) {
            $this->surveyRepository->question_destroy($question_id, $request->all());
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
        $this->surveyRepository->question_option_destroy($id, $request->all());
        return response()->json([
            'success' => true,
            'message' => __('messages.delete'),
        ], $this->successStatus);
    }

    public function question_matrix_option_destroy(Request $request, $id)
    {
        $this->surveyRepository->question_matrix_option_destroy($id, $request->all());
        return response()->json([
            'success' => true,
            'message' => __('messages.delete'),
        ], $this->successStatus);
    }

    public function update_question_order(Request $request)
    {
        $this->surveyRepository->update_question_order($request->list);

        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroups(Request $request, $id){

        $survey = $this->surveyRepository->getGroups($request->all(), $id);
        return response()->json([
            'success' => true,
            'data' => $survey
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignGroups(Request $request, $id)
    {
        $this->surveyRepository->assignGroup($request->all(), $id);
        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $id
     * 
     * @return [type]
     */
    public function update_question_status(Request $request, $id)
    {
        $this->surveyRepository->update_question_status($request->all(), $id);
        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $id
     *
     * @return [type]
     */
    public function updateStatus(Request $request, $id)
    {
        $this->surveyRepository->updateStatus($request->all(), $id);
        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function fullScreenProjector(Request $request, $event_id, $id)
    {
        $request->merge(["question_id" => $id]);
        $question = $this->surveyRepository->fullScreenProjectorData($request->all());
        $questionTotalResponses = $this->surveyRepository->getQuestionTotalResponses($request->all());
        $setting = $this->surveyRepository->getSetting($request->all());
        $template = $this->surveyRepository->getTemplateForProjector($request->all());
        
        $event_setting = get_event_branding($request->event_id);
        if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
            $logo = cdn('/assets/event/branding/' . $event_setting['header_logo']);
        } else {
            $logo = cdn('/_admin_assets/images/eventbuizz_logo.png');
        }

        $total_vote_label = $this->labelRepository->getEventLabels($request->all(), 'polls', 'PROJECTOR_VOTES_BY_ATTENDEES');

        if ($total_vote_label) {
            $total_vote_label = sprintf($total_vote_label, $question['total_count'], $question['total_attendee']);
        } else {
            $total_vote_label = $question['total_count'] . ' vote(s) by ' . $question['total_attendee'] . ' attendee(s) ';
        }

        return response()->json([
            'success' => true,
            'data' => array(
                "question" => $question,
                "questionTotalResponses" => $questionTotalResponses,
                "setting" => $setting,
                "template" => $template,
                "logo" => $logo,
                "total_vote_label" => $total_vote_label,
            )
        ], $this->successStatus);
    }

    public function clearSurveyResults($id)
    {
        EventSurveyResult::where('survey_id', $id)->delete();
        EventSurveyAttendeeResult::where('survey_id', $id)->delete();
        EventSurveyResultScore::where('survey_id', $id)->delete();
        return response()->json([
            'success' => true]);
    }

    public function clearQuestionResults($id)
    {
        EventSurveyResult::where('question_id', $id)->delete();
        EventSurveyAttendeeResult::where('question_id', $id)->delete();
        EventSurveyResultScore::where('question_id', $id)->delete();

        return response()->json([
            'success' => true]);
    }

    public function surveySingleResultExport(Request $request, $id){
        $results = $this->surveyRepository->getSurveySingleResults($request->all(), $id);

        $this->importRepository->export($request->all(), $results);
    }
    public function surveyResultByPointsExport(Request $request){
        $results = $this->surveyRepository->surveyResultByPointsExport($request->all());
        $this->importRepository->export($request->all(), $results,'survey_results_points_'.time().'.csv');
    }
    public function surveyGetLeaderBoard(Request $request, $id){
        $attendee_survey_score = $this->surveyRepository->attendeeSurveyScore($request['event_id'],$id);
        $survey = $this->surveyRepository->getSurvey($id,$request);
        $event = Event::where('id', '=', $request['event_id'])->whereNull('deleted_at')->first();
        $event = $event !== null ? $event->toArray() : [];
        $event_settings = EventSetting::where('event_id', '=', $request['event_id'])->whereNull('deleted_at')->get()->toArray();
        $gdpr_settings = EventGdprSetting::where('event_id', '=', $request['event_id'])->first();
        $poll_settings = PollSetting::where('event_id', '=', $request['event_id'])->first();
        $event_settings2 = array();
        foreach ($event_settings as $row) {
            $event_settings2[$row['name']] = $row['value'];
        }
        return response()->json([
            'success' => true,
            'data'=>[
                'attendee_survey_score'=>$attendee_survey_score,
                'survey'=>$survey,
                'event'=>$event,
                'gdpr_settings'=>$gdpr_settings,
                'poll_settings'=>$poll_settings,
                'event_settings'=>$event_settings2,
            ]
        ]);
    }
}
