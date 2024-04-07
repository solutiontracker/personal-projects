<?php

namespace App\Http\Controllers\Wizard\Requests\Survey;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{

    public $answer_count = 0;
    public $column_count = 0;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'question' => 'required',
            'question_type' => 'required',
            'column' => 'bail|required_if:question_type,==,matrix',
            'min_options' => (in_array($this->request->get('question_type'), ['multiple']) ? 'bail|required_if:question_type,==,multiple|integer' : ''),
            'max_options' => (in_array($this->request->get('question_type'), ['multiple']) ? 'bail|required_if:question_type,==,multiple|integer' : '')
        ];

        return $rules;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $answer = 0;
        $column =  1;
        if (in_array($this->request->get('question_type'), ['single', 'multiple', 'dropdown', 'matrix'])) {
            if ($this->request->has('answer')) {
                $answers = (\Route::is('wizard-survey-question-update') ? $this->request->get('answer') : json_decode($this->request->get('answer'), true));
                foreach ($answers as $key => $val) {
                    if ($val && trim($val['value'])) {
                        $answer += 1;
                    }
                }
            }
            if($this->request->get('question_type') == 'matrix'){
                $column = 0;
                $answers = (\Route::is('wizard-survey-question-update') ? $this->request->get('column') : json_decode($this->request->get('column'), true));
                foreach ($answers as $key => $val) {
                    if ($val && trim($val['value'])) {
                        $column += 1;
                    }
                }
            }
        }
        else {
            $answer = 1;
            $column = 1;
        }

        $this->answer_count = $answer;
        $this->column_count = $column;

        if (in_array($this->request->get('question_type'), ['multiple'])) {
            $this->merge([
                'min_options' => ($this->request->get('min_options') == '' ? 0 : $this->request->get('min_options')),
                'max_options' => ($this->request->get('max_options') == '' ? 0 : $this->request->get('max_options'))
            ]);
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        //if no answer in 'single', 'multiple', 'dropdown' then throw error.
        $validator->after(function ($validator) {
            if (!$this->answer_count) $validator->errors()->add('answer', __('messages.answer'));
        });

        //if no column in 'matrix' then throw error.
        $validator->after(function ($validator) {
            if (!$this->column_count) $validator->errors()->add('column', __('messages.answer'));
        });

        // Min and Max Options validation
        if (in_array($this->request->get('question_type'), ['multiple'])) {

            if($this->request->get('max_options') > $this->answer_count) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('max_options', __('messages.survey.max_option_less_than_answer'));
                });
            } else if($this->request->get('min_options') > $this->answer_count) {
                $validator->after(function ($validator){
                    $validator->errors()->add('min_options', __('messages.survey.min_option_less_than_answer'));
                });
            } else if($this->request->get('max_options') < $this->request->get('min_options')){
                $validator->after(function ($validator) {
                    $validator->errors()->add('min_options', __('messages.survey.min_option_less_than_max_option'));
                });
            }
        }
    }
}