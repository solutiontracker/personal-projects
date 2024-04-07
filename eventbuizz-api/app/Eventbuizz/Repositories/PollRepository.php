<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class PollRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     *PollSetting clone/default
     *
     * @param array
     */
    public function install($request)
    {
        $setting = \App\Models\PollSetting::where('event_id', $request['from_event_id'])->first();

        if ($setting) {
            $duplicate = $setting->replicate();
            $duplicate->event_id = $request['to_event_id'];
            $duplicate->save();
        } else {
            $event_settings = \App\Models\EventSetting::where('event_id', '=', $request['to_event_id'])->get();
            $setting = array();
            foreach ($event_settings as $val) {
                $setting[$val['name']] = $val['value'];
            }

            $color = $setting['primary_color'] . ',' . $setting['secondary_color'] . ',,';
            \App\Models\PollSetting::create(array('event_id' => $request['to_event_id'], 'tagcloud_colors' => $color));
        }

        //content
        if ($request["content"]) {
            //Poll
            $from_polls = \App\Models\EventPoll::where("event_id", $request['from_event_id'])->get();
            if ($from_polls) {
                foreach ($from_polls as $from_poll) {
                    if (session()->has('clone.event.programs.' . $from_poll->agenda_id)) {
                        $to_poll = $from_poll->replicate();
                        $to_poll->event_id = $request['to_event_id'];
                        $to_poll->agenda_id = session()->get('clone.event.programs.' . $from_poll->agenda_id);
                        $to_poll->save();

                        //Questions
                        $from_questions = \App\Models\EventPollQuestion::where("poll_id", $from_poll->id)->get();
                        if ($from_questions) {
                            foreach ($from_questions as $from_question) {
                                $to_question = $from_question->replicate();
                                $to_question->poll_id = $to_poll->id;
                                $to_question->save();

                                //question info
                                $from_question_info = \App\Models\PollQuestionInfo::where("question_id", $from_question->id)->get();
                                if ($from_question_info) {
                                    foreach ($from_question_info as $from_info) {
                                        $to_info = $from_info->replicate();
                                        $to_info->question_id = $to_question->id;
                                        $to_info->languages_id = $request["languages"][0];
                                        $to_info->save();
                                    }
                                }

                                //question answers
                                $from_answers = \App\Models\EventPollAnswer::where("question_id", $from_question->id)->get();
                                if ($from_answers) {
                                    foreach ($from_answers as $from_answer) {
                                        $to_answer = $from_answer->replicate();
                                        $to_answer->question_id = $to_question->id;
                                        $to_answer->save();

                                        //answer info
                                        $from_answer_info = \App\Models\EventPollAnswerInfo::where("answer_id", $from_answer->id)->get();
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
                                $from_matrix_columns = \App\Models\EventPollMatrix::where("question_id", $from_question->id)->get();
                                if ($from_matrix_columns) {
                                    foreach ($from_matrix_columns as $from_matrix_column) {
                                        $to_matrix_column = $from_matrix_column->replicate();
                                        $to_matrix_column->question_id = $to_question->id;
                                        $to_matrix_column->save();
                                    }
                                }
                            }
                        }

                        //poll groups
                        $from_poll_groups = \App\Models\EventPollGroup::where("poll_id", $from_poll->id)->get();
                        if ($from_poll_groups) {
                            foreach ($from_poll_groups as $from_poll_group) {
                                if (session()->has('clone.event.event_groups.' . $from_poll_group->group_id)) {
                                    $to_poll_group = $from_poll_group->replicate();
                                    $to_poll_group->poll_id = $to_poll->id;
                                    $to_poll_group->group_id = session()->get('clone.event.event_groups.' . $from_poll_group->group_id);
                                    $to_poll_group->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
