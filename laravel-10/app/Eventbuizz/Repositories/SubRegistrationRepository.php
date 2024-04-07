<?php

namespace App\Eventbuizz\Repositories;

use \App\Mail\Email;
use App\Models\EventSubregistrationMatrix;
use App\Models\EventSubRegistrationResult;
use App\Models\EventSurveyMatrix;
use Illuminate\Http\Request;

class SubRegistrationRepository extends AbstractRepository
{
    private $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * content copy / when new event create / cloning event
     *
     * @param array
     */
    public function install($request)
    {
        $from_sub_registrations = \App\Models\EventSubRegistration::where('event_id', $request['from_event_id'])->with('question')->get();
      
        if ($from_sub_registrations) {

            foreach($from_sub_registrations as $from_sub_registration) {

                if($from_sub_registration->registration_form_id > 0) {
                    $to_sub_registration = $from_sub_registration->replicate();
                    $to_sub_registration->event_id = $request['to_event_id'];
                    if (session()->has('clone.event.event_registration_form.' . $from_sub_registration->registration_form_id) && $from_sub_registration->registration_form_id > 0) {
                        $to_sub_registration->registration_form_id = session()->get('clone.event.event_registration_form.' . $from_sub_registration->registration_form_id);
                    }
                    $to_sub_registration->save();
                } else {
                    $to_sub_registration = \App\Models\EventSubRegistration::where('event_id', $request['to_event_id'])->where('registration_form_id', 0)->first();
                    if(!$to_sub_registration) {
                        $to_sub_registration = new \App\Models\EventSubRegistration();
                        $to_sub_registration->event_id = $request['to_event_id'];
                        $to_sub_registration->status = $from_sub_registration->status;
                        $to_sub_registration->save();
                    }
                } 
    
                //Questions
                $questions = $from_sub_registration->question()->get();
    
                foreach ($questions as $question) {
    
                    $duplicate = $question->replicate();
                    $duplicate->sub_registration_id = $to_sub_registration->id;
                    $duplicate->save();
                    $to_question_id = $duplicate->id;
    
                    //Question info
                    $infos = $question->info()->get();
                    foreach ($infos as $info) {
                        $duplicate = $info->replicate();
                        $duplicate->question_id = $to_question_id;
                        $duplicate->save();
                    }
    
                    //Answers
                    $anwers = $question->answer()->get();
    
                    foreach ($anwers as $anwers) {
                        $duplicate = $anwers->replicate();
                        $duplicate->question_id = $to_question_id;
                        if (session()->has('clone.event.programs.' . $anwers->link_to)) {
                            $duplicate->link_to = session()->get('clone.event.programs.' . $anwers->link_to);
                        } else {
                            $duplicate->link_to = 0;
                        }
                        $duplicate->save();
                        $to_answer_id = $duplicate->id;
    
                        //Answer info
                        $infos = $anwers->info()->get();
                        foreach ($infos as $info) {
                            $duplicate = $info->replicate();
                            $duplicate->answer_id = $to_answer_id;
                            $duplicate->save();
                        }
                    }
                    
                }

            }
        }
    }

    /**
     *sub registrations listing
     * @param array
     */
    public function listing($formInput)
    {
        $sub_registration = \App\Models\EventSubRegistration::where('event_id', '=', $formInput['event_id'])
            ->with('question')
            ->first()
            ->toArray();

        if (!empty($sub_registration)) {
            $sub_registration['results'] = \App\Models\EventSubRegistrationResult::where('event_id', '=', $formInput['event_id'])->where('sub_registration_id', '=', $sub_registration['id'])
                ->groupBy('attendee_id')
                ->get();
        }

        return $sub_registration;
    }

    /**
     *sub registrations questions
     * @param array
     * @param int
     */
    public function questions($formInput, $id)
    {
        $query = \App\Models\EventSubRegistrationQuestion::where('sub_registration_id', '=', $id)
            ->with([
                'info'   => function ($query) use ($formInput) {
                    return $query->where('languages_id', '=', $formInput['language_id']);
                },
                'matrix' => function ($query) {
                    $query->orderBy('sort_order');
                }])
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC');

        if (isset($formInput['question_id']) && $formInput['question_id']) {
            $query->where('id', $formInput['question_id']);
        }

        $result = $query->get();

        foreach ($result as $key => $val) {
            $answer_array = array();

            if (in_array($val->question_type, ["single", "", "multiple", "dropdown", "matrix"])) {
                $query = \App\Models\EventSubRegistrationAnswer::join('conf_event_sub_registration_answer_info', function ($join) use ($formInput) {
                    $join->on('conf_event_sub_registration_answer_info.answer_id', '=', 'conf_event_sub_registration_answers.id')
                        ->where('conf_event_sub_registration_answer_info.languages_id', $formInput['language_id']);
                })
                    ->leftJoin('conf_event_sub_registration_results', function ($join) use ($val) {
                        $join->on('conf_event_sub_registration_results.answer_id', '=', 'conf_event_sub_registration_answers.id')
                            ->where('conf_event_sub_registration_results.question_id', $val['id']);
                    })
                    ->where('conf_event_sub_registration_answers.question_id', '=', $val['id'])
                    ->select('conf_event_sub_registration_answers.*', 'conf_event_sub_registration_answer_info.value', \DB::raw('count(conf_event_sub_registration_results.id) as result_count'))
                    ->groupBy('conf_event_sub_registration_answers.id');

                if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && $formInput['sort_by'] == "result_count")) {
                    $query->orderBy("result_count", $formInput['order_by']);
                } else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && $formInput['sort_by'] == "value")) {
                    $query->orderBy("conf_event_sub_registration_answer_info.value", $formInput['order_by']);
                } else {
                    $query->orderBy('conf_event_sub_registration_answers.sort_order');
                    $query->orderBy('conf_event_sub_registration_answers.id', 'ASC');
                }

                $answers = $query->get()->toArray();

                foreach ($answers as $row) {
                    $answer_array[]  = [
                        'id' => $row['id'],
                        'status' => $row['status'],
                        'value' => $row['value'],
                        'correct' => $row['correct'],
                        'sort_order' => $row['sort_order'],
                        'result_count' => $row['result_count'],
                    ];
                }
                $result[$key]['answer'] = $answer_array;
            } else {
                $answer = \App\Models\EventSubRegistrationResult::where('question_id', $val['id'])->first();
                $result[$key]['answer'] = $answer->answer;
            }
            $q_responses = $this->getQuestionResultCount($val['id']);
            $info = readArrayKey($val, [], 'info');
            $result[$key]['question'] = (isset($info['question']) ? $info['question'] : '');
            $result[$key]['q_responses'] = $q_responses;
        }

        return $result;
    }

    public function getQuestionResultCount($id){
        $data = EventSubRegistrationResult::where('question_id', $id)->groupBy('attendee_id')->get();
        return $data->count();
    }

    /**
     *create question
     * @param array
     * @param object
     */
    public function question_store($formInput, $sub_registration)
    {
        $sort_order = \App\Models\EventSubRegistrationQuestion::where('sub_registration_id', $sub_registration->id)->max('sort_order');
        $sort_order = $sort_order + 1;
        $languages = get_event_languages($formInput['event_id']);
        set_event_timezone($formInput['event_id']);

        $question = new \App\Models\EventSubRegistrationQuestion(array(
            'question_type' => $formInput['question_type'],
            'max_options' => $formInput['max_options'] ? $formInput['max_options'] : 0,
            'min_options' => $formInput['min_options'] ? $formInput['max_options'] : 0,
            'required_question' => (isset($formInput['required_question']) ? $formInput['required_question'] : '0'),
            'enable_comments' => (isset($formInput['enable_comments']) ? $formInput['enable_comments'] : '0'),
            'sort_order' => $sort_order,
            'status' => 1,
            'link_to' => '0',
            'max_options' => (isset($formInput['max_options']) ? $formInput['max_options'] : 0)
        ));

        $question_instance = $sub_registration->question()->save($question);

        foreach ($languages as $language) {
            $info[] = new \App\Models\EventSubRegistrationQuestionInfo(array(
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
                    $question_answer = new \App\Models\EventSubRegistrationAnswer(array(
                        'correct' => $correct,
                        'status' => 1,
                        'link_to' => 0
                    ));

                    $answer_obj = $question_instance->answer()->save($question_answer);

                    foreach ($languages as $language) {
                        $answer_info = new \App\Models\EventSubRegistrationAnswerInfo(array(
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
                    $matrix = new EventSubregistrationMatrix(array('name' => $column['value']));
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
            $question->max_options = $formInput['max_options'] ? $formInput['max_options'] : 0;
            $question->min_options = $formInput['min_options'] ? $formInput['min_options'] : 0;
            $question->required_question = (isset($formInput['required_question']) ? $formInput['required_question'] : '0');
            $question->enable_comments = (isset($formInput['enable_comments']) ? $formInput['enable_comments'] : '0');
            $question->link_to = '0';
            $question->save();

            //save question info
            $question_info = \App\Models\EventSubRegistrationQuestionInfo::where('question_id', $question->id)->where('name', 'question')->where('languages_id', $formInput['language_id'])->first();
            if ($question_info) {
                $question_info->value = $formInput['question'];
                $question_info->save();
            }

            //save answer
            $sort = 0;
            if (!empty($formInput['answer'])) {
                foreach ($formInput['answer'] as $key => $answer) {
                    $answer_id = (isset($answer['id']) ? $answer['id'] : 0);
                    $correct = (isset($answer['correct']) && $answer['correct'] ? $answer['correct'] : 0);
                    $answer = (isset($answer['value']) && $answer['value'] ? $answer['value'] : NULL);
                    if ($answer) {
                        $question_answer = \App\Models\EventSubRegistrationAnswer::where('question_id', $question->id)->where('id', $answer_id)->first();
                        if ($question_answer) {
                            $question_answer->correct = $correct;
                            $question_answer->link_to = '0';
                            $question_answer->sort_order = $sort;
                            $question_answer->save();

                            $answer_info =  \App\Models\EventSubRegistrationAnswerInfo::where('languages_id', $formInput['language_id'])->where('answer_id', $question_answer->id)->first();

                            if ($answer_info) {
                                $answer_info->value = $answer;
                                $answer_info->save();
                            } else {
                                $answer_obj = $question->answer()->save($question_answer);

                                $answer_info = new \App\Models\EventSubRegistrationAnswerInfo(array(
                                    'name' => 'answer',
                                    'value' => $answer,
                                    'languages_id' => $formInput['language_id'],
                                    'status' => 1
                                ));

                                $answer_obj->info()->save($answer_info);
                            }
                        } else {
                            $question_answer = new \App\Models\EventSubRegistrationAnswer(array(
                                'correct' => $correct,
                                'status' => 1,
                                'sort_order' => $sort,
                                'link_to' => '0'
                            ));

                            $answer_obj = $question->answer()->save($question_answer);

                            foreach ($languages as $language) {
                                $answer_info = new \App\Models\EventSubRegistrationAnswerInfo(array(
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
                        $matrix = EventSubregistrationMatrix::where('question_id', $question->id)->where('id', $column_id)->first();

                        if(!$matrix){
                            $matrix =  new EventSubregistrationMatrix();
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
    public function question_destroy($question_id)
    {
        //delete question
        \App\Models\EventSubRegistrationQuestion::where('id', $question_id)->delete();
        \App\Models\EventSubRegistrationQuestionInfo::where('question_id', $question_id)->delete();

        //delete answer
        $answers = \App\Models\EventSubRegistrationAnswer::where('question_id', $question_id)->get();
        foreach ($answers as $row) {
            \App\Models\EventSubRegistrationAnswerInfo::where('answer_id', $row->id)->delete();
        }

        \App\Models\EventSubRegistrationAnswer::where('question_id', $question_id)->delete();

        //delete results
        \App\Models\EventSubRegistrationResult::where('question_id', $question_id)->delete();
    }

    /**
     *delete question option
     * @param int
     */
    public function question_option_destroy($option_id)
    {
        \App\Models\EventSubRegistrationAnswer::find($option_id)->delete();
        \App\Models\EventSubRegistrationAnswerInfo::where('answer_id', $option_id)->delete();
    }

    /**
     *delete question option
     * @param int
     */
    public function question_matrix_option_destroy($option_id)
    {
        \App\Models\EventSubregistrationMatrix::find($option_id)->delete();
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
                $question = \App\Models\EventSubRegistrationQuestion::query();
                $model = $question->find($row['id']);
                $model->sort_order = $sort;
                $model->save();
                $sort++;
            }
        }

        return true;
    }

    /**
     *sub registrations question results
     * @param array
     */
    public function question_results($formInput)
    {
        $question = \App\Models\EventSubRegistrationQuestion::where('id', $formInput['question_id'])
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', '=', $formInput['language_id']);
            }])->first();

        $answer = \App\Models\EventSubRegistrationAnswer::where('id', $formInput['answer_id'])
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', '=', $formInput['language_id']);
            }])->first();

        $query =   \App\Models\EventSubRegistrationResult::join('conf_event_sub_registration_answers', 'conf_event_sub_registration_answers.id', '=', 'conf_event_sub_registration_results.answer_id')
            ->join('conf_event_sub_registration_answer_info', function ($join) use ($formInput) {
                $join->on('conf_event_sub_registration_answer_info.answer_id', '=', 'conf_event_sub_registration_answers.id')
                    ->where('conf_event_sub_registration_answer_info.languages_id', $formInput['language_id']);
            })
            ->join('conf_event_sub_registration_questions', 'conf_event_sub_registration_questions.id', '=', 'conf_event_sub_registration_results.question_id')
            ->join('conf_attendees', 'conf_attendees.id', '=', 'conf_event_sub_registration_results.attendee_id')
            ->join('conf_attendees_info AS a_company', function ($join) use ($formInput) {
                $join->on('conf_attendees.id', '=', 'a_company.attendee_id')
                    ->where('a_company.name', '=', 'company_name')
                    ->where('a_company.languages_id', $formInput['language_id']);
            })
            ->where('conf_event_sub_registration_results.question_id', $formInput['question_id'])
            ->where('conf_event_sub_registration_results.answer_id', $formInput['answer_id'])
            ->where('conf_event_sub_registration_results.event_id', $formInput['event_id'])
            ->groupBy('conf_event_sub_registration_results.id')
            ->select('conf_attendees.first_name', 'conf_attendees.last_name', 'conf_attendees.email', 'a_company.value as company_name', 'conf_event_sub_registration_answer_info.value as answer');

        if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && in_array($formInput['sort_by'], ['first_name', 'last_name', 'email']))) {
            $query->orderBy("conf_attendees." . $formInput['sort_by'], $formInput['order_by']);
        } else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && $formInput['sort_by'] == "company_name")) {
            $query->orderBy("a_company.value", $formInput['order_by']);
        } else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && $formInput['sort_by'] == "answer")) {
            $query->orderBy("conf_event_sub_registration_answer_info.value", $formInput['order_by']);
        }

        $results = $query->get()->toArray();

        return array(
            "results" => $results,
            "question" => (isset($question->info[0]['value']) ? $question->info[0]['value'] : ''),
            "answer" => (isset($answer->info[0]['value']) ? $answer->info[0]['value'] : ''),
            "total_results" => count($results) . '/' . $this->event_total_questions_submissions($formInput),
        );
    }

    /**
     *sub registrations event total questions submissions
     * @param array
     */
    public function event_total_questions_submissions($formInput)
    {
        return \App\Models\EventSubRegistrationResult::where('event_id', $formInput['event_id'])->count();
    }

    /**
     *sub registrations event total submissions
     * @param array
     */
    public function event_total_submissions($formInput)
    {
        $sub_registration_id = \App\Models\EventSubRegistration::where('event_id', $formInput['event_id'])->value('id');
        return \App\Models\EventSubRegistrationResult::where('event_id', $formInput['event_id'])->where('sub_registration_id', $sub_registration_id)->groupBy('attendee_id')->get();
    }

    /**
     *sub registrations event question submissions
     * @param array
     */
    public function event_question_submissions($formInput)
    {
        return \App\Models\EventSubRegistrationResult::where('event_id', $formInput['event_id'])->where('question_id', $formInput['question_id'])->count();
    }

    /**
     *sub registrations update settings
     * @param array
     */
    public function updateSettings($formInput)
    {

        $event = $formInput['event'];

        $registrationFormId = $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($formInput['event_id'], 'attendee') : 0;

        if($registrationFormId == 0) {

            $setting =  \App\Models\EventSubRegistrationSetting::where('event_id', $formInput['event_id'])->first();

            if ($setting) {

                $setting->listing = $formInput['listing'];

                $setting->save();

            }

        } else {

            $setting =  \App\Models\EventSubRegistration::where(['event_id'=> $formInput['event_id'], 'registration_form_id'=> $registrationFormId])->first();

            if ($setting) {

                $setting->status = $formInput['listing'];

                $setting->save();

            }

        }
        
        $payment_setting =  \App\Models\EventsitePaymentSetting::where('event_id', $formInput['event_id'])->where('registration_form_id', $registrationFormId)->first();

        if ($payment_setting) {

            $payment_setting->show_subregistration = $formInput['listing'];

            $payment_setting->save();

        }

    }

    /**
     *sub registrations get settings
     * @param array
     */
    public function getSettings($formInput)
    {
        $result = \App\Models\EventSubRegistrationSetting::where('event_id', $formInput['event_id'])->whereNull('deleted_at')->first();

        if(isset($formInput['registration_form_id']) && $formInput['registration_form_id'] > 0) {

            $payment_setting =  \App\Models\EventsitePaymentSetting::where('event_id', $formInput['event_id'])->where('registration_form_id', $formInput['registration_form_id'])->first();

            if($payment_setting) {
                $result->listing = $payment_setting->show_subregistration;
            }

        }

        return $result ?? [];
    }

    /**
     *sub registrations update module setting
     * @param array
     */
    public function update_module_setting($formInput)
    {
        $event_modules_order = \App\Models\EventModuleOrder::where('event_id', $formInput['event_id'])->where('alias', 'subregistration')->where('type', 'backend')->first();
        $event_modules_order->status = (isset($formInput['moduleStatus']) && $formInput['moduleStatus'] ? $formInput['moduleStatus'] : 0);
        $event_modules_order->save();
    }

    /**
     *sub registrations get module setting
     * @param array
     */
    public function get_module_setting($formInput)
    {
        return \App\Models\EventModuleOrder::where('event_id', $formInput['event_id'])->where('alias', 'subregistration')->where('type', 'backend')->first();
    }

    /**
     *sub registrations questions
     * @param array
     */
    public function registrationQuestions($formInput)
    {
        $draft_orders = $valid_order_ids = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->currentDraftOrders()->pluck('id');

        $sub_registration = \App\Models\EventSubRegistration::where('event_id', $formInput['event_id'])
        ->where('registration_form_id', (int)$formInput['registration_form_id'])
        ->where('status', '=', '1')
        ->with(['question'=> function($query) {
            return $query->where('status','=','1')->orderBy('sort_order','ASC')->orderBy('id', 'ASC');
        }, 'question.info' => function($q) use($formInput) {
            return $q->where('languages_id', $formInput['language_id']);
        }, 'question.answer' => function($r){
            return $r->orderBy('sort_order', 'ASC')->orderBy('id','ASC');
        },'question.answer.info' => function($r) use($formInput) {
            return $r->where('languages_id', $formInput['language_id']);
        }, 'question.result' => function($s) use($formInput) {
            return $s->where('attendee_id', $formInput['language_id']);
        },'question.matrix'=> function($query) {
            return $query->orderBy('sort_order');
        }])
        ->first();  

        if($sub_registration) {
            
            $sub_registration = $sub_registration->toArray();

            foreach ($sub_registration['question'] as $k => $row) {

                $programTicket = 0;

                $order_question = \App\Models\EventOrderSubRegistrationAnswer::where('order_id', $formInput['order_id'])->where('attendee_id', $formInput['attendee_id'])->where('question_id', $row['id'])->first();

                foreach ($row['answer'] as  $l => $ans) {
    
                    if ($ans['link_to'] > 0) {
                        $programTicket = ProgramRepository::getProgramTicket(["id" => $ans['link_to'], 'draft_orders' => $draft_orders]);
                        $sub_registration['question'][$k]['answer'][$l]['program_schedule'] = ProgramRepository::getProgramInfo(['agenda_id' => $ans['link_to'], 'language_id' => $formInput['language_id']], 'schedule');
                    }
    
                    $sub_registration['question'][$k]['answer'][$l]['tickets'] = $programTicket;
                    
                    if (($ans['link_to'] > 0 && ((string) $programTicket == 'unlimited' || $programTicket > 0)) || $ans['link_to'] == 0) {
                        $sub_registration['question'][$k]['answer'][$l]['ticket_left'] = 'yes';
                    } else {
                        $sub_registration['question'][$k]['answer'][$l]['ticket_left'] = 'no';
                    }

                    $info = readArrayKey($ans, array(), 'info');
   
                    $sub_registration['question'][$k]['answer'][$l]['detail'] = $info;

                    $sub_registration['question'][$k]['answer'][$l]['is_default'] = 0;

                    //Make data for dropdown
                    $sub_registration['question'][$k]['answer'][$l]['label'] = $info['answer'];

                    $sub_registration['question'][$k]['answer'][$l]['value'] = $ans['id'];

                    //Order
                    if(in_array($row['question_type'], ["multiple", "matrix", "single", "dropdown"])) {
                        $order_question_answer = \App\Models\EventOrderSubRegistrationAnswer::where('order_id', $formInput['order_id'])->where('attendee_id', $formInput['attendee_id'])->where('question_id', $row['id'])->where('answer_id', $ans['id'])->first();
                        if($order_question_answer) {
                            if(in_array($row['question_type'], ["multiple"])) {
                                $sub_registration['question'][$k]['answer'][$l]['is_default'] = 1;
                            } else if(in_array($row['question_type'], ["single", "dropdown"])) {
                                $sub_registration['question'][$k]['answer'][$l]['is_default'] = 1;
                            } else if(in_array($row['question_type'], ["matrix"])) {
                                $sub_registration['question'][$k]['answer'][$l]['answerValue'] = $order_question_answer['matrix_id'];
                            }
                        }
                    }
                    //End
                }

                $info = readArrayKey($row, array(), 'info');

                $sub_registration['question'][$k]['detail'] = $info;

                //Order
                if(in_array($row['question_type'], ["open", "date", "date_time", "number"])) {
                    $sub_registration['question'][$k]['answerValue'] = $order_question['answer'];
                } else if(in_array($row['question_type'], ["dropdown", "single"])) {
                    $sub_registration['question'][$k]['answerValue'] = $order_question['answer_id'];
                }
 
                if($order_question && $order_question['comment']) {
                    $sub_registration['question'][$k]['comment'] = $order_question['comment'];
                }
                //End

                $sub_registration['question'][$k]['show_comment'] = 1;
            }
            
            //Matrix
            if(isset($row['matrix']) && count((array)$row['matrix']) > 0) {

                foreach ($row['matrix'] as  $p => $matrix) {
                    $sub_registration['question'][$k]['matrix'][$p]['is_default'] = 0;

                    //Make data for dropdown
                    $sub_registration['question'][$k]['matrix'][$p]['label'] = $matrix['name'];

                    $sub_registration['question'][$k]['matrix'][$p]['value'] = $matrix['id'];
                }
            }
            
            return $sub_registration['question'];
        }

        return $sub_registration;
    }
    
    /**
     * saveOrderQuestionAnswers
     *
     * @param  mixed $formInput
     * @return void
     */
    public function saveOrderQuestionAnswers($formInput)
    {
        //First clean 
        \App\Models\EventOrderSubRegistrationAnswer::where('order_id', $formInput['order_id'])->where('attendee_id', $formInput['attendee_id'])->delete();

        foreach($formInput['questions'] as $question) {

            $question = json_decode($question, true);

            $modelData = array("order_id" => $formInput['order_id'], 'question_id' => $question['id'], "attendee_id" => $formInput['attendee_id']);

            //save comment
            if(isset($question['comment']) && $question['comment']) $modelData['comment'] = $question['comment'];

            if(in_array($question['question_type'], ["open", "number", "date", "date_time"])) {
                if(isset($question['answerValue']) && $question['answerValue']) {
                    $modelData['answer'] = $question['answerValue'];
                    \App\Models\EventOrderSubRegistrationAnswer::create($modelData);
                }
            } else if(in_array($question['question_type'], ["multiple"])) {
                if(count((array)$question['answer']) > 0) {
                    foreach($question['answer'] as $answer) {
                        if(isset($answer['is_default']) && $answer['is_default'] == 1) {
                            $modelData['answer_id'] = $answer['id'];
                            $modelData['agenda_id'] = $answer['link_to'];
                            \App\Models\EventOrderSubRegistrationAnswer::create($modelData);
                        }
                    }
                }
            } else if(in_array($question['question_type'], ["dropdown", "single"])) {
                if(isset($question['answerValue']) && $question['answerValue']) {
                    $answer = array_values(array_filter($question['answer'], function($val) use($question) {
                        return $val['id'] == $question['answerValue'];
                    }));
                    $modelData['answer_id'] = $question['answerValue'];
                    if(count($answer) > 0) {
                        $modelData['agenda_id'] = $answer[0]['link_to'];
                    }
                    \App\Models\EventOrderSubRegistrationAnswer::create($modelData);
                }
            } else if(in_array($question['question_type'], ["matrix"])) {
                if(count((array)$question['answer']) > 0) {
                    foreach($question['answer'] as $answer) {
                        if(isset($answer['answerValue']) && $answer['answerValue']) {
                            $modelData['answer_id'] = $answer['id'];
                            $modelData['matrix_id'] = $answer['answerValue'];
                            \App\Models\EventOrderSubRegistrationAnswer::create($modelData);
                        }
                    }
                }
            }

        }
    }

    // RegistrationSite

    /**
	 * @param mixed $attendee
	 * @param mixed $settings
	 * 
	 * @return [type]
	 */
	public function getSubregistrationSettings($event_id)
    {
        $result = \App\Models\EventSubRegistrationSetting::where('event_id', '=', $event_id)->whereNull('deleted_at')->first()->toArray();
        return $result;
    }

	public function getSubRegistrationQuestion($formInput, $attendee_id, $skip_subregistration_id, $registration_form_id){
		if (!$skip_subregistration_id) {
            $result = \App\Models\EventSubRegistration::where('event_id', $formInput['event_id'])->where('status', '=', '1')->with(['question' => function ($query) {
                return $query->where('status', '=', '1')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
            }, 'question.info' => function ($q) use($formInput) {
                return $q->where('languages_id', '=', $formInput['language_id']);
            }, 'question.answer' => function ($r) {
                return $r->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
            }, 'question.answer.info' => function ($r) use($formInput) {
                return $r->where('languages_id', '=', $formInput['language_id']);
            }, 'question.result' => function ($s) use($attendee_id) {
                return $s->where('attendee_id', '=', $attendee_id);
            },'question.matrix'=> function($query) {
            return $query->orderBy('sort_order');
        }])->where('registration_form_id', $registration_form_id)->whereNull('deleted_at')->get()->toArray();
        }else{
            $result = \App\Models\EventSubRegistration::where('event_id', '=', $formInput['event_id'])->where('status', '=', '1')->with(['question' => function ($query) {
                return $query->where('status', '=', '1')->where('required_question','=' ,1)->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
            }, 'question.info' => function ($q) use($formInput) {
                return $q->where('languages_id', '=', $formInput['language_id']);
            }, 'question.answer' => function ($r) {
                return $r->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
            }, 'question.answer.info' => function ($r) use($formInput) {
                return $r->where('languages_id', '=', $formInput['language_id']);
            }, 'question.result' => function ($s) use($attendee_id) {
                return $s->where('attendee_id', '=', $attendee_id);
            },'question.matrix'=> function($query) {
            return $query->orderBy('sort_order');
        }])->where('registration_form_id', $registration_form_id)->whereNull('deleted_at')->get()->toArray();
        }

        $temp_result = array();
        foreach ($result as $row){
           foreach ($row['question'] as $val){
               if(count($val['result']) == 0){
                   $temp_result[] = $val;
               }
            }
        }

        $result[0]['question'] = $temp_result;
        $result_data = $result;


        return $result_data[0];
	}

	public function getSubRegistrationAlreadyAnswerQuestion($formInput, $attendee_id, $registration_form_id)
    {
        $result = \App\Models\EventSubRegistration::where('event_id', '=', $formInput['event_id'])->with(['question'=> function($query) {
            return $query->orderBy('sort_order','ASC')->orderBy('id','ASC');
        }, 'question.info' => function($q) use($formInput) {
            return $q->where('languages_id','=',$formInput['language_id']);
        }, 'question.answer' => function($r){
            return $r->orderBy('sort_order')->orderBy('id','ASC');
        },'question.answer.info' => function($r) use($formInput){
            return $r->where('languages_id', '=', $formInput['language_id']);
        }, 'question.result' => function($s) use($attendee_id){
            return $s->where('attendee_id', '=', $attendee_id)->orderBy('id','DESC');
        },'question.matrix'=> function($query) {
            return $query->orderBy('sort_order');
        }])->where('registration_form_id', $registration_form_id)->whereNull('deleted_at')->get()->toArray();


        return $result[0];

    }

	public function getSubRegistrationSkipMsg($event_id, $attendee_id)
    {

        $temp = 0;
        $result = \App\Models\EventSubRegistration::where('event_id', '=', $event_id)->with(['question'=> function($query) {
            $query->where('required_question','=' ,1);
            return $query->where('status','=','1')->orderBy('sort_order','ASC');
        }])->whereNull('deleted_at')->get()->toArray();

        if (count($result) > 0) {
            if (count($result[0]['question']) > 0) {
                foreach ($result[0]['question'] as $question) {
                    $attendee_results = \App\Models\EventSubRegistrationResult::where('event_id', '=', $event_id)
                        ->where('sub_registration_id', '=', $result[0]['id'])->where('question_id', '=', $question['id'])
                        ->where('attendee_id', '=', $attendee_id)->whereNull('deleted_at')->get();

                    if (count($attendee_results) < 1) {
                        $temp = 1;
                    }
                }
            }
        }

        if ($temp == 1) {

            $show_form = 0;

        } else {

            $show_form = 0;
            $result = \App\Models\EventSubRegistration::where('event_id', '=', $event_id)->with(['question'=> function($query) {
                $query->where('required_question','=' ,0);
                return $query->where('status','=','1')->orderBy('sort_order','ASC');
            }])->whereNull('deleted_at')->get()->toArray();

            if (count($result) > 0) {
                if (count($result[0]['question']) > 0) {
                    foreach ($result[0]['question'] as $question) {
                        //echo $result[0]['id'];echo "<hr>";
                        $attendee_results = \App\Models\EventSubRegistrationResult::where('event_id', '=', $event_id)
                            ->where('sub_registration_id', '=', $result[0]['id'])->where('question_id', '=', $question['id'])
                            ->where('attendee_id', '=', $attendee_id)->whereNull('deleted_at')->get();

                        if (count($attendee_results) < 1) {
                            $show_form = 1;
                        }
                    }

                }
            }
            return $show_form;
        }

    }

	public function getSubRegistrationAfterLogin($formInput, $id)
    {
        $labels = eventsite_labels(['subregistration', 'generallabels', 'eventsite']);
        $attendeeRegFormId = $this->getAttendeeRegFormId($id, $formInput['event_id']);
        if($attendeeRegFormId === null){
            return [
                'labels' => $labels,
                'displaySubregistration' => 'no',
            ];
        }
        $settings = $this->getSubregistrationSettings($formInput['event_id']);
        $questions = $this->getSubRegistrationQuestion($formInput, $id, $formInput['skip_subregistration_id'], $attendeeRegFormId);
        $error_msg = $labels['GENERAL_ANSWER_ALL_QUESTION']; 
        $displaySubregistration = 'no';
        $first_time = 'no';

        $draft_orders = $valid_order_ids = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->currentDraftOrders()->pluck('id');


        foreach ($questions['question'] as $key => $question) {
            $displayQuestion = 'no';
            if (
                $question['question_type'] == 'open' ||
                $question['question_type'] == 'number' ||
                $question['question_type'] == 'date' ||
                $question['question_type'] == 'date_time'
            ) {
                $displaySubregistration = 'yes';
            }

            foreach ($question['answer'] as $anskey => $answer) {

                if ($answer['link_to'] > 0) {

                    $programTicket = ProgramRepository::getProgramTicket(["id" => $answer['link_to'], 'draft_orders' => $draft_orders]);

                    if ($answer['link_to'] > 0 && (string)$programTicket == 'unlimited') {
                        $displaySubregistration = 'yes';
                        $displayQuestion = 'yes';
                    } elseif ($answer['link_to'] > '0' && $programTicket > 0) {
                        $displaySubregistration = 'yes';
                        $displayQuestion = 'yes';
                    }
                    $questions['question'][$key]['answer'][$anskey]['tickets'] =  $programTicket;
                } else {
                    $displayQuestion = 'yes';
                    $displaySubregistration = 'yes';
                }
            }

            $questions['question'][$key]['display_question'] =  $displayQuestion;
            $questions['question'][$key]['show'] = count($question['result']) == 0 ? 'yes' : 'no';
            
            if(count($question['result']) == 0){
                $first_time = 'yes';
            }
        }

        
        $skip_msg = $this->getSubRegistrationSkipMsg($formInput['event_id'], $id);

        $alert_label = $labels['SUB_REGISTRATION_MAX_SELECTION_ERROR'];
        $min_alert_label = $labels['SUB_REGISTRATION_MIN_SELECTION_ERROR'];

        $getAllProgram = $this->getProgramListSubSearch($formInput);

		return [
			'labels' => $labels,
			'settings' => $settings,
			'questions' => $questions,
			'skip_msg' => $skip_msg,
			'alert_label' => $alert_label,
			'error_msg' => $error_msg,
			'first_time' => $first_time,
			'min_alert_label' => $min_alert_label,
			'displaySubregistration' => $displaySubregistration,
			'all_programs' => $getAllProgram,
		];
    }

	public function getMySubRegistration($formInput, $id)
    {

        $labels = eventsite_labels(['subregistration', 'generallabels', 'eventsite']);
        
        $attendeeRegFormId = $this->getAttendeeRegFormId($id, $formInput['event_id']);
       
        if($attendeeRegFormId === null){
            return "null";
        }

        $settings = $this->getSubregistrationSettings($formInput['event_id']);

        $error_msg = $labels['GENERAL_ANSWER_ALL_QUESTION'];

        $questions = $this->getSubRegistrationAlreadyAnswerQuestion($formInput, $id, $attendeeRegFormId);
        $first_time = 'no';

        $answered = 0;

        $draft_orders = $valid_order_ids = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->currentDraftOrders()->pluck('id');

        foreach ($questions['question'] as $key => $question) {
            foreach ($question['answer'] as $anskey => $answer) {
                if ($answer['link_to'] > 0) {
                    $programTicket = 0;
                    $programTicket = ProgramRepository::getProgramTicket(["id" => $answer['link_to'], 'draft_orders' => $draft_orders]);
                    $questions['question'][$key]['answer'][$anskey]['tickets'] =  $programTicket;
                } 
                $questions['question'][$key]['answer'][$anskey]['result'] =  \App\Models\EventSubRegistrationResult::where('question_id',$question['id'])->where('answer_id',$answer['id'])->where('attendee_id', '=', $id)->first();
            }

            $questions['question'][$key]['show'] = count($question['result']) == 0 ? 'yes' : 'no';
            if(count($question['result']) == 0){
                $first_time = 'yes';
            }
            else{
                $answered += 1;
            }
        }

        $skip_msg = $this->getSubRegistrationSkipMsg($formInput['event_id'], $id);

        // Check if SUB_REG_ATTENDEE_CAN_CHANGE_ANSWER_UNTILL date is passed then do not let attendees change there answers.
        if($settings['answer'] == 1 && isset($settings['end_date']) && $settings['end_date'] != "0000-00-00 00:00:00"){
            $current = date("Y-m-d H:i");
            $end_date = $settings['end_date'];
            if($current > $end_date){
                $settings['answer'] = 0;
            }
        }

        $alert_label = $labels['SUB_REGISTRATION_MAX_SELECTION_ERROR'];

        $getAllProgram = $this->getProgramListSubSearch($formInput);

        $show_save = $settings['answer'] == 1 ? 1 : 0;

        if($settings['answer'] == 1 && isset($settings['end_date']) && $settings['end_date'] != "0000-00-00 00:00:00"){
            $current = date("Y-m-d H:i");
            $end_date = $settings['end_date'];
            if($current > $end_date){
                $show_save=0;
            }
        }


        return [
            'labels' => $labels,
            'settings' => $settings,
            'questions' => $questions,
            'skip_msg' => $skip_msg,
            'alert_label' => $alert_label,
            'error_msg' => $error_msg,
            'first_time' => $first_time,
            'answered' => $answered,
            'all_programs' => $getAllProgram,
            'show_save' => $show_save,
        ];

    }

	/**
     * saveSubRegistration
     *
     * @param  mixed $formInput
     * @return void
     */
    public function saveSubRegistration($formInput, $id)
    {
        $event_id = $formInput['event_id'];
        $language_id = $formInput['language_id'];
        $attendee_id = $id;
        $sub_reg_id = $formInput['sub_reg_id'];
        $settings = $this->getSubregistrationSettings($formInput['event_id']);

        $attendeeRegFormId = $this->getAttendeeRegFormId($id, $formInput['event_id']);
       
        if($attendeeRegFormId === null){
            return "null";
        }

        $show_feedback = false;
        $fieldResult = \App\Models\EventSubRegistration::where('event_id', $event_id)->whereNull('deleted_at')->first();
        $send_email = 'no';

        if ($formInput['questions'] == null) {
            return [
                'status'=>false,
                'message'=> 'No questions answered',
            ];
        }

        if($formInput['first_time'] == "no" && $settings['answer'] == 1 && isset($settings['end_date']) && $settings['end_date'] != "0000-00-00 00:00:00"){
            $current = date("Y-m-d H:i");
            $end_date = $settings['end_date'];
            if($current > $end_date){
                return [
                    'status'=>false,
                    'message'=> 'Change answer date ended',
                ];
            }
        }
        $shouldSave = $this->checkShouldSave($formInput);
        if ($shouldSave) {
            if ($fieldResult) {
                if ($formInput['first_time'] == "no") {

                    \App\Models\EventSubRegistrationResult::where('event_id', $event_id)
                    ->where('attendee_id', $attendee_id)
                    ->delete();
                }
                $rs_question = \App\Models\EventSubRegistrationQuestion::where('sub_registration_id', $sub_reg_id)->whereNull('deleted_at')->get()->toArray();
                foreach ($rs_question as $row) {
                    if ($formInput['first_time'] == "no") {
                        $Ans_reslt =  \App\Models\EventSubRegistrationAnswer::where('question_id', '=', $row['id'])->whereNull('deleted_at')->get()->toArray();
                        foreach ($Ans_reslt as $ans_val) {
                            if ($ans_val['link_to'] > 0) {
                                \App\Models\EventAgendaAttendeeAttached::where("attendee_id", $attendee_id)
                                    ->where("agenda_id", $ans_val['link_to'])
                                    ->where("linked_from" , "subregistration")
                                    ->delete();
                            }
                        }
                    }
                    $checkQuery = \App\Models\EventSubRegistrationResult::where('event_id', $event_id)
                        ->where('attendee_id', $attendee_id)
                        ->where('question_id', $row['id'])
                        ->whereNull('deleted_at')
                        ->first();
                    if (!$checkQuery) {
                        $show_feedback = true;
                    }
                }
            }

            if ($show_feedback) {

                $error_array = array();
                $current_date = date('Y-m-d H:i:s');
                foreach ($formInput['questions'] as $question) {

                    $comments = '';
                    if (isset($formInput['comments' . $question])) {
                        $comments = is_array($formInput['comments' . $question]) ? $formInput['comments' . $question][0] : $formInput['comments' . $question];
                    }

                    //check for question type single
                    if ($formInput['questionsType'][$question] == 'single') {
                        if (isset($formInput['answer' . $question]) && count($formInput['answer' . $question]) > 0) {

                            foreach ($formInput['answer' . $question] as $answer) {

                                if ($answer != '') {
                                    
                                    if ($formInput['answer_agenda_' . $answer] && $formInput['answer_agenda_' . $answer] > 0) {
                                        $agenda_id = $formInput['answer_agenda_' . $answer];
                        
                                        $program = \App\Models\EventAgenda::where('id', '=', $agenda_id)->with(['info' => function ($q) use($language_id) {
                                            return $q->where('languages_id', '=', $language_id)->where('name', "topic");
                                        }, 'program_attendees_attached'])->whereNull('deleted_at')->first()->toArray();

                                        $program_name1 = $program['info'][0]['value'];

                                        $total_attach_attendee = count($program['program_attendees_attached']);
                                        
                                        $checkAgendaQuery = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', $attendee_id)
                                        ->where('agenda_id', $agenda_id)
                                        ->first();
                            
                                        if(!$checkAgendaQuery){
                                            if ($program['ticket'] == '0' || $program['ticket'] > $total_attach_attendee) {
                                                \App\Models\EventAgendaAttendeeAttached::create([
                                                    "attendee_id" => $attendee_id,
                                                    "agenda_id" => $agenda_id,
                                                    "linked_from" => "subregistration",
                                                    "link_id" => $answer
                                                ]);
                                            } else {
                                                $error_array[] =  "Sorry, Tickets are not available for " . $program_name1;
                                                continue;
                                            }
                                        }
                                    }

                                    $update_itration = 0;
                                    if ($formInput['first_time'] == 'no') {
                                        $max_update_itration = \App\Models\EventSubRegistrationResult::max('update_itration');
                                        $update_itration = $max_update_itration + 1;
                                    } else {
                                        $single_result = \App\Models\EventSubRegistrationResult::where('sub_registration_id', '=', $sub_reg_id)->where('question_id', '=', $question)->whereNull('deleted_at')->get()->toArray();
                                        if (count($single_result) == 0) {
                                            $update_itration = 1;
                                        }
                                    }

                                    $result_model = new \App\Models\EventSubRegistrationResult();
                                    $result_model->event_id = $event_id;
                                    $result_model->sub_registration_id = $sub_reg_id;
                                    $result_model->answer = "";
                                    $result_model->answer_id = addslashes($answer);
                                    $result_model->comments = addslashes($comments);
                                    $result_model->question_id = addslashes($question);
                                    $result_model->attendee_id = $attendee_id;
                                    $result_model->update_itration = $update_itration;

                                    $result_model->save();
                                    break;
                                }
                            }
                        }
                    }

                    //check for question type multiple
                    if ($formInput['questionsType'][$question] == 'multiple') {

                        if (isset($formInput['answer' . $question]) && count($formInput['answer' . $question]) > 0) {
                            
                            

                            foreach ($formInput['answer' . $question] as $answer) {
                                if ($answer != '') {
                                    
                                    if ($formInput['answer_agenda_' . $answer] && $formInput['answer_agenda_' . $answer] > 0) {

                                        $agenda_id = $formInput['answer_agenda_' . $answer];
                                        $program1 = \App\Models\EventAgenda::where('id', '=', $agenda_id)->with(['info' => function ($q) use($language_id) {
                                            return $q->where('languages_id', '=', $language_id)->where('name', "topic");
                                        }, 'program_attendees_attached'])->whereNull('deleted_at')->first()->toArray();

                                        $program_name1 = $program1['info'][0]['value'];

                                        $total_attach_attendee = count($program1['program_attendees_attached']);

                                        $checkAgendaQuery = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', $attendee_id)
                                                ->where('agenda_id', $agenda_id)
                                                ->first();
                                    
                                        if(!$checkAgendaQuery){
                                            if ($program1['ticket'] == '0' || $program1['ticket'] > $total_attach_attendee) {
                                                \App\Models\EventAgendaAttendeeAttached::create([
                                                    "attendee_id" => $attendee_id,
                                                    "agenda_id" => $agenda_id,
                                                    "linked_from" => "subregistration",
                                                    "link_id" => $answer
                                                ]);
                                            } else {
                                                $error_array[] =  "Sorry, Tickets are not available for " . $program_name1;
                                                continue;
                                            }
                                        }
                                    }

                                    $update_itration = 0;
                                    if ($formInput['first_time'] == 'no') {
                                        $max_update_itration = \App\Models\EventSubRegistrationResult::max('update_itration');
                                        $update_itration = $max_update_itration + 1;
                                    } else {
                                        $single_result = \App\Models\EventSubRegistrationResult::where('sub_registration_id', '=', $sub_reg_id)->where('question_id', '=', $question)->whereNull('deleted_at')->get()->toArray();
                                        if (count($single_result) == 0) {
                                            $update_itration = 1;
                                        }
                                    }

                                    \App\Models\EventSubRegistrationResult::create([
                                        'event_id' => $event_id,
                                        'sub_registration_id' => $sub_reg_id,
                                        'answer' => '',
                                        'comments' => $comments,
                                        'answer_id' => addslashes($answer),
                                        'question_id' => addslashes($question),
                                        'attendee_id' => $attendee_id,
                                        'update_itration' => $update_itration,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                                }
                            }
                        }
                    }

                    //check for question type matrix
                    if ($formInput['questionsType'][$question] == 'matrix') {
                        foreach ($formInput['answer' . $question] as $input_answer) {
                            foreach ($formInput['answer_matrix' . $question . '_' . $input_answer] as $answer) {
                                if ($answer != '') {

                                    $matrix_answer = explode("-", $answer);
                                    $answer_id = $matrix_answer[0];
                                    $matrix_id = $matrix_answer[1];

                                    if ($answer_id > 0) {

                                        $update_itration = 0;
                                        if ($formInput['first_time'] == 'no') {

                                            $sql = \DB::select(\DB::raw("SELECT  MAX(update_itration) as max_update_itration  FROM conf_event_sub_registration_results WHERE sub_registration_id='" . $sub_reg_id . "' AND question_id = '" . $question . "' AND deleted_at IS NOT NULL ORDER BY update_itration DESC"));
                                            $single_result = object_to_array($sql);
                                            $update_itration = $single_result[0]['max_update_itration'] + 1;
                                        } else {
                                            $single_result = \App\Models\EventSubRegistrationResult::where('sub_registration_id', '=', $sub_reg_id)->where('question_id', '=', $question)->whereNull('deleted_at')->get()->toArray();

                                            if (count($single_result) == 0) {
                                                $update_itration = 1;
                                            }
                                        }

                                        \App\Models\EventSubRegistrationResult::create([
                                            'event_id' => $event_id,
                                            'sub_registration_id' => $sub_reg_id,
                                            'answer' => $matrix_id,
                                            'comments' => $comments,
                                            'answer_id' => addslashes($answer_id),
                                            'question_id' => addslashes($question),
                                            'attendee_id' => $attendee_id,
                                            'update_itration' => $update_itration,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    //check for question type dropdown
                    if ($formInput['questionsType'][$question] == 'dropdown') {

                        if (isset($formInput['answer_dropdown' . $question]) && count($formInput['answer_dropdown' . $question]) > 0) {
                            
                            foreach ($formInput['answer_dropdown' . $question] as $answer) {
                                if ($answer != '') {

                                    $dropdown_answer = explode("-", $answer);
                                    $answer_id = $dropdown_answer[0];
                                    $agenda_id = $dropdown_answer[1];

                                    if ($agenda_id && $agenda_id > 0) {
                                        
                                        $program1 = \App\Models\EventAgenda::where('id', '=', $agenda_id)->with(['info' => function ($q) use($language_id) {
                                            return $q->where('languages_id', '=', $language_id)->where('name', "topic");
                                        }, 'program_attendees_attached'])->whereNull('deleted_at')->first()->toArray();

                                        $program_name1 = $program1['info'][0]['value'];

                                        $total_attach_attendee = count($program1['program_attendees_attached']);

                                        $checkAgendaQuery = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', $attendee_id)
                                                ->where('agenda_id', $agenda_id)
                                                ->first();
                                    
                                        if(!$checkAgendaQuery){

                                            if ($program1['ticket'] == '0' || $program1['ticket'] > $total_attach_attendee) {
                                                \App\Models\EventAgendaAttendeeAttached::create([
                                                    "attendee_id" => $attendee_id,
                                                    "agenda_id" => $agenda_id,
                                                    "linked_from" => "subregistration",
                                                    "link_id" => $answer
                                                ]);
                                            } else {
                                                $error_array[] =  "Sorry, Tickets are not available for " . $program_name1;
                                                continue;
                                            }
                                        }
                                    }

                                    if ($answer_id > 0) {

                                        $update_itration = 0;
                                        if ($formInput['first_time'] == 'no') {
                                            $max_update_itration = \App\Models\EventSubRegistrationResult::max('update_itration');
                                            $update_itration = $max_update_itration + 1;
                                        } else {
                                            $single_result = \App\Models\EventSubRegistrationResult::where('sub_registration_id', '=', $sub_reg_id)->where('question_id', '=', $question)->whereNull('deleted_at')->get()->toArray();
                                            if (count($single_result) == 0) {
                                                $update_itration = 1;
                                            }
                                        }

                                        \App\Models\EventSubRegistrationResult::create([
                                            'event_id' => $event_id,
                                            'sub_registration_id' => $sub_reg_id,
                                            'answer' => '',
                                            'comments' => $comments,
                                            'answer_id' => addslashes($answer_id),
                                            'question_id' => addslashes($question),
                                            'attendee_id' => $attendee_id,
                                            'update_itration' => $update_itration,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                    if ($formInput['questionsType'][$question] == 'open') {
						//check for question type open
						if (isset($formInput['answer_open' . $question]) && count($formInput['answer_open' . $question]) > 0) {

							foreach ($formInput['answer_open' . $question] as $answer) {
								if (trim($answer) != '') {

									$update_itration = 0;
									if ($formInput['first_time'] == 'no') {

										$sql =  \DB::select(\DB::raw("SELECT  MAX(update_itration) as max_update_itration  FROM conf_event_sub_registration_results WHERE sub_registration_id='" . $sub_reg_id . "' AND question_id = '" . $question . "' AND deleted_at IS NOT NULL ORDER BY update_itration DESC"));
										$single_result = object_to_array($sql);
										$update_itration = $single_result[0]['max_update_itration'] + 1;
									} else {
										$single_result = \App\Models\EventSubRegistrationResult::where('sub_registration_id', '=', $sub_reg_id)->where('question_id', '=', $question)->whereNull('deleted_at')->get()->toArray();

										if (count($single_result) == 0) {
											$update_itration = 1;
										}
									}

									$result_model = new \App\Models\EventSubRegistrationResult();
									$result_model->event_id = $event_id;
									$result_model->sub_registration_id = $sub_reg_id;
									$result_model->answer = addslashes($answer);
									$result_model->answer_id = "0";
									$result_model->comments = addslashes($comments);
									$result_model->question_id = addslashes($question);
									$result_model->attendee_id = $attendee_id;
									$result_model->update_itration = $update_itration;
									$result_model->save();
								}
							}
						}

					}
                    if ($formInput['questionsType'][$question] == 'number') {
						//check for question type number
						if (isset($formInput['answer_number' . $question]) && count($formInput['answer_number' . $question]) > 0) {

							foreach ($formInput['answer_number' . $question] as $answer) {
								if (trim($answer) != '') {

									$update_itration = 0;
									if ($formInput['first_time'] == 'no') {

										$sql =  \DB::select(\DB::raw("SELECT  MAX(update_itration) as max_update_itration  FROM conf_event_sub_registration_results WHERE sub_registration_id='" . $sub_reg_id . "' AND question_id = '" . $question . "' AND deleted_at IS NOT NULL ORDER BY update_itration DESC"));
										$single_result = object_to_array($sql);
										$update_itration = $single_result[0]['max_update_itration'] + 1;
									} else {
										$single_result = \App\Models\EventSubRegistrationResult::where('sub_registration_id', '=', $sub_reg_id)->where('question_id', '=', $question)->whereNull('deleted_at')->get()->toArray();

										if (count($single_result) == 0) {
											$update_itration = 1;
										}
									}

									$result_model = new \App\Models\EventSubRegistrationResult();
									$result_model->event_id = $event_id;
									$result_model->sub_registration_id = $sub_reg_id;
									$result_model->answer = addslashes($answer);
									$result_model->answer_id = "0";
									$result_model->comments = addslashes($comments);
									$result_model->question_id = addslashes($question);
									$result_model->attendee_id = $attendee_id;
									$result_model->update_itration = $update_itration;
									$result_model->save();
								}
							}
						}
					}
                    if ($formInput['questionsType'][$question] == 'date') {

						//check for question type answer date
						if (isset($formInput['answer_date' . $question]) && count($formInput['answer_date' . $question]) > 0) {

							foreach ($formInput['answer_date' . $question] as $answer) {
								if (trim($answer) != '') {

									$update_itration = 0;
									if ($formInput['first_time'] == 'no') {

										$sql =  \DB::select(\DB::raw("SELECT  MAX(update_itration) as max_update_itration  FROM conf_event_sub_registration_results WHERE sub_registration_id='" . $sub_reg_id . "' AND question_id = '" . $question . "' AND deleted_at IS NOT NULL ORDER BY update_itration DESC"));
										$single_result = object_to_array($sql);
										$update_itration = $single_result[0]['max_update_itration'] + 1;
									} else {
										$single_result = \App\Models\EventSubRegistrationResult::where('sub_registration_id', '=', $sub_reg_id)->where('question_id', '=', $question)->whereNull('deleted_at')->get()->toArray();

										if (count($single_result) == 0) {
											$update_itration = 1;
										}
									}

									$result_model = new \App\Models\EventSubRegistrationResult();
									$result_model->event_id = $event_id;
									$result_model->sub_registration_id = $sub_reg_id;
									$result_model->answer = addslashes($answer);
									$result_model->answer_id = "0";
									$result_model->comments = addslashes($comments);
									$result_model->question_id = addslashes($question);
									$result_model->attendee_id = $attendee_id;
									$result_model->update_itration = $update_itration;
									$result_model->save();
								}
							}
						}
					}
                    if ($formInput['questionsType'][$question] == 'date_time') {
						//check for question type answer date time
						if (isset($formInput['answer_date_time' . $question]) && count($formInput['answer_date_time' . $question]) > 0) {

							foreach ($formInput['answer_date_time' . $question] as $answer) {
								if (trim($answer) != '') {

									$update_itration = 0;
									if ($formInput['first_time'] == 'no') {

										$sql =  \DB::select(\DB::raw("SELECT  MAX(update_itration) as max_update_itration  FROM conf_event_sub_registration_results WHERE sub_registration_id='" . $sub_reg_id . "' AND question_id = '" . $question . "' AND deleted_at IS NOT NULL ORDER BY update_itration DESC"));
										$single_result = object_to_array($sql);
										$update_itration = $single_result[0]['max_update_itration'] + 1;
									} else {
										$single_result = \App\Models\EventSubRegistrationResult::where('sub_registration_id', '=', $sub_reg_id)->where('question_id', '=', $question)->whereNull('deleted_at')->get()->toArray();

										if (count($single_result) == 0) {
											$update_itration = 1;
										}
									}

									$result_model = new \App\Models\EventSubRegistrationResult();
									$result_model->event_id = $event_id;
									$result_model->sub_registration_id = $sub_reg_id;
									$result_model->answer = addslashes($answer);
									$result_model->answer_id = "0";
									$result_model->comments = addslashes($comments);
									$result_model->question_id = addslashes($question);
									$result_model->attendee_id = $attendee_id;
									$result_model->update_itration = $update_itration;
									$result_model->save();
								}
							}
						}
					}

                    
                }
                //exit;

                // Skip optional questions
                $q_ids = array();
                foreach ($formInput['questions'] as $row1) {
                    $attendee_results = \App\Models\EventSubRegistrationResult::where('event_id', $event_id)->where('question_id', '=', $row1)
                        ->where('attendee_id', $attendee_id)->whereNull('deleted_at')->get()->toArray();
                    if (count($attendee_results) > 0) {
                        $q_ids[] = $row1;
                    }
                }

                $un_answer_questions = array_diff($formInput['questions'], $q_ids);
                $set_session = 1;
                foreach ($un_answer_questions as $row2) {
                    $attendee_results = \App\Models\EventSubRegistrationQuestion::where('id', '=', $row2)
                        ->where('required_question', '=', '1')->whereNull('deleted_at')->get()->toArray();
                    if (count($attendee_results) > 0) {
                        $set_session = 0;
                    }
                }

                if ($set_session == 1) {
                    \Session::put('skip_subregistration_id', 'yes');
                }
                // End Skip optional questions

                \Session::put('attach_program_error', $error_array);
                
                if ($formInput['first_time'] == 'no') {
                    
                    if ($settings['update_answer_email'] == '1') {
                        $sub_registration_result_detail = $this->updateResultEmailOrganizer($sub_reg_id,$attendee_id, $event_id, $language_id);

                        $event = \App\Models\Event::where('id', '=', $event_id)->whereNull('deleted_at')->with(['info' => function ($query) {
                            return $query->where('name', '=', 'support_email');
                        }])->get()->toArray();

                        $event_name = $event[0]['name'];
                        $event_url = config('app.eventcenter_url') . '/event/' . $event[0]['url'];
                        $organizer_name = $event[0]['organizer_name'];
                        $organizer_email = $event[0]['info'][0]['value'];

                        $template = $this->getAttendeeEmailInfoFront($event_id, $language_id, 'email', 'template', 'sub_registration_update_result_email');
                        $template_value = $template->info[0]->value;

                        $findme   = '{result_detail}';
                        $pos = strpos($template_value, $findme);
                        if ($pos !== false) {

                            $attendee_detail = $this->getAttendeeDetailFront($attendee_id, $language_id);

                            $final_template = getEmailTemplate($template->info[0]->value, $event_id);

                            $template_subject = $this->getAttendeeEmailInfoFront($event_id, $language_id, 'email', 'subject', 'sub_registration_update_result_email');
                            $subject = $template_subject->info[0]->value;

                            $contents = stripslashes($final_template);


                            $event_settings = \App\Models\EventSetting::where('event_id', '=', $event_id)->where('name', '=', 'header_logo')->get()->toArray();
                            if ($event_settings[0]['value'] != '' && $event_settings[0]['value'] != 'NULL') {
                                $src = config('app.eventcenter_url') . '/assets/event/branding/' . $event_settings[0]['value'];
                            } else {
                                $src = config('app.eventcenter_url') . "/_admin_assets/images/eventbuizz_logo.png";
                            }
                            $logo = '<img src="' . $src . '" width="150" />';

                            $contents = str_replace("{event_logo}", stripslashes($logo), $contents);

                            $contents = str_replace("{first_name}", stripslashes($attendee_detail[0]['first_name']), $contents);
                            $contents = str_replace("{last_name}", stripslashes($attendee_detail[0]['last_name']), $contents);

                            $contents = str_replace("{attendee_name}", stripslashes($attendee_detail[0]['first_name'] . ' ' . $attendee_detail[0]['last_name']), $contents);
                            $contents = str_replace("{event_name}", stripslashes($event_name), $contents);
                            $contents = str_replace("{result_detail}", stripslashes($sub_registration_result_detail), $contents);

                            $contents = str_replace("{event_organizer_name}", stripslashes($organizer_name), $contents);

                            //Send Email
                            $to = $organizer_email;
                            $name = $organizer_name;

                            $body = $contents;
                            $event['event_id'] = $event_id;
                            $event['organizer_id'] = $event[0]['organizer_id'];

                            $data = array();
                            $data['template'] = 'sub_registration_update_result_email';
                            $data['event_id'] = $event_id;
                            $data['subject'] = $subject;
                            $data['content'] = $body;
                            $data['view'] = 'email.plain-text';
                            $data['from_name'] = $organizer_name;
                            $data['email'] = $to;
                            \Mail::to($to)->send(new Email($data));
                        }
                    }
                }
                if($settings['result_email'] == '1'){
                    $this->getSubregistrationEmailAttendee($attendeeRegFormId, $event_id, $attendee_id, $language_id);
                }
                
                return [
                    'status'=>true,
                    'message'=> 'Answers Successfully Updated...',
                    'errors'=>$error_array
                ];
            } else {
                return [
                    'status'=>false,
                    'message'=> 'Nothing to updated',
                ];
            }
        } else {
            return [
                'status'=>false,
                'message'=> 'Nothing to updated',
            ];
        }

    }
	
	/**
     * checkShouldSave
     *
     * @param  mixed $formInput
     * @return void
     */
    public function checkShouldSave($formInput)
    {
        $shouldSave = true;

        foreach ($formInput['questions'] as $q) {
            if (isset($formInput['answer' . $q])) {
                if (count($formInput['answer' . $q]) == 0 && $formInput['optionals'][$q] == '1') {
                    $shouldSave = false;
                }
            } elseif (isset($formInput['answer_dropdown' . $q])) {

                if ($formInput['answer_dropdown' . $q][0] == '-' && $formInput['optionals'][$q] == '1') {

                    $shouldSave = false;
                }
            } elseif (isset($formInput['answer_open' . $q])) {

                if ($formInput['answer_open' . $q][0] == '' && $formInput['optionals'][$q] == '1') {
                    $shouldSave = false;
                }
            } elseif (isset($formInput['answer_number' . $q])) {
                if ($formInput['answer_number' . $q][0] == '' && $formInput['optionals'][$q] == '1') {
                    $shouldSave = false;
                }
            } elseif (isset($formInput['answer_date' . $q])) {
                if ($formInput['answer_date' . $q][0] == '' && $formInput['optionals'][$q] == '1') {
                    $shouldSave = false;
                }
            } elseif (isset($formInput['answer_date_time' . $q])) {
                if ($formInput['answer_date_time' . $q][0] == '' && $formInput['optionals'][$q] == '1') {
                    $shouldSave = false;
                }
            } else {
                if ($formInput['optionals'][$q] == '1') {
                    $shouldSave = false;
                }
            }
            $question = \App\Models\EventSubRegistrationQuestion::find($q);
            if ($question->question_type == 'matrix') {
                foreach ($formInput['answers' . $q] as $ans) {
                    if (isset($formInput['answer_matrix' . $q . '_' . $ans]) && $formInput['optionals'][$q] == '1') {
                        $shouldSave = true;
                    }
                }
            }
        }

        return $shouldSave;
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function getOrderAttendeeQuestionAnswers($formInput)
    {
        return \App\Models\EventOrderSubRegistrationAnswer::where('order_id', $formInput['order_id'])->where('attendee_id', $formInput['attendee_id'])->get();
    }
    
    /**
     * getAttendeeRegFormId
     *
     * @param  mixed $id
     * @param  mixed $event_id
     * @return void
     */
    public function getAttendeeRegFormId($id, $event_id)
    {
        $event_attendee = \App\Models\EventAttendee::where('attendee_id', $id)->where('event_id', $event_id)->with(['regForm'])->first();
        
        if(!$event_attendee){
            return null;
        }
        
        $event_attendee = $event_attendee->toArray();
        
        if(!isset($event_attendee['reg_form']['id'])){
            return null;
        }
        
        return $event_attendee['reg_form']['id'];
    }

    function updateResultEmailOrganizer($sub_reg_id,$attendee_id,$event_id,$language_id)
    {
        $sub_registration_result_detail = '';

        $all_question = \App\Models\EventSubRegistrationQuestion::where('sub_registration_id', '=', $sub_reg_id)->with(['info' => function ($query) use($language_id) {
            return $query->where('languages_id', '=', $language_id);
        }])->whereNull('deleted_at')->get()->toArray();

        foreach ($all_question as $question){

            if($question['question_type'] == 'multiple'){

                $result = \App\Models\EventSubRegistrationResult::where('event_id', '=', $event_id)->where('attendee_id', '=', $attendee_id)->where('question_id', '=', $question['id'])->whereNull('deleted_at')->get()->toArray();

                $delete_result_one = \App\Models\EventSubRegistrationResult::where('event_id', '=', $event_id)->where('attendee_id', '=', $attendee_id)->where('question_id', '=', $question['id'])->onlyTrashed()->orderBy('id', 'DESC')->limit('1')->get()->toArray();

                $delete_result_all = \App\Models\EventSubRegistrationResult::where('event_id', '=', $event_id)->where('attendee_id', '=', $attendee_id)->where('question_id', '=', $question['id'])->where('updated_at', '=', $delete_result_one[0]['updated_at'])->onlyTrashed()->get()->toArray();


                $temp_all_result_ids = array();
                foreach ($result as $del){
                    $temp_all_result_ids[] = $del['answer_id'];
                }

                $temp_all_deletd_ids = array();
                foreach ($delete_result_all as $del){
                    $temp_all_deletd_ids[] = $del['answer_id'];
                }

                $remaing_del_ids = array();
                foreach ($temp_all_deletd_ids as $del){

                    if(!in_array($del, $temp_all_result_ids)){
                        $remaing_del_ids[] = $del;
                    }
                }

                $sub_registration_result_detail .= '<div class="question-type-open">';
                $sub_registration_result_detail .= '<b>Question:</b>';
                if ($question['required_question'] == 1) {
                    $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">*</span>';
                }
                $sub_registration_result_detail .= ' ' . $question['info'][0]['value'];


                foreach ($result as $val){

                    if(in_array($val['answer_id'], $temp_all_deletd_ids)){

                        $answers = \App\Models\EventSubRegistrationAnswer::where('id', '=', $val['answer_id'])->with(['info' => function ($query) use($language_id) {
                            return $query->where('languages_id', '=', $language_id);
                        }])->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id', 'ASC')->get()->toArray();

                        $sub_registration_result_detail .= '<br><span>';
                        $sub_registration_result_detail .= $answers[0]['info'][0]['value'];
                        $sub_registration_result_detail .= '</span>';

                    } else {

                        $answers = \App\Models\EventSubRegistrationAnswer::where('id', '=', $val['answer_id'])->with(['info' => function ($query) use($language_id) {
                            return $query->where('languages_id', '=', $language_id);
                        }])->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id', 'ASC')->get()->toArray();

                        $sub_registration_result_detail .= '<br><span style="color: #008000;">';
                        $sub_registration_result_detail .= $answers[0]['info'][0]['value'];
                        $sub_registration_result_detail .= '</span>';

                    }

                }


                foreach ($remaing_del_ids as $del_val){

                    $answers = \App\Models\EventSubRegistrationAnswer::where('id', '=', $del_val)->with(['info' => function ($query) use($language_id) {
                        return $query->where('languages_id', '=', $language_id);
                    }])->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id', 'ASC')->get()->toArray();

                    $sub_registration_result_detail .= '<br><span style="color: #ff0000;">';
                    $sub_registration_result_detail .= $answers[0]['info'][0]['value'];
                    $sub_registration_result_detail .= '</span>';

                }

                $sub_registration_result_detail .= ' </div>';

                if ($result[0]['comments'] != '') {
                    $sub_registration_result_detail .= ' <div class="question-type-open">';
                    $sub_registration_result_detail .= '<br>' . $result[0]['comments'];
                    $sub_registration_result_detail .= '</div>';
                }


            } else {

            $result = \App\Models\EventSubRegistrationResult::where('event_id', '=', $event_id)->where('attendee_id', '=', $attendee_id)->where('question_id', '=', $question['id'])->whereNull('deleted_at')->get()->toArray();

            $delete_result = \App\Models\EventSubRegistrationResult::where('event_id', '=', $event_id)->where('attendee_id', '=', $attendee_id)->where('question_id', '=', $question['id'])->onlyTrashed()->orderBy('id', 'DESC')->limit('1')->get()->toArray();

            $sub_registration_result_detail .= '<div class="question-type-open">';
            $sub_registration_result_detail .= '<b>Question:</b>';
            if ($question['required_question'] == 1) {
                $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">*</span>';
            }
            $sub_registration_result_detail .= ' ' . $question['info'][0]['value'];

            if ($result[0]['answer'] != '' && ($result[0]['answer'] == $delete_result[0]['answer'])) {

                $sub_registration_result_detail .= '<br><span>';
                $sub_registration_result_detail .= $result[0]['answer'];
                $sub_registration_result_detail .= '</span>';

            } elseif ($result[0]['answer'] != '' && ($result[0]['answer'] != $delete_result[0]['answer'])) {


                $sub_registration_result_detail .= '<br><span style="color: #008000;">';
                $sub_registration_result_detail .= $result[0]['answer'];
                $sub_registration_result_detail .= '</span>';

                $sub_registration_result_detail .= '<br><span style="color: #ff0000;">';
                $sub_registration_result_detail .= $delete_result[0]['answer'];
                $sub_registration_result_detail .= '</span>';

            } elseif ($result[0]['answer_id']!='' && ($result[0]['answer_id'] == $delete_result[0]['answer_id'])) {

                $answers = \App\Models\EventSubRegistrationAnswer::where('id', '=', $result[0]['answer_id'])->with(['info' => function ($query)use($language_id) {
                    return $query->where('languages_id', '=', $language_id);
                }])->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id', 'ASC')->get()->toArray();

                $sub_registration_result_detail .= '<br><span>';
                $sub_registration_result_detail .= $answers[0]['info'][0]['value'];
                $sub_registration_result_detail .= '</span>';

            } elseif ($result[0]['answer_id']!='' && ($result[0]['answer_id'] != $delete_result[0]['answer_id'])) {

                $answers = \App\Models\EventSubRegistrationAnswer::where('id', '=', $result[0]['answer_id'])->with(['info' => function ($query)use($language_id) {
                    return $query->where('languages_id', '=', $language_id);
                }])->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id', 'ASC')->get()->toArray();

                $sub_registration_result_detail .= '<br><span style="color: #008000;">';
                $sub_registration_result_detail .= $answers[0]['info'][0]['value'];
                $sub_registration_result_detail .= '</span>';

                $answers = \App\Models\EventSubRegistrationAnswer::where('id', '=', $delete_result[0]['answer_id'])->with(['info' => function ($query)use($language_id) {
                    return $query->where('languages_id', '=', $language_id);
                }])->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id', 'ASC')->get()->toArray();


                $sub_registration_result_detail .= '<br><span style="color: #ff0000;">';
                $sub_registration_result_detail .= $answers[0]['info'][0]['value'];
                $sub_registration_result_detail .= '</span>';

            }

            $sub_registration_result_detail .= ' </div>';


            if ($result[0]['comments'] != '') {
                $sub_registration_result_detail .= ' <div class="question-type-open">';
                $sub_registration_result_detail .= '<br>' . $result[0]['comments'];
                $sub_registration_result_detail .= '</div>';
            }

        }

        }

        return $sub_registration_result_detail;

    }

    public function getAttendeeEmailInfoFront($event_id, $language_id, $type, $name, $alias)
    {
        $information = \App\Models\EventEmailTemplate::where('event_id', '=', $event_id)
            ->where('type', '=', $type)
            ->where('alias', '=', $alias)
            ->with(['info' => function ($query) use ($name, $language_id) {
                return $query->where('languages_id', '=',$language_id)
                    ->where('name', '=', $name);
            }])->first();
        return $information;
    }

    public function getAttendeeDetailFront($id, $language_id)
    {
        $result =  \App\Models\Attendee::where('id', '=', $id)->with(['info'=> function($query) use($language_id) {
            return $query->where('languages_id','=',$language_id);
        }])->whereNull('deleted_at')->get()->toArray();


        $i=0;

        foreach ($result as $row) {
            $temp = array();

            if (count($row['info']) > 0) {
                foreach ($row['info'] as $val) {
                    $temp[$val['name']] = $val['value'];
                }
            }

            $row['attendee_detail'] = $temp;
            $result[$i] = $row;

            $i++;
        }

        return $result;

    }

    public function getSubregistrationEmailAttendee($attendeeRegFormId, $event_id, $attendee_id, $language_id)
    {

        $sub_registration_result_detail = '';

        $result = \App\Models\EventSubRegistration::where('event_id', '=', $event_id)->where('registration_form_id', $attendeeRegFormId)->with(['question' => function ($query) {
            return $query->where('status', '=', '1')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
        }, 'question.info' => function ($q) use($language_id) {
            return $q->where('languages_id', '=', $language_id);
        }, 'question.answer' => function ($r) {
            return $r->orderBy('sort_order')->orderBy('id', 'ASC');
        }, 'question.answer.info' => function ($r) use($language_id) {
            return $r->where('languages_id', '=', $language_id);
        }, 'question.result' => function ($s) use($attendee_id) {
            return $s->where('attendee_id', '=', $attendee_id)->orderBy('id', 'DESC');
        }])->whereNull('deleted_at')->get()->toArray();

        $SubRegistration_Result = $result[0];

        $n = 0;

        foreach ($SubRegistration_Result['question'] as $question) {
            $n++;

            if ($question['question_type'] == 'single') {

                $sub_registration_result_detail .= '<br><br><div class="question-type-open">';
                if ($question['required_question'] == 1) {
                    $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                }

                $sub_registration_result_detail .= $question['info'][0]['value'];

                $j = 0;

                foreach ($question['answer'] AS $answer) {

                    $sub_registration_result_detail .= '<br><label>';

                    if (isset($question['result'][0]['answer_id']) && $question['result'][0]['answer_id'] == $answer['id']) {
                        $checked_single = 'yes';
                    } else {
                        $checked_single = '';
                    }

                    if ($checked_single == 'yes') {
                        $sub_registration_result_detail .= '<b style="color: green;">';
                    }

                    $sub_registration_result_detail .= $answer['info'][0]['value'];

                    if ($checked_single == 'yes') {
                        $sub_registration_result_detail .= '</b>';
                    }

                    $sub_registration_result_detail .= '</label>';
                    $j++;
                }

                $sub_registration_result_detail .= ' </div>';

            } elseif ($question['question_type'] == 'multiple') {

                $sub_registration_result_detail .= '<br><br><div class="question-type-open">';
                if ($question['required_question'] == 1) {
                    $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                }

                $sub_registration_result_detail .= $question['info'][0]['value'];

                $i = 0;

                foreach ($question['answer'] AS $answer) {

                    $sub_registration_result_detail .= '<br><label>';
                    $checked_multiple = '';

                    
                    foreach ($question['result'] as $result) {
                        if ($result['answer_id'] == $answer['id']) {
                            $checked_multiple = "yes";
                            break;
                        }
                    }
                    

                    if ($checked_multiple == 'yes') {
                        $sub_registration_result_detail .= '<b style="color: green;">';
                    }

                    $sub_registration_result_detail .= $answer['info'][0]['value'];

                    if ($checked_multiple == 'yes') {
                        $sub_registration_result_detail .= '</b>';
                    }


                    $sub_registration_result_detail .= '</label>';
                    $i++;
                }


                $sub_registration_result_detail .= ' </div>';
            } elseif ($question['question_type'] == 'dropdown') {

                $sub_registration_result_detail .= '<br><br><div class="question-type-open">';
                if ($question['required_question'] == 1) {
                    $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                }

                $sub_registration_result_detail .= $question['info'][0]['value'];

                $drop_down = $question['answer'];

                $i = 0;

                foreach ($drop_down AS $answer) {

                    if (isset($question['result'][0]['answer_id']) && $question['result'][0]['answer_id'] == $answer['id']) {
                        $checked_drop = 'yes';
                    } else {
                        $checked_drop = '';
                    }

                    $sub_registration_result_detail .= '<br><label>';

                    if ($checked_drop == 'yes') {
                        $sub_registration_result_detail .= '<b style="color: green;">';
                    }

                    $sub_registration_result_detail .= $answer['info'][0]['value'];

                    if ($checked_drop == 'yes') {
                        $sub_registration_result_detail .= '</b>';
                    }

                    $sub_registration_result_detail .= '</label>';
                    $i++;
                }

                $sub_registration_result_detail .= '</div>';
            } elseif ($question['question_type'] == 'open') {

                $sub_registration_result_detail .= '<br><br><div class="question-type-open">';
                if ($question['required_question'] == 1) {
                    $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                }


                $sub_registration_result_detail .= $question['info'][0]['value'];

                $answer_open = '';

                if (isset($question['result'][0]['answer']) && $question['result'][0]['answer'] != '') {
                    $answer_open = $question['result'][0]['answer'];
                }

                $sub_registration_result_detail .= '<div>' . $answer_open . '</div>';


                $sub_registration_result_detail .= '</div>';

            } elseif ($question['question_type'] == 'number') {

                $sub_registration_result_detail .= '<br><br><div class="question-type-open">';
                if ($question['required_question'] == 1) {
                    $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                }

                $sub_registration_result_detail .= $question['info'][0]['value'];

                $answer_number = '';
                if (isset($question['result'][0]['answer']) && $question['result'][0]['answer'] != '') {
                    $answer_number = $question['result'][0]['answer'];
                }

                $sub_registration_result_detail .= '<div>';

                $sub_registration_result_detail .= $answer_number;

                $sub_registration_result_detail .= '</div>';

                $sub_registration_result_detail .= '</div>';

            } elseif ($question['question_type'] == 'date') {

                $sub_registration_result_detail .= '<br><br><div class="question-type-open">';
                if ($question['required_question'] == 1) {
                    $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                }

                $sub_registration_result_detail .= $question['info'][0]['value'];

                $answer_number = '';
                if (isset($question['result'][0]['answer']) && $question['result'][0]['answer'] != '') {
                    $answer_date = $question['result'][0]['answer'];
                }

                $sub_registration_result_detail .= '<div>' . $answer_date . '<div>';


                $sub_registration_result_detail .= '</div>';
            } elseif ($question['question_type'] == 'date_time') {

                $sub_registration_result_detail .= '<br><br><div class="question-type-open">';
                if ($question['required_question'] == 1) {
                    $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                }

                $sub_registration_result_detail .= $question['info'][0]['value'];

                $answer_date_time = '';
                if (isset($question['result'][0]['answer']) && $question['result'][0]['answer'] != '') {
                    $answer_date_time = $question['result'][0]['answer'];
                }
                $sub_registration_result_detail .= '<div>' . $answer_date_time . '</div>';

                $sub_registration_result_detail .= '</div>';
            }


            if ($question['enable_comments'] == 1) {
                $sub_registration_result_detail .= ' <div class="question-type-open">';

                $sub_registration_result_detail .= '<br>' . $question['result'][0]['comments'];

                $sub_registration_result_detail .= '</div>';
            }

        }

        $event = \App\Models\Event::where('id', '=', $event_id)->whereNull('deleted_at')->with('info')->get()->toArray();

        $event_name = $event[0]['name'];
        $event_url = config('app.eventcenter_url') . '/event/' . $event[0]['url'];
        $organizer_name = $event[0]['organizer_name'];

        $template = $this->getAttendeeEmailInfoFront($event_id, $language_id,'email', 'template', 'sub_registration_result_email');
        $template_value = $template->info[0]->value;
        $findme   = '{sub_registration_result_detail}';
        $pos = strpos($template_value, $findme);
        if ($pos !== false) {

        $attendee_detail = $this->getAttendeeDetailFront($attendee_id, $language_id);

        $final_template = getEmailTemplate($template->info[0]->value, $event_id);

        $template_subject = $this->getAttendeeEmailInfoFront($event_id, $language_id, 'email', 'subject', 'sub_registration_result_email');
        $subject = $template_subject->info[0]->value;


        $contents = stripslashes($final_template);



            $event_settings = \App\Models\EventSetting::where('event_id', '=', $event_id)->where('name', '=', 'header_logo')->get()->toArray();
            if ($event_settings[0]['value'] != '' && $event_settings[0]['value'] != 'NULL') {
                $src = config('app.eventcenter_url') . '/assets/event/branding/' . $event_settings[0]['value'];
            } else {
                $src = config('app.eventcenter_url') . "/_admin_assets/images/eventbuizz_logo.png";
            }
            $logo = '<img src="' . $src . '" width="150" />';

            $contents = str_replace("{event_logo}", stripslashes($logo), $contents);

            $contents = str_replace("{first_name}", stripslashes($attendee_detail[0]['first_name']), $contents);
            $contents = str_replace("{last_name}", stripslashes($attendee_detail[0]['last_name']), $contents);

            $contents = str_replace("{attendee_name}", stripslashes($attendee_detail[0]['first_name'] . ' ' . $attendee_detail[0]['last_name']), $contents);
            $contents = str_replace("{event_name}", stripslashes($event_name), $contents);
            $contents = str_replace("{sub_registration_result_detail}", stripslashes($sub_registration_result_detail), $contents);

            $contents = str_replace("{event_organizer_name}", stripslashes($organizer_name), $contents);

            //Send Email
            $to = $attendee_detail[0]['email'];
            $name = $attendee_detail[0]['first_name'] . ' ' . $attendee_detail[0]['last_name'];

            $body = $contents;
            $event['event_id'] = $event[0]['id'];
            $event['organizer_id'] = $event[0]['organizer_id'];
            $data = array();
            $data['template'] = 'sub_registration_update_result_email';
            $data['event_id'] = $event_id;
            $data['subject'] = $subject;
            $data['content'] = $body;
            $data['view'] = 'email.plain-text';
            $data['from_name'] = $organizer_name;
            $data['email'] = $to;
            \Mail::to($to)->send(new Email($data));
        }

    }

    public function getProgramListSubSearch($formInput)
    {
            
            $result = \App\Models\EventAgenda::leftJoin('conf_agenda_info AS a_end_time', function ($join) use ($formInput) {
                $join->on('conf_event_agendas.id', '=', 'a_end_time.agenda_id')
                    ->where('a_end_time.name', '=', 'end_time')
                    ->where('a_end_time.languages_id', '=', $formInput['language_id']);
            })
                ->where('conf_event_agendas.event_id', '=', $formInput['event_id'])
                ->with([
                    'info' => function ($query) use ($formInput) {
                        $query->where('languages_id', '=', $formInput['language_id']);
                    }
                ])
                ->whereNull('conf_event_agendas.deleted_at')
                ->orderBy('conf_event_agendas.start_date', 'ASC')
                ->orderBy('conf_event_agendas.start_time', 'ASC')
                ->orderBy('end_time', 'ASC')
                ->orderBy('conf_event_agendas.created_at', 'ASC')
                ->groupBy('conf_event_agendas.id')
                ->select(array('conf_event_agendas.*',  'a_end_time.value as end_time'));

                $programs = $result->get()->toArray();

                
                foreach ($programs as $key => $row) {
                    $rowData = array();
                    $infoData = readArrayKey($row, $rowData, 'info');
                    $rowData['id'] = $row['id'];
                    $rowData['topic'] = isset($infoData['topic']) ? $infoData['topic'] : '';
                    $rowData['description']['desc'] = isset($infoData['description']) ? $infoData['description'] : '';
                    $rowData['date'] = isset($infoData['date']) ? date('Y-m-d', strtotime($infoData['date'])) : '';
                    $rowData['start_time'] = isset($infoData['start_time']) ? $infoData['start_time'] : '';
                    $rowData['end_time'] = isset($infoData['end_time']) ? $infoData['end_time'] : '';
                    $rowData['location'] = isset($infoData['location']) ? $infoData['location'] : '';
        

                    $programs[$key] = $rowData;
                }
            
            $program_array = array();

            foreach( $programs as $key => $program) {
                $program_array[$key]['id'] = $program['id'];
                $program_array[$key]['name'] = $program['topic'];
                $program_array[$key]['description'][] = $program['description'];
                $program_array[$key]['workshop'] = $program['program_workshops']['name'];
                $program_array[$key]['workshop_id'] = $program['program_workshop_id'];
                $program_array[$key]['program_first_track'] = $program['program_first_track'];
                $program_array[$key]['date'] = \Carbon\Carbon::parse($program['date'])->format('d-m-Y');
                $program_array[$key]['start_time'] = \Carbon\Carbon::parse($program['start_time'])->format('H:i');
                $program_array[$key]['end_time'] = \Carbon\Carbon::parse($program['end_time'])->format('H:i');
            }


            return $program_array;
    }
}
