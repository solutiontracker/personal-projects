<?php

namespace App\Eventbuizz\Repositories;

use App\Models\AttendeeInfo;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\EventGroup;
use App\Models\Events;
use App\Models\EventSurveyAnswer;
use App\Models\EventSurveyGroup;
use App\Models\EventSurveyMatrix;
use App\Models\EventSurveyQuestion;
use App\Models\EventSurveyResultScore;
use App\Models\PollSetting;
use App\Models\EventSurveyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;


use \App\Models\EventSurvey;

class SurveyRepository extends AbstractRepository
{
    protected $model;
    protected $request;

    public function __construct(Request $request, EventSurvey $model)
    {
        $this->request = $request;
        $this->model = $model;
    }

    /**
     * when new event create / cloning event
     *
     * @param array
     */
    public function install($request)
    {
        if ($request["content"]) {
            //Survey
            $from_surveys = \App\Models\EventSurvey::where("event_id", $request['from_event_id'])->get();
            if ($from_surveys) {
                foreach ($from_surveys as $from_survey) {
                    $to_survey = $from_survey->replicate();
                    $to_survey->event_id = $request['to_event_id'];
                    $to_survey->save();

                    //survey info
                    $from_survey_info = \App\Models\EventSurveyInfo::where("survey_id", $from_survey->id)->get();
                    foreach ($from_survey_info as $from_info) {
                        $info = $from_info->replicate();
                        $info->survey_id = $to_survey->id;
                        $info->languages_id = $request["languages"][0];
                        $info->save();
                    }

                    //Questions
                    $from_questions = \App\Models\EventSurveyQuestion::where("survey_id", $from_survey->id)->get();
                    if ($from_questions) {
                        foreach ($from_questions as $from_question) {
                            $to_question = $from_question->replicate();
                            $to_question->survey_id = $to_survey->id;
                            $to_question->save();

                            //question info
                            $from_question_info = \App\Models\SurveyQuestionInfo::where("question_id", $from_question->id)->get();
                            if ($from_question_info) {
                                foreach ($from_question_info as $from_info) {
                                    $to_info = $from_info->replicate();
                                    $to_info->question_id = $to_question->id;
                                    $to_info->languages_id = $request["languages"][0];
                                    $to_info->save();
                                }
                            }

                            //question answers
                            $from_answers = \App\Models\EventSurveyAnswer::where("question_id", $from_question->id)->get();
                            if ($from_answers) {
                                foreach ($from_answers as $from_answer) {
                                    $to_answer = $from_answer->replicate();
                                    $to_answer->question_id = $to_question->id;
                                    $to_answer->save();

                                    //answer info
                                    $from_answer_info = \App\Models\EventSurveyAnswerInfo::where("answer_id", $from_answer->id)->get();
                                    if ($from_answer_info) {
                                        foreach ($from_answer_info as $from_info) {
                                            $to_info = $from_info->replicate();
                                            $to_info->answer_id = $to_answer->id;
                                            $to_info->question_id = $to_question->id;
                                            $to_info->languages_id = $request["languages"][0];
                                            $to_info->save();
                                        }
                                    }
                                }
                            }

                            //question matrix
                            $from_matrix_columns = \App\Models\EventSurveyMatrix::where("question_id", $from_question->id)->get();
                            if ($from_matrix_columns) {
                                foreach ($from_matrix_columns as $from_matrix_column) {
                                    $to_matrix_column = $from_matrix_column->replicate();
                                    $to_matrix_column->question_id = $to_question->id;
                                    $to_matrix_column->save();
                                }
                            }
                        }
                    }

                    //survey groups
                    $from_survey_groups = \App\Models\EventSurveyGroup::where("survey_id", $from_survey->id)->get();
                    if ($from_survey_groups) {
                        foreach ($from_survey_groups as $from_survey_group) {
                            if (session()->has('clone.event.event_groups.' . $from_survey_group->group_id)) {
                                $to_survey_group = $from_survey_group->replicate();
                                $to_survey_group->survey_id = $to_survey->id;
                                $to_survey_group->group_id = session()->get('clone.event.event_groups.' . $from_survey_group->group_id);
                                $to_survey_group->save();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     *survey listing
     * @param array
     */
    public function listing($formInput)
    {
        //Total registered attendees
        $registered_attendees = AttendeeRepository::registered_attendees($formInput['event_id'], true);

        $result = \App\Models\EventSurvey::where('event_id', $formInput['event_id'])
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }, 'question']);


        if (isset($formInput['limit']) && $formInput['limit']) $limit = $formInput['limit'];
        else $limit = 200;

        //Sorting
        if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'])) {
            $result->orderBy($formInput['sort_by'], $formInput['order_by']);
        } else {
            $result->orderBy('id', 'ASC');
        }

        $result = $result->paginate($limit)->toArray();

        foreach ($result['data'] as $key => $row) {
            $info = readArrayKey($row, [], 'info');
            $result['data'][$key]['created_at'] = \Carbon\Carbon::parse($row['created_at'])->format('m/d/y');
            $result['data'][$key]['info'] = $info;
            $result['data'][$key]['questions'] = count($row['question']  ?? []);
            $result['data'][$key]['total_attendees'] = $registered_attendees;
            $results = $this->survey_results($row['id'], $formInput['event_id']);
            $result['data'][$key]['results'] = count($results);
        }

        return $result;
    }

    /**
     *survey results
     * @param int
     * @param int
     */
    public function survey_results($survey_id, $event_id)
    {
        return \App\Models\EventSurveyAttendeeResult::where('survey_id', $survey_id)->where('event_id', $event_id)
            ->groupBy('attendee_id')
            ->get()
            ->toArray();
    }

    /**
     *create survey
     * @param array
     */
    public function store($formInput)
    {
        $this->setSurveyForm($formInput)
            ->create()
            ->insertInfo();

        return $this->getObject();
    }

    /**
     *set form input for survey creation
     * @param array
     */
    public function setSurveyForm($formInput)
    {
        $formInput['event_id'] = $formInput['event_id'];
        $formInput['status'] = '1';
        $this->setFormInput($formInput);
        return $this;
    }

    /**
     *insert info for survey
     */
    public function insertInfo()
    {
        $formInput = $this->getFormInput();
        $languages = get_event_languages($formInput['event_id']);
        foreach ($languages as $key) {
            $info[] = new \App\Models\EventSurveyInfo(array('name' => 'name', 'value' => $formInput['name'], 'languages_id' => $key, 'status' => 1));
        }
        $survey = $this->getObject();
        $survey->info()->saveMany($info);
        return $this;
    }

    /**
     *update survey
     * @param array
     * @param object
     */
    public function edit($formInput, $survey)
    {
        return \App\Models\EventSurveyInfo::where('survey_id', $survey->id)->where('languages_id', $formInput['language_id'])->update([
            'value' => $formInput['name']
        ]);
    }

    /**
     *delete survey
     * @param int
     */
    public function destroy($survey_id, $formInput)
    {

        $survey = EventSurvey::with('info')->find($survey_id);
        $survey_name = $survey->info()->where('name', 'name')->first();
        $poll_setting = PollSetting::where('event_id', $formInput['event_id'])->first();

        // Push Data on Redis for socket.
        if($poll_setting['display_survey_module'] == 1 && $poll_setting['display_survey'] == 1){

            $this->pushSocketSurveyStatus(
                $formInput['event_id'],
                $survey_id,
                null,
                true,
                $survey_name->value
            );
        }

        \App\Models\EventSurveyResult::where('survey_id', $survey_id)->delete();
        \App\Models\EventSurveyAttendeeResult::where('survey_id', $survey_id)->delete();
        $questions = \App\Models\EventSurveyQuestion::where('survey_id', $survey_id)->get();
        foreach ($questions as $row) {
            \App\Models\EventSurveyResultScore::where('question_id', $row->id)->delete();
            \App\Models\SurveyQuestionInfo::where('question_id', $row->id)->delete();
            $answers = \App\Models\EventSurveyAnswer::where('question_id', $row->id)->whereNull('deleted_at')->get();
            foreach ($answers as $answer) {
                \App\Models\EventSurveyAnswerInfo::where('answer_id', $answer->id)->delete();
            }
            \App\Models\EventSurveyAnswer::where('question_id', $row->id)->delete();
        }
        \App\Models\EventSurveyQuestion::where('survey_id', $survey_id)->delete();
        \App\Models\EventSurvey::find($survey_id)->delete();
        \App\Models\EventSurveyInfo::where('survey_id', $survey_id)->delete();
    }

    /**
     *survey questions
     * @param array
     * @param int
     */
    public function questions($formInput, $id)
    {
        $result = \App\Models\EventSurveyQuestion::where('survey_id', '=', $id)
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', '=', $formInput['language_id']);
            },
            'matrix' => function ($query){
                $query->orderBy('sort_order');
            }])
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        foreach ($result as $key => $val) {
            $answers = \App\Models\EventSurveyAnswer::where('question_id', '=', $val['id'])
                ->with(['info' => function ($query) use ($formInput) {
                    return $query->where('languages_id', '=', $formInput['language_id']);
                }])
                ->orderBy('sort_order')
                ->orderBy('id', 'ASC')
                ->get()
                ->toArray();

            $q_responses = $this->getResultCountForSurveyQuestion($val['id']);
            $answer_array = array();
            foreach ($answers as $row) {
                $a_responses = $this->getResultsCountForSurveyAnswer($row['id']);
                $answer_array[]  = [
                    'id' => $row['id'],
                    'status' => $row['status'],
                    'value' => (isset($row['info'][0]['value']) ? $row['info'][0]['value'] : ''),
                    'correct' => $row['correct'],
                    'sort_order' => $row['sort_order'],
                    'a_responses' => $a_responses
                ];
            }

            $info = readArrayKey($val, [], 'info');
            $result[$key]['question'] = (isset($info['question']) ? $info['question'] : '');
            $result[$key]['answer'] = $answer_array;
            $result[$key]['q_responses'] = $q_responses;
        }

        return $result;
    }

    /**
     *create question
     * @param array
     * @param object
     */
    public function question_store($formInput, $survey)
    {
        $sort_order = \App\Models\EventSurveyQuestion::where('survey_id', $survey->id)->max('sort_order');
        $sort_order = $sort_order + 1;
        $languages = get_event_languages($formInput['event_id']);
        set_event_timezone($formInput['event_id']);

        $question = new \App\Models\EventSurveyQuestion(array(
            'result_chart_type' => $formInput['result_chart_type'],
            'max_options' => $formInput['max_options'] ? $formInput['max_options'] : 0,
            'min_options' => $formInput['min_options'] ? $formInput['max_options'] : 0,
            'question_type' => $formInput['question_type'],
            'required_question' => (isset($formInput['required_question']) ? $formInput['required_question'] : '0'),
            'enable_comments' => (isset($formInput['enable_comments']) ? $formInput['enable_comments'] : '0'),
            'is_anonymous' => (isset($formInput['is_anonymous']) ? $formInput['is_anonymous'] : '0'),
            'sort_order' => $sort_order,
            'status' => 1
        ));

        $question_instance = $survey->question()->save($question);

        foreach ($languages as $language) {
            $info[] = new \App\Models\SurveyQuestionInfo(array(
                'name' => 'question',
                'value' => $formInput['question'],
                'languages_id' => $language,
                'status' => 1
            ));
        }

        $question_instance->info()->saveMany($info);

        if (!empty($formInput['answer'])) {
            foreach ($formInput['answer'] as $key => $answer) {
                $correct = (isset($answer['correct']) && $answer['correct'] ? $answer['correct'] : 0);
                $answer = (isset($answer['value']) && $answer['value'] ? $answer['value'] : NULL);
                if ($answer) {
                    $question_answer = new \App\Models\EventSurveyAnswer(array(
                        'correct' => $correct,
                        'status' => 1
                    ));

                    $answer_obj = $question_instance->answer()->save($question_answer);

                    foreach ($languages as $language) {
                        $answer_info = new \App\Models\EventSurveyAnswerInfo(array(
                            'name' => 'answer',
                            'value' => $answer,
                            'languages_id' => $language,
                            'status' => 1
                        ));
                        $answer_obj->info()->save($answer_info);
                    }
                }
            }

        }
        if(!empty($formInput['column']) && $formInput['question_type'] == "matrix"){
            foreach($formInput['column'] as $column) {
                if (trim($column['value'])) {
                    //Question column
                    $matrix = new EventSurveyMatrix(array('name' => $column['value']));
                    $question_instance->matrix()->save($matrix);
                }
            }
        }
    }

    /**
     *update question
     * @param array
     * @param object
     */
    public function question_update($formInput, $question)
    {
        $languages = get_event_languages($formInput['event_id']);

        //save question
        if ($question) {
            $question->question_type = $formInput['question_type'];
            $question->result_chart_type = $formInput['result_chart_type'];
            $question->max_options = $formInput['max_options'] ? $formInput['max_options'] : 0;
            $question->min_options = $formInput['min_options'] ? $formInput['min_options'] : 0;
            $question->required_question = (isset($formInput['required_question']) ? $formInput['required_question'] : '0');
            $question->enable_comments = (isset($formInput['enable_comments']) ? $formInput['enable_comments'] : '0');
            $question->is_anonymous = (isset($formInput['is_anonymous']) ? $formInput['is_anonymous'] : '0');
            $question->save();

            //save question info
            $question_info = \App\Models\SurveyQuestionInfo::where('question_id', $question->id)->where('name', 'question')->where('languages_id', $formInput['language_id'])->first();
            if ($question_info) {
                $question_info->value = $formInput['question'];
                $question_info->save();
            } else {
                $question_info = \App\Models\SurveyQuestionInfo::create([
                    "question_id" => $question->id,
                    "name" => 'question',
                    "languages_id" => $formInput['language_id'],
                    "value" => $formInput['question'],
                ]);
            }

            //save answer
            $sort = 0;
            if (!empty($formInput['answer'])) {
                foreach ($formInput['answer'] as $key => $answer) {
                    $answer_id = (isset($answer['id']) ? $answer['id'] : 0);
                    $correct = (isset($answer['correct']) && $answer['correct'] ? $answer['correct'] : 0);
                    $answer = (isset($answer['value']) && $answer['value'] ? $answer['value'] : NULL);
                    if ($answer) {
                        $question_answer = \App\Models\EventSurveyAnswer::where('question_id', $question->id)->where('id', $answer_id)->first();
                        if ($question_answer) {
                            $question_answer->correct = $correct;
                            $question_answer->sort_order = $sort;
                            $question_answer->save();

                            $answer_info =  \App\Models\EventSurveyAnswerInfo::where('languages_id', $formInput['language_id'])->where('answer_id', $question_answer->id)->first();

                            if ($answer_info) {
                                $answer_info->value = $answer;
                                $answer_info->save();
                            } else {
                                $answer_obj = $question->answer()->save($question_answer);

                                $answer_info = new \App\Models\EventSurveyAnswerInfo(array(
                                    'name' => 'answer',
                                    'value' => $answer,
                                    'languages_id' => $formInput['language_id'],
                                    'status' => 1
                                ));

                                $answer_obj->info()->save($answer_info);
                            }
                        } else {
                            $question_answer = new \App\Models\EventSurveyAnswer(array(
                                'correct' => $correct,
                                'status' => 1,
                                'sort_order' => $sort,
                            ));

                            $answer_obj = $question->answer()->save($question_answer);

                            foreach ($languages as $language) {
                                $answer_info = new \App\Models\EventSurveyAnswerInfo(array(
                                    'name' => 'answer',
                                    'value' => $answer,
                                    'languages_id' => $language,
                                    'status' => 1
                                ));
                                $answer_obj->info()->save($answer_info);
                            }
                        }
                        $sort++;
                    }
                }
            }
            if (!empty($formInput['column'])) {
                foreach ($formInput['column'] as $key => $column) {
                    $column_id = (isset($column['id']) ? $column['id'] : 0);

                    if (trim($column['value'])) {
                        $matrix = EventSurveyMatrix::where('question_id', $question->id)->where('id', $column_id)->first();

                        if(!$matrix){
                            $matrix =  new EventSurveyMatrix();
                            $matrix->question_id =  $question->id;
                        }

                        $matrix->sort_order = $column['sort_order'] ? $column['sort_order'] : 0 ;
                        $matrix->name = $column['value'];
                        $matrix->save();

                    }
                }
            }

        }
    }

    /**
     *delete question
     * @param int
     */
    public function question_destroy($question_id, $formInput)
    {
        $question = EventSurveyQuestion::find($question_id);

        $survey = EventSurvey::with('info')->find($question->survey_id);

        $survey_name = $survey->info()->where('name', 'name')->first();

        $poll_setting = PollSetting::where('event_id', $formInput['event_id'])->first();

        // Push Data on Redis for socket.
        if($survey->status == 1 && $poll_setting['display_survey_module'] == 1 && $poll_setting['display_survey'] == 1){
            $this->pushSocketSurveyStatus(
                $formInput['event_id'],
                $survey->id,
                1,
                true,
                $survey_name->value
            );
        }

        \App\Models\EventSurveyResult::where('survey_id', '=', $question_id)->delete();
        \App\Models\EventSurveyAttendeeResult::where('survey_id', '=', $question_id)->delete();
        \App\Models\EventSurveyResultScore::where('question_id', '=', $question_id)->delete();
        $questions = \App\Models\EventSurveyAnswer::where('question_id', '=', $question_id)->whereNull('deleted_at')->get();
        foreach ($questions as $row) {
            \App\Models\EventSurveyAnswerInfo::where('answer_id', $row->id)->delete();
        }
        \App\Models\EventSurveyAnswer::where('question_id', '=', $question_id)->delete();
        \App\Models\EventSurveyQuestion::find($question_id)->delete();
        \App\Models\SurveyQuestionInfo::where('question_id', '=', $question_id)->delete();
    }

    /**
     *delete question option
     * @param int
     */
    public function question_option_destroy($option_id)
    {
        \App\Models\EventSurveyAnswer::find($option_id)->delete();
        \App\Models\EventSurveyAnswerInfo::where('answer_id', $option_id)->delete();
    }

    /**
     *delete question option
     * @param int
     */
    public function question_matrix_option_destroy($option_id)
    {
        \App\Models\EventSurveyMatrix::find($option_id)->delete();
    }

    /**
     *update question sort order
     * @param array
     */

    function update_question_order($list)
    {
        if (!empty($list)) {
            $sort = 0;
            foreach ($list as $row) {
                $question = \App\Models\EventSurveyQuestion::query();
                $model = $question->find($row['id']);
                $model->sort_order = $sort;
                $model->save();
                $sort++;
            }
        }

        return true;
    }

    /**
     *fetch survey
     * @param int
     */
    public function fetch($id)
    {
        $response = array();
        $row = \App\Models\EventSurvey::where('id', $id)->with(['info'])->first();
        if ($row && $row->info) {
            $info = readArrayKey($row, [], 'info');
            $response['id'] = $row->id;
            $response['info'] = $info;
        }
        return $response;
    }

    /**
     *Fetch result count for survey question
     * @param int
     */
    function getResultCountForSurveyQuestion($question_id)
    {
        $question_results = \App\Models\EventSurveyResult::where('question_id', '=', $question_id)->groupBy('attendee_id')->get()->toArray();
        return count($question_results);
    }

    /**
     *Fetch result count for survey answer
     * @param int
     */
    function getResultsCountForSurveyAnswer($answer_id)
    {
        $answer_results = \App\Models\EventSurveyResult::where('answer_id', '=', $answer_id)->get()->toArray();
        return count($answer_results);
    }

    /**
     *Fetch all surveys
     * @param array
     */
    static public function fetchAllSurvey($formInput)
    {
        $label = eventsite_labels('polls', $formInput, 'BLANK_TEMPLATE_SURVEY_LABEL_POLL');
        $array = array();
        $rows = \App\Models\EventSurvey::where('event_id', $formInput['event_id'])->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput['language_id']);
        }])->get(['id'])->toArray();
        $array[0] = $label;
        foreach ($rows as $survey) {
            $id = $survey['id'];
            foreach ($survey['info'] as $info) {
                if ($info['name'] == 'name') {
                    $name = $info['value'];
                    break;
                }
            }
            $array[$id] = $name;
        }
        return $array;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getGroups($formInput, $id){
        $event_id =  $formInput['event_id'];
        $lang_id =  $formInput['language_id'];

        $groups = EventGroup::where('event_id', '=',$event_id)
            ->where('parent_id', '=', '0')
            ->has('children')
            ->with(['Info' => function ($query) use($lang_id) {
                return $query->where('languages_id', '=', $lang_id);
            }])
            ->with(['children' => function ($r) {
                return $r->orderBy('sort_order', 'asc')->orderBy('id','asc');
            }, 'children.childrenInfo' => function ($r) use($lang_id) {
                return $r->where('languages_id', '=', $lang_id);
            }])
            ->orderBy('sort_order','asc')->orderBy('id','asc')->get()->toArray();


        $assigned_groups = EventSurveyGroup::where('survey_id', $id)->get()->pluck(['group_id']);

        return [
                'groups' => $groups,
                'assigned_ids' => $assigned_groups
            ];

    }

    /**
     * @param array $formInput
     * @param $id
     * @return bool
     */
    public function assignGroup(array $formInput, $id)
    {
       $survey = EventSurvey::find($id);
       if($survey){
           $survey->surveyGroups()->sync($formInput['groups']);
       }
       return true;
    }

    /**
     * @param array $formInput
     * @param mixed $id
     * 
     * @return [type]
     */
    public function update_question_status(array $formInput, $id)
    {
        $question = EventSurveyQuestion::find($id);
        $question->status = $formInput['value'];
        $question->save();

        $survey = EventSurvey::with('info')->find($question->survey_id);

        $survey_name = $survey->info()->where('name', 'name')->first();

        $poll_setting = PollSetting::where('event_id', $formInput['event_id'])->first();

        if($survey->status == 1 && $poll_setting['display_survey_module'] == 1 && $poll_setting['display_survey'] == 1){

            // Push Data on Redis for socket.
            $this->pushSocketSurveyStatus(
                $formInput['event_id'],
                $survey->id,
                1,
                $question->status ? false : true,
                $survey_name->value
            );
        }
        return true;
    }

    /**
     * @param array $formInput
     * @param mixed $id
     *
     * @return [type]
     */
    public function updateStatus(array $formInput, $id)
    {
        $survey = EventSurvey::with('info')->find($id);
        $survey->status = $formInput['value'];
        $survey->save();

        $survey_name = $survey->info()->where('name', 'name')->first();
        $poll_setting = PollSetting::where('event_id', $formInput['event_id'])->first();
        if($poll_setting['display_survey_module'] == 1 && $poll_setting['display_survey'] == 1){

            // Push Data on Redis for socket.
            $this->pushSocketSurveyStatus(
                $formInput['event_id'],
                $id,
                null,
                $survey->status ? false : true,
                $survey_name->value
            );
        }
        return true;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function fullScreenProjectorData($formInput) {
        $question = EventSurveyQuestion::where('id', $formInput['question_id'])->with(['info' => function ($query) use($formInput) {
			return $query->where('languages_id', $formInput['language_id']);
        }])->first()->toArray();
        
        $question['detail'] = readArrayKey($question, [], 'info');
        
        $answers = \App\Models\EventSurveyAnswer::where('question_id', '=', $question['id'])->with(['info' => function ($query) use($formInput) {
            return $query->where('languages_id', $formInput['language_id']);
        },'result'])->orderBy('sort_order')->get()->toArray();

        $temp = array();
        $total_count = 0;
        foreach ($answers as $row) {
            $column =\App\Models\EventSurveyResult::where('question_id', '=', $question['id'])->where('answer_id', '=', $row['id'])->first();
            $result_count = \App\Models\EventSurveyResult::where('answer_id', '=', $row['id'])->count();
            $temp[] = ['id' => $row['id'], 'sort_order' => $row['sort_order'], 'result_count'=> $result_count, 'status' => $row['status'],'column_answer'=>$column['answer'], 'created_at' => $row['created_at'], 'updated_at' => $row['updated_at'], 'deleted_at' => $row['deleted_at'], 'answer' => $row['info'][0]['value'], 'correct' => $row['correct'],'resultCount'=>count($row['result'])];
            $total_count = $total_count + $result_count;
        }
        array_multisort(array_column($temp, 'result_count'), SORT_DESC, $temp);
        $question['results'] = \App\Models\EventSurveyAttendeeResult::where('question_id', $question['id'])->where('event_id', $formInput['event_id'])->groupBy('attendee_id')->get();
        $question['answer'] = $temp;
        $question['total_count'] = $total_count;
        $question['total_attendee'] = count($question['results']);

		return $question;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function getQuestionTotalResponses($formInput) {
        return \App\Models\EventSurveyResult::where('question_id', $formInput['question_id'])->count();
    }
    
    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function getTemplateForProjector($formInput)
    {
        return \App\Models\PollTemplate::where('event_id', $formInput['event_id'])->first();
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function getSetting($formInput)
    {
        return \App\Models\PollSetting::where('event_id', $formInput['event_id'])->first();
    }

    private function pushSocketSurveyStatus($event_id, $survey_id, $single_question = null, $inactive = false, $survey_value)
    {
        if ($inactive) {
            return 0;
        }

        $values = [];
        $survey_group = EventSurveyGroup::where('survey_id', $survey_id)->first();

        $values['group_attached'] = $survey_group ? 1 : 0;
        $values['survey_id'] = $survey_id;
        $values['survey_name'] = $survey_value;
        $values['single_question'] = $single_question;
        $values['inactive'] = $inactive ? 1 : 0;

        $data = [
            'event' => 'survey_question_active_inactive' . $event_id,
            'data' => $values,
        ];

        return \Redis::publish('event-buizz', json_encode($data));
    }

    public function getSurveySingleResults($formInput, $id)
    {

        // Rename sheet
        $survey_heading = ['First Name', 'Last Name', 'Email'];
        $survey_records = [];

        $survey_data = EventSurvey::where('id', $id)->where('event_id', '=', $formInput['event_id'])->with(['info' => function ($query) use ($formInput){
            return $query->where('languages_id', '=', $formInput['language_id']);
        }])->get()->toArray();

        foreach ($survey_data as $survey_row){

            $survey_heading[] = $survey_row['info'][0]['value'];

            $survey_question_data = EventSurveyQuestion::where('survey_id', '=', $survey_row['id'])->where('is_anonymous', 0)->where('question_type','!=','matrix')->with(['info' => function ($query) use($formInput){
                return $query->where('languages_id', '=', $formInput['language_id']);
            }])->whereNull('deleted_at')->orderBy('sort_order')->get()->toArray();

            foreach ($survey_question_data as $survey_quest_row) {

                $survey_heading[] = $survey_quest_row['info'][0]['value'];
                $survey_heading[] = 'Comments';
            }

        }

        $survey_records[] = $survey_heading;

        // Add data
        $attendees = EventSurveyResult::where('event_id', '=', $formInput['event_id'])->where('survey_id', $id)->groupBy('attendee_id')->get()->toArray();

        $attendee_ids = array_column($attendees, 'attendee_id');

        $result_attendee = EventAttendee::where('event_id', '=', $formInput['event_id'])->whereIn('attendee_id', $attendee_ids)->with('attendee')->whereNull('deleted_at')->get()->toArray();
        foreach($result_attendee as $row) {
            $survey_row = [];
            $survey_row[] =  $row['attendee']['first_name'];
            $survey_row[] = $row['attendee']['last_name'];
            $survey_row[] = $row['attendee']['email'];

            foreach ($survey_data as $st){

                $survey_row[] = $st['info'][0]['value'];

                $survey_reslt_question_data = EventSurveyQuestion::where('survey_id', '=', $st['id'])->where('is_anonymous', 0)->where('question_type','!=','matrix')->whereNull('deleted_at')->orderBy('sort_order')->get()->toArray();

                foreach ($survey_reslt_question_data as $qt){

                    $question_type = $qt['question_type'];

                    $survey_result = EventSurveyResult::where('question_id', '=', $qt['id'])->where('survey_id', '=', $st['id'])->where('attendee_id', '=', $row['attendee']['id'])->where('event_id', '=', $formInput['event_id'])->get()->toArray();

                    if(count($survey_result) > 0){

                        $answers = EventSurveyAnswer::where('id', '=', $survey_result[0]['answer_id'])->with(['info' => function ($query) use ($formInput) {
                            return $query->where('languages_id', '=', $formInput['language_id']);
                        }])->orderBy('sort_order')->get()->toArray();

                        if ($survey_result[0]['answer'] != '') {
                            $q_anwser = $survey_result[0]['answer'];
                        } else {

                            if($question_type == 'multiple'){
                                $q_anwser_arr = [];
                                foreach ($survey_result as $pr){
                                    $answers = EventSurveyAnswer::where('id', '=', $pr['answer_id'])->with(['info' => function ($query) use ($formInput){
                                        return $query->where('languages_id', '=', $formInput['language_id']);
                                    }])->whereNull('deleted_at')->orderBy('sort_order')->get()->toArray();

                                    $q_anwser_arr[] = $answers[0]['info'][0]['value'];
                                }

                                $q_anwser = implode(',',$q_anwser_arr);
                            }else{
                                $q_anwser = $answers[0]['info'][0]['value'];
                            }

                        }

                        $survey_row[] = $q_anwser;

                       $survey_row[] = $survey_result[0]['comment'];
                        $y++;

                    } else {
                        $survey_row[] = '';
                        $survey_row[] = '';
                    }

                }

            }

            $survey_records[] = $survey_row;
            $survey_row = [];
        }
        return $survey_records;
    }

    /**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function getSurveyListing($formInput, $id)
	{
		$event_id= $formInput["event_id"];
		$lang_id =$formInput["language_id"];
        $current_date       = date('Y-m-d H:i:s');
        $result = \App\Models\EventSurvey::where('event_id', '=', $event_id)->where('status', '=', '1')
        ->where(function ($query) use ($current_date) {
            return $query->where(function ($query) use ($current_date) {
                return $query->where('start_date', '<=' , $current_date)
                    ->where('end_date', '>=' , $current_date);
            })->orWhere(function ($query) use ($current_date) {
                return $query->where('start_date', '=' , '0000-00-00 00:00:00')
                    ->where('end_date', '=' , '0000-00-00 00:00:00');
            })->orWhere(function ($query) use ($current_date) {
                return $query->where('start_date', '<=' , $current_date)
                    ->where('end_date', '=' , '0000-00-00 00:00:00');
            });
        })->with(['info'=> function($query) use($lang_id) {
           return $query->where('languages_id', '=', $lang_id);
        }, 'question', 'question.results' => function($q) use($id) { return $q->where("attendee_id", $id);}])->whereNull('deleted_at')->get()->toArray();

        $surveyArray = array();
        foreach($result as $survey){
            $survey_groups= \App\Models\EventSurveyGroup::where('survey_id', $survey['id'])->first();
            if($survey_groups){
                $group_attendee= \App\Models\EventAttendeeGroup::whereIn('group_id', function($query) use($survey, $id) {
					$query->select('group_id')
					->from(with(new EventSurveyGroup())->getTable())
					->where('survey_id', $survey["id"])
					->where('attendee_id', $id);
				})->get()->toArray();

                if(count($group_attendee)){
                    $survey['available'] = 'yes';
                }else{
                    $survey['available'] = 'no';
                }

            } else {
                $survey['available'] = 'yes';
            }
            $survey_result = 0;
            $question_count = 0;
            foreach ($survey['question'] as $key => $question) {
                if( ($question['start_date'] == '0000-00-00 00:00:00' || $question['start_date'] <= date('Y-m-d H:i:s')) && ($question['end_date'] == '0000-00-00 00:00:00' || $question['end_date'] > date('Y-m-d H:i:s'))){
                    if(count($question['results']) > 0){
                        if($question['question_type'] === 'world_cloud'){
                            if($question['is_participants_multiple_times'] != 1){
                                $survey_result++;
                            }
                        }
                        else{
                            $survey_result++;
                        }
                    }
                    $question_count++;
                }
            }
            $survey['info'] = readArrayKey($survey, [], 'info');
            
            if($formInput['WITHOUT_QUESTION']){
                unset($survey['question']);
            }

            if($formInput['ALL_SURVEYS'] == 1){
                $survey['complete_answered'] = $survey_result == $question_count ? true : false;  
                $survey['answered'] = $survey_result > 0 ? true : false;  
                $surveyArray[]=$survey;
            }
            elseif($formInput['COMPLETE_ANSWERED_SURVEYS'] == 1) {
                if($survey_result == $question_count){
                    $survey['complete_answered'] = true;  
                    $surveyArray[]=$survey;
                }
            }
            else{
                $survey['answered'] = $survey_result > 0 ? true : false;  
                $surveyArray[]=$survey;
            }
        }

		$temp_array = array();
        foreach($surveyArray as $row){
            if( (($row['start_date'] == '0000-00-00 00:00:00' || $row['start_date'] <= date('Y-m-d H:i:s')) && ($row['end_date'] == '0000-00-00 00:00:00' || $row['end_date'] > date('Y-m-d H:i:s'))) && $row['available'] === 'yes'){
                $temp_array[] = $row;
            }
        }

        $surveys =  $temp_array;

        return $surveys;
	}
    public function surveyResultByPointsExport($form_data){
        $sql = "SELECT score.*, sum(score.score) as sub_total, a_detail.first_name, a_detail.last_name,a.attendee_id FROM `conf_event_survey_results_score` score INNER JOIN `conf_event_attendees` a ON a.attendee_id = score.attendee_id INNER JOIN `conf_attendees` a_detail ON a_detail.id = a.attendee_id INNER JOIN `conf_event_survey_questions` q ON q.id = score.question_id INNER JOIN `conf_event_surveys` s ON s.id = q.survey_id Where a.event_id  = '".$form_data['event_id']."' AND q.is_anonymous = 0 AND q.question_type != 'matrix' AND a.deleted_at is null AND score.deleted_at is null AND a.event_id = score.event_id GROUP BY a.id ORDER BY sub_total DESC";

        $d_records   =  \DB::select(\DB::raw($sql));
        $result =  object_to_array($d_records);
        $survey_result[]=array('Attendees','Points');
        foreach($result as $row)
        {
            $attendee_name = stripslashes($row['first_name']).' '.stripslashes($row['last_name']);
            $survey_result[]=array($attendee_name,$row['sub_total']);
        }
        return $survey_result;
    }
	public function getSurveyDetail($formInput, $id, $attendee_id){
        $wolrd_clound_questions= \App\Models\EventSurveyQuestion::where('survey_id',$id)
            ->where('conf_event_survey_questions.status',1)
            ->where('question_type','=','world_cloud')->where('is_participants_multiple_times','=',1)
            ->pluck('id');
        $survey_result = \App\Models\EventSurveyAttendeeResult::where('survey_id', '=', $id)
                        ->where('event_id', '=', $formInput['event_id'])
                        ->where('attendee_id', '=', $attendee_id);
        if(count($wolrd_clound_questions)){
            $survey_result=$survey_result->whereNotIn('question_id',$wolrd_clound_questions);
        }
        $survey_result=$survey_result->whereNull('deleted_at')->get()->toArray();

        $question_ids = '';
        foreach ($survey_result as $row) {
            $question_ids .= $row['question_id'].',';
        }

        $question_ids = rtrim($question_ids, ',');

        if($question_ids != ''){
            $survey_q = "AND conf_event_survey_questions.id NOT IN($question_ids)";
        }


        $current_date       = date('Y-m-d H:i:s');
        $q_date_check = " AND ( ('".$current_date."' BETWEEN conf_event_survey_questions.start_date AND conf_event_survey_questions.end_date) || ( conf_event_survey_questions.start_date = '0000-00-00 00:00:00' AND conf_event_survey_questions.end_date = '0000-00-00 00:00:00') || (conf_event_survey_questions.start_date <= '".$current_date."' AND conf_event_survey_questions.end_date >= '".$current_date."') || (conf_event_survey_questions.start_date <= '".$current_date."' AND conf_event_survey_questions.end_date = '0000-00-00 00:00:00')) ";




        $survey_question = \DB::select(\DB::raw("SELECT conf_event_survey_questions.*, conf_survey_question_info.name, conf_survey_question_info.value FROM conf_event_survey_questions, conf_survey_question_info WHERE conf_event_survey_questions.survey_id = '".$id."' $survey_q AND conf_event_survey_questions.id = conf_survey_question_info.question_id AND conf_event_survey_questions.status = '1' AND conf_survey_question_info.languages_id = '".$formInput['language_id']."' AND conf_event_survey_questions.deleted_at is null $q_date_check "));
        $result  = object_to_array($survey_question);

        $survey_groups= \DB::select(\DB::raw("SELECT * FROM conf_event_surveys_groups WHERE survey_id='".$id."' AND deleted_at is null"));

        if(count($survey_groups)){

            $group_attendee= \DB::select(\DB::raw("SELECT * FROM `conf_event_attendees_groups` WHERE `group_id` in (SELECT group_id FROM `conf_event_surveys_groups` WHERE `survey_id`=".$id." and attendee_id=".$attendee_id." AND deleted_at is null) AND deleted_at is null"));

            if(count($group_attendee)){
                $display = 'yes';
            }else{
                $display = 'no';
            }

        } else {
            $display = 'yes';
        }

        $i = 0;
        foreach ($result as $val) {


            $answers = \App\Models\EventSurveyAnswer::where('question_id', '=', $val['id'])->with(['info' => function ($query) use($formInput) {
                return $query->where('languages_id', '=', $formInput['language_id']);
            }])->whereNull('deleted_at')->orderBy('sort_order')->get()->toArray();

            $matrix = \App\Models\EventSurveyMatrix::where('question_id', '=', $val['id'])->orderBy('sort_order')->get()->toArray();

            $temp = array();

            foreach ($answers as $row3) {
                    $temp[] = ['id' => $row3['id'], 'sort_order' => $row3['sort_order'], 'status' => $row3['status'], 'created_at' => $row3['created_at'], 'updated_at' => $row3['updated_at'], 'deleted_at' => $row3['deleted_at'], 'answer' => $row3['info'][0]['value'], 'correct' => $row3['correct']];

                }

            $result[$i]['display'] = $display;
            $result[$i]['answer'] = $temp;
            $result[$i]['matrix'] = $matrix;
            $i++;
        }


		 $result;
        // $poll_survey_no_record = $this->surveyRepository->getFrantSurveyNoRecord($id);
		$survey_result = \App\Models\EventSurveyResult::where('survey_id', '=', $id)->where('event_id', '=', $formInput['event_id'])->where('attendee_id', '=', $attendee_id)->whereNull('deleted_at')->get()->toArray();

        $survey_details = array_values(Arr::sort($result, function($value)
        {
            return $value['sort_order'];
        }));

        $result = \App\Models\EventSurvey::where('id', '=', $id)->where('status', '=', '1')->with(['info'=> function($query) use($formInput) {
            return $query->where('languages_id', '=', $formInput['language_id']);
         }])->whereNull('deleted_at')->get()->toArray();
         
         $survey = $result[0];
         $survey['info'] = readArrayKey($survey, [], 'info');

		return  [
			"survey_details" => $survey_details,
			"survey_result" => $survey_result,
            'survey' => $survey
		];
	}

	public function saveSurveyDetail($formInput, $id, $attendee_id){
		$event_id = $formInput['event_id'];
        $organizer_id = $formInput['organizer_id'];
		$sql = 'INSERT INTO conf_event_survey_results (event_id, survey_id, question_id, answer_id, answer, attendee_id, created_at, updated_at, comment, status) VALUES ';
        $survey_id = $formInput['survey_id'];
		$questionType = $formInput['questionsType'];
        $subSql = '';
        $subSql_score 	= '';
        $shouldSave = true;

        $poll_error = false;

        foreach($formInput['questions'] AS $q)
        {
            if ($formInput['optionals'][$q] !=0) {

                //check answers
                if (isset($formInput['answer'.$q])) {
                    if(count($formInput['answer'.$q]) == 0)
                    {
                        $shouldSave = false;
                        break;
                    }
                } else if (isset($formInput['answer_dropdown'.$q])) {
					if ($formInput['answer_dropdown'.$q][0] == 0 && $formInput['optionals'][$q] == '1') {
						$shouldSave = false;
						break;
					}
				} else if (isset($formInput['answer_open'.$q])) {
                    if($formInput['answer_open'.$q][0] == '')
                    {
                        $shouldSave = false;
                        break;
                    }
                } else if (isset($formInput['answer_number'.$q])) {
					if($formInput['answer_number'.$q][0] == '')
					{
						$shouldSave = false;
						break;
					}
				} else if (isset($formInput['answer_date'.$q])) {
					if($formInput['answer_date'.$q][0] == '')
					{
						$shouldSave = false;
						break;
					}
				}  else if (isset($formInput['answer_date_time'.$q])) {
					if($formInput['answer_date_time'.$q][0] == '')
					{
						$shouldSave = false;
						break;
					}
				}
				else {
					$i =0;
					foreach ($formInput['answers'.$q] AS $ans) {
						if (isset($formInput['matrix' . $q . '_' . $ans])) {
							$i++;
						}
					}
					if($i == 0) {
						$shouldSave = false;
						break;
					}
				}
            }

            //check Question  expiry date
            $current_date     = date('Y-m-d H:i:s');
            $q_date_check     = " AND ( ('".$current_date."' BETWEEN start_date AND end_date) || ( start_date = '0000-00-00 00:00:00' AND end_date = '0000-00-00 00:00:00') || (start_date <= '".$current_date."' AND end_date >= '".$current_date."')  || (conf_event_survey_questions.start_date <= '".$current_date."' AND conf_event_survey_questions.end_date = '0000-00-00 00:00:00')) ";

            $q_date_check = "SELECT * FROM conf_event_survey_questions WHERE status = '1' $q_date_check AND deleted_at is null AND id=".$q.$q_date_check;

            $q_active       = \DB::select(\DB::raw($q_date_check));
            $q_active = count($q_active);

            if ($q_active < 1) {

                $poll_error  = true;
                $shouldSave = false;
                break;
            }
        }

		if($shouldSave)
        {
			$marked_question = 0;
            $ans = array();
            foreach($formInput['questions'] AS $question)
            {

                $submitted_question = \App\Models\EventSurveyAnswer::where('question_id', '=', $question)->whereNull('deleted_at')->get()->toArray();

                foreach($submitted_question as $submitted_question_row){
                    if($submitted_question_row['correct'] == 1){
                        $marked_question = 1;
                    }
                }


                // $query = "SELECT * FROM conf_event_survey_questions WHERE id = '".$question."' AND deleted_at is null";
                // $query       = \DB::select(\DB::raw($query));
                // $questionType =  object_to_array($query);
				$anonymous_attendee_id = $attendee_id;

                if($questionType[$question] == 'single')
                {

                    if(count($formInput['answer'.$question]) > 0)
                    {

                        foreach($formInput['answer'.$question] AS $answer)
                        {
                            $ans = $formInput['comments'.$question][0] ?? "";
                            if($answer != '')
                            {
                                $insertSingle = 1;
                                $subSql .= '("'.$event_id.'", "'.$survey_id.'", "'.addslashes($question).'", "'.addslashes($answer).'", "", "'.$attendee_id.'", "'.date('Y-m-d H:i:s'). '", "' . date('Y-m-d H:i:s') . '", "'.addslashes($ans).'", "1"),';
                                break;
                            }

                        }

                    }
                }



                //check for question type multiple

                if($questionType[$question] == 'multiple')
                {

                    if(count($formInput['answer'.$question]) > 0)
                    {

                        foreach($formInput['answer'.$question] AS $answer)
                        {
                            $ans = $formInput['comments'.$question][0] ?? "";
                            if($answer != '')
                            {

                                $insertMultiple = 1;
                                $subSql .= '("'.$event_id.'", "'.$survey_id.'", "'.addslashes($question).'", "'.addslashes($answer).'", "", "'.$attendee_id.'", "'.date('Y-m-d H:i:s'). '", "' . date('Y-m-d H:i:s') . '", "'.addslashes($ans).'", "1"),';
                            }
                        }
                    }
                }

				 //check for question type matrix

				 if ($questionType[$question] == 'matrix') {
					foreach ($formInput['answer' . $question] AS $input_answer) {
						foreach ($formInput['matrix' . $question.'_'.$input_answer] AS $answer) {
							if ($answer != '') {
								$matrix_answer = explode("-", $answer);
								$answer_id = $matrix_answer[0];
								$matrix_id = $matrix_answer[1];

								if ($answer_id > 0) {
									$subSql .= '("'.$event_id.'", "'.$survey_id.'", "'.addslashes($question).'", "'.addslashes($answer).'", "'.$matrix_id.'", "'.$anonymous_attendee_id.'", "'.date('Y-m-d H:i:s').'", "'.date('Y-m-d H:i:s').'", "'.$ans.'", "1"),';
								}
							}
						}
					}
				}

				//check for question type dropdown

				if($questionType[$question] == 'dropdown')
				{

					if(count($formInput['answer_dropdown'.$question]) > 0)
					{

						foreach($formInput['answer_dropdown'.$question] AS $answer)
						{
							$ans = $formInput['comments'.$question][0] ?? "";
							if($answer != '')
							{

								$insertMultiple = 1;
								$subSql .= '("'.$event_id.'", "'.$survey_id.'", "'.addslashes($question).'", "'.addslashes($answer).'", "", "'.$attendee_id.'", "'.date('Y-m-d H:i:s'). '", "' . date('Y-m-d H:i:s') . '", "'.addslashes($ans).'", "1"),';
							}
						}
					}
				}


                //check for question type open
				if($questionType[$question] == 'open')
				{
					if(isset($formInput['answer_open'.$question]) && count($formInput['answer_open'.$question]) > 0)
					{

						foreach($formInput['answer_open'.$question] AS $answer)
						{
							$ans = $formInput['comments'.$question][0] ?? "";
							if(trim($answer) != '')
							{
								$insertOpen = 1;
								$subSql .= '("'.$event_id.'", "'.$survey_id.'", "'.addslashes($question).'", "0", "'.addslashes($answer).'", "'.$attendee_id.'", "'.date('Y-m-d H:i:s'). '", "' . date('Y-m-d H:i:s') . '", "'.addslashes($ans).'", "1"),';


							}
						}
					}
				}

				//check for question type number
				if($questionType[$question] == 'number')
				{
					if(isset($formInput['answer_number'.$question]) && count($formInput['answer_number'.$question]) > 0)
					{
						foreach($formInput['answer_number'.$question] AS $answer)
						{
							$ans = $formInput['comments'.$question][0] ?? "";
							if(trim($answer) != '')
							{
								$insertOpen = 1;
								$subSql .= '("'.$event_id.'", "'.$survey_id.'", "'.addslashes($question).'", "0", "'.addslashes($answer).'", "'.$attendee_id.'", "'.date('Y-m-d H:i:s'). '", "' . date('Y-m-d H:i:s') . '", "'.addslashes($ans).'", "1"),';
							}
						}
					}
				}

				//check for question type date
				if($questionType[$question] == 'date')
				{
					if(isset($formInput['answer_date'.$question]) &&  count($formInput['answer_date'.$question]) > 0)
					{
						foreach($formInput['answer_date'.$question] AS $answer)
						{
							$ans = $formInput['comments'.$question][0] ?? "";
							if(trim($answer) != '')
							{
								$insertOpen = 1;
								$subSql .= '("'.$event_id.'", "'.$survey_id.'", "'.addslashes($question).'", "0", "'.addslashes($answer).'", "'.$attendee_id.'", "'.date('Y-m-d H:i:s'). '", "' . date('Y-m-d H:i:s') . '", "'.addslashes($ans).'", "1"),';
							}
						}
					}
				}

				//check for question type date time
				if($questionType[$question] == 'date_time')
				{
					if(isset($formInput['answer_date_time'.$question]) &&  count($formInput['answer_date_time'.$question]) > 0)
					{
						foreach($formInput['answer_date_time'.$question] AS $answer)
						{
							$ans = $formInput['comments'.$question][0] ?? "";
							if(trim($answer) != '')
							{
								$insertOpen = 1;
								$subSql .= '("'.$event_id.'", "'.$survey_id.'", "'.addslashes($question).'", "0", "'.addslashes($answer).'", "'.$attendee_id.'", "'.date('Y-m-d H:i:s'). '", "' . date('Y-m-d H:i:s') . '", "'.addslashes($ans).'", "1"),';
							}
						}
					}
				}

                //check scrore
					if(isset($formInput['answer'.$question]) &&  count($formInput['answer'.$question]) > 0)
					{
						$score		= $this->getScore(addslashes($question), $formInput['answer'.$question]);
						$subSql_score .= '("'.$survey_id.'", "'.addslashes($question).'", "'.$attendee_id.'", "'.$score.'", "'.$event_id.'", "1", "'.date('Y-m-d H:i:s'). '", "' . date('Y-m-d H:i:s') . '"),';
					}
			
					if(isset($formInput['answer_dropdown'.$question]) &&  count($formInput['answer_dropdown'.$question]) > 0)
					{
						$score		= $this->getScore(addslashes($question), $formInput['answer_dropdown'.$question]);
						$subSql_score .= '("'.$survey_id.'", "'.addslashes($question).'", "'.$attendee_id.'", "'.$score.'", "'.$event_id.'", "1", "'.date('Y-m-d H:i:s'). '", "' . date('Y-m-d H:i:s') . '"),';
					}
				

            }
            if($subSql != "")
            {

                $subSql = substr_replace($subSql,'',-1);
                $sql .= $subSql;


                //Score
                $subSql_score = substr_replace($subSql_score,'',-1);

                if($subSql_score){
                    $sql_score 		= 'INSERT INTO conf_event_survey_results_score (survey_id, question_id, attendee_id, score, event_id, status, created_at, updated_at) VALUES ' .$subSql_score;
                }
                $if_answered = "Select * from conf_event_survey_results WHERE event_id=".$event_id." and attendee_id=".$attendee_id." and survey_id=".$survey_id." and question_id=".addslashes($question)." AND deleted_at is null";
                $if_answered       = \DB::select(\DB::raw($if_answered));
                $if_answered_result =  object_to_array($if_answered);

                if(!count($if_answered_result)){
                    \DB::insert(\DB::raw($sql));
                    if(trim($sql_score) != '') {
                    \DB::insert(\DB::raw($sql_score));
					return ["status" => true , "message" => 'Survey Answered successfully'];
                    }
                }
                else{
                    return ["status" => false , "message" => 'Survey Already Answered'];
                }
            }           
            
        }
        else
        {
            return ["status" => false , "message" => 'Something went wrong'];
        }
	}

	function getScore($p_questionID, $answer_id_submitted)
    {
        $is_true = false;

        //check question type

        $q_type = "SELECT * FROM conf_event_survey_questions WHERE id = ".$p_questionID." AND deleted_at is null";
        $q_type       = \DB::select(\DB::raw($q_type));
        $row_q =  object_to_array($q_type);



        if($row_q[0]['question_type'] == 'single')
        {
            $sql_anwer		= "SELECT * FROM conf_event_survey_answers WHERE question_id = ".$p_questionID." AND id = ".$answer_id_submitted[0]." AND deleted_at is null";
        }else
        {
            $sql_anwer		= "SELECT * FROM conf_event_survey_answers WHERE question_id = ".$p_questionID." AND correct = 1 AND deleted_at is null";
        }


        $pollresults       = \DB::select(\DB::raw($sql_anwer));
        $pollresults =  object_to_array($pollresults);
        $total_ans = count($pollresults);



        if(count($answer_id_submitted) == $total_ans)//if user select all the checkboxes answers
        {
            foreach($pollresults as $row)
            {

                if(in_array($row['id'], $answer_id_submitted))
                {
                    if($row['correct']==1)
                    {
                        $is_true = true;
                    }

                }else
                {
                    $is_true = false;
                    break;
                }

            }
        }else
        {
            $is_true = false;

        }

        if($is_true==true)
            return '1';
        else
            return '0';
    }
    public function attendeeSurveyScore($event_id,$survey_id)
    {

        $survey_setting = PollSetting::where('event_id', '=', $event_id)->whereNull('deleted_at')->first();
        $limit = $survey_setting['projector_attendee_count'];
        if($limit == 0){
            $limit = 10;
        }
        $event = Events::find($event_id);
        $language_id = $event->language_id;
        $survey_questions = EventSurveyQuestion::where('survey_id',$survey_id)->where('is_anonymous',0)->where(function ($query)  {
            return $query->where('question_type','single')->orWhere('question_type','multiple')->orWhere('question_type','dropdown');
        })->pluck('id');
        $score = EventSurveyResultScore::whereIn('question_id',$survey_questions)
            ->selectRaw("SUM(score) as total_score,attendee_id")
            ->groupBy('attendee_id')
            ->orderBy('total_score','desc')
            ->limit($limit)
            ->with(['attendee.eventAttendees'=>function($query)use($event_id){
                return $query->where('event_id','=',$event_id)->where('type_resource','=',0);
            }])
            ->get()->toArray();
        $total_score=[];
        $name=[];
        foreach ($score as $key=>$data){
            $attendee_info = AttendeeInfo::where('attendee_id',$data['attendee']['id'])->where('languages_id',$language_id)->where(function ($query)  {
                return $query->where('name','=','company_name')->orWhere('name','=','title');
            })->get()->toArray();
            $score[$key]['attendee']['info'] = $attendee_info;
            $score[$key]['first_name'] = $data['attendee']['first_name'];
            $total_score[$key]  = $data['total_score'];
            $name[$key] = $data['attendee']['first_name'];
        }
            array_multisort($total_score, SORT_DESC, $name, SORT_ASC, $score);
        return $score;
    }
    public function getSurvey($id,$request){

        $result = EventSurvey::where('id', '=', $id)->with(['info' => function ($query) use($request){
            return $query->where('languages_id', '=', $request['language_id']);
        }, 'groups'])->whereNull('deleted_at')->get()->toArray();

        return $result;
    }
}
