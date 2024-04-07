<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FormBuilderRepository extends AbstractRepository
{
    const REGULAR = 'regular';
    const OTHER = 'other';

    const ANSWER_TYPES = [
        "regular" => self::REGULAR,
        "other" => self::OTHER,
    ];

    /**
     * createForm
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function createForm($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }
        if((!isset($formInput['title']) || $formInput['title'] == '')){
            return [
                "status" => 0,
                "message" => "Title is required"
            ];
        }
        $create = \App\Models\FormBuilderForm::create([
            "event_id" => $event_id,
            'registration_form_id' => $registration_form_id, 
            'screenshot' => "", 
            'status' => "draft"
        ]);

        if($create){
            foreach ($formInput as $key => $value) {
                \App\Models\FormBuilderFormInfo::create([
                    "name" => $key,
                    "value" => $value ?? "",
                    "form_id" => $create->id,
                    "language_id" => $event['language_id']
                ]);
            }
         $section =   \App\Models\FormBuilderSection::create([
                'form_builder_form_id' => $create->id, 
                'next_section' => "CONTINUE", 
                'sort_order' => 0
            ]);
        
         if($section){
            foreach ($formInput as $key => $value) {
                \App\Models\FormBuilderSectionInfo::create([
                    "name" => $key,
                    "value" => $value ?? "",
                    "section_id" => $section->id,
                    "language_id" => $event['language_id']
                ]);
            }
         }
        }

        $form = \App\Models\FormBuilderForm::where("id", $create->id)->with(['info' => function($q) use($event) { $q->where('language_id', $event['language_id']);}, 'sections'])->first()->toArray();

        foreach ($form['info'] as $key => $value) {
            $form[$value['name']] = $value['value'];
        }

        unset($form['info']);

        return [
            "status" => 1,
            "message" => "Form Created Successfully",
            "data" => $form
        ];

    }
    
    /**
     * getForms
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function getForms($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }
        $forms = \App\Models\FormBuilderForm::where("event_id", $event_id)->where("registration_form_id", $registration_form_id)->with(['info' => function($q) use($event) { $q->where('language_id', $event['language_id']);}])->orderBy('sort_order')->get()->toArray();

        foreach ($forms as $key => $form) {

            foreach ($form['info'] as $value) {
                $form[$value['name']] = $value['value'];
            }

            unset($form['info']);

            $forms[$key] = $form;
        }

        return [
            "status" => 1,
            "message" => "Form Created Successfully",
            "data" => $forms
        ];
    }
    
    /**
     * getForm
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function getForm($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }
        if((!isset($formInput['form_id']) || $formInput['form_id'] == '')){
            return [
                "status" => 0,
                "message" => "Form id is required"
            ];
        }

        
        $form = \App\Models\FormBuilderForm::where("id", (int) $formInput['form_id'])
        ->where("registration_form_id", $registration_form_id)
        ->with([
            'info' => function($q) use($event) { $q->where('language_id', $event['language_id']);},
            'sections', 
            'sections.info',
            'sections.questions', 
            'sections.questions.info' => function($q) use($event) { $q->where('language_id', $event['language_id']);},
            'sections.questions.answers',
            'sections.questions.answers.info' => function($q) use($event) { $q->where('language_id', $event['language_id']);},
            'sections.questions.options',
            'sections.questions.validation',
            'sections.questions.gridQuestions',
            'sections.questions.gridQuestions.info' => function($q) use($event) { $q->where('language_id', $event['language_id']);},
            ])
        ->first();


        if(!$form){
            return [
                "status" => 0,
                "message" => "Form not Found"
            ];
        }
        
        $form = $form->toArray();
        $form = $this->mapItemsInfoData($form);

        $form["sections"] = collect($form["sections"])->map(function ($section) {
            $section = $this->mapItemsInfoData($section);
            $section['questions'] = collect($section["questions"])->map(function ($question) {
                $question = $this->mapItemsInfoData($question);
                $question['answers'] = collect($question["answers"])->map(function ($answers) {
                    $answers = $this->mapItemsInfoData($answers);
                    return $answers;
                })->toArray();
                $question['grid_questions'] = collect($question["grid_questions"])->map(function ($grid_questions) {
                    $grid_questions = $this->mapItemsInfoData($grid_questions);
                    return $grid_questions;
                })->toArray();
                if(is_array($question['options'])){
                    $question['options'] = array_filter($question['options'], "strlen");
                }
                return $question;
            })->toArray();
            return $section;
        })->toArray();




        return [
            "status" => 1,
            "message" => "Form retrieved successfully",
            "data" => $form,
        ];
        

    }
    
    /**
     * saveSection
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function saveSection($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }
        if((!isset($formInput['title']) || !isset($formInput['sort_order']) )){
            return [
                "status" => 0,
                "message" => "Invalid data"
            ];
        }
        
        $infoKeys = ['title', 'description'];

        if(isset($formInput['id'])){
            \App\Models\FormBuilderSection::where('id', $formInput['id'])->update([
                'sort_order' => $formInput['sort_order'],
                'next_section' => $formInput['next_section'], 
            ]);

            foreach ($formInput as $key => $value) {
                if(in_array($key, $infoKeys)){
                    $infoItem = \App\Models\FormBuilderSectionInfo::where('section_id', $formInput['id'])->where('name', $key)->where('language_id', $event['language_id'])->first();
                    if($infoItem){
                        $infoItem->update([
                            "value" => $formInput[$key],
                        ]);
                    }
                    else{
                        \App\Models\FormBuilderSectionInfo::create([
                            "name" => $key,
                            "value" => $value ?? "",
                            "section_id" => $formInput['id'],
                            "language_id" => $event['language_id']
                        ]);
                    }
                }
            }
            return [
                "status" => 1,
                "message" => "Data saved successfully",
                "data" =>  ["section_id" => $formInput['id']]
            ];
        }
        else{
            $section = \App\Models\FormBuilderSection::create([
                'form_builder_form_id' => $formInput['form_builder_form_id'], 
                'next_section' => $formInput['next_section'], 
                'sort_order' => $formInput['sort_order']
            ]);

            foreach ($formInput as $key => $value) {
                if(in_array($key, $infoKeys)){
                    \App\Models\FormBuilderSectionInfo::create([
                        "name" => $key,
                        "value" => $value ?? "",
                        "section_id" => $section->id,
                        "language_id" => $event['language_id']
                    ]);
                }
            }

            return [
                "status" => 1,
                "message" => "Data saved successfully",
                "data" =>  ["section_id" => $section->id ]
            ];
    
        }

        
    }

    
    /**
     * saveSectionSort
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function saveSectionSort($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }

        foreach ($formInput as $section_id => $section_sort) {
            \App\Models\FormBuilderSection::where('id', $section_id)->update([
                'sort_order' => $section_sort
            ]);
        }

        return [
            "status" => 1,
            "message" => "Data saved successfully",
        ];
    }
    
    /**
     * saveFormSort
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function saveFormSort($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }

        foreach ($formInput as $form_sort => $form) {
            \App\Models\FormBuilderForm::where('id', $form['id'])->update([
                'sort_order' => $form_sort
            ]);
        }

        return [
            "status" => 1,
            "message" => "order updated successfully",
        ];
    }
    
    /**
     * saveFormStatus
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function saveFormStatus($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }

        
            \App\Models\FormBuilderForm::where('id', $formInput['form_id'])->update([
                'active' => $formInput['status']
            ]);
        

        return [
            "status" => 1,
            "message" => "status saved successfully",
        ];
    }
    
    /**
     * saveQuestions
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function addQuestion($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }
        // ADD QUESTION
        $createQuestion = \App\Models\FormBuilderQuestion::create([
            'form_builder_form_id' => $formInput['form_builder_form_id'],
            'form_builder_section_id' => $formInput['form_builder_section_id'],
            'type' => $formInput['type'],
            'required' => $formInput['required'],
            'sort_order' => $formInput['sort_order']
        ]);
        
        $infoKeys = ['title', 'description'];
        
        // ADD QUESTION INFO
        if ($createQuestion) {
            
            foreach ($formInput as $key => $value) {
                if (in_array($key, $infoKeys)) {
                    \App\Models\FormBuilderQuestionInfo::create([
                        "name" => $key,
                        "value" => $value ?? "",
                        "question_id" => $createQuestion->id,
                        "language_id" => $event['language_id']
                    ]);
                }
            }
            
            
            
            // ADD QUESTION OPTIONS
            if(isset($formInput['options'])){
                \App\Models\FormBuilderQuestionOption::create(array_merge($formInput['options'], ["question_id" => $createQuestion->id]));
            }
            
            // ADD QUESTION VALIDATION
            if($formInput['options']['response_validation'] == 1){
                \App\Models\FormBuilderQuestionValidation::create([
                    "question_id" => $createQuestion->id,
                    'type' =>$formInput['validation']['type'], 
                    'rule' =>$formInput['validation']['rule'], 
                    'value' =>$formInput['validation']['value'] ?? '', 
                    'value_2' =>$formInput['validation']['value_2'] ?? '', 
                    'custom_error' =>$formInput['validation']['custom_error'] ?? ''
                ]);
            }

            // ADD QUESTION ANSWERS 
            if (
                $formInput['type'] === "multiple_choice" ||
                $formInput['type'] === "checkboxes" ||
                $formInput['type'] === "drop_down"
                ) {
                    
                    foreach ($formInput['answers'] as $key => $value) {
                        $createAnswers =  \App\Models\FormBuilderAnswer::create([
                            'question_id' => $createQuestion->id,
                            'next_section' => isset($value['next_section']) ? $value['next_section'] : 'CONTINUE',
                            'sort_order' => $key,
                            'type' =>  in_array($value['type'], self::ANSWER_TYPES) ?  self::ANSWER_TYPES[$value['type']] : self::ANSWER_TYPES['regular']    
                        ]);
                        
                        \App\Models\FormBuilderAnswerInfo::create([
                            "name" => "label",
                            "value" => $value['label'],
                            "answer_id" => $createAnswers->id,
                            "language_id" => $event['language_id']
                        ]);
                    }
                }
                // ADD GRID QUESTIONS

                if (
                    $formInput['type'] === "multiple_choice_grid" ||
                    $formInput['type'] === "tick_box_grid" 
                ) {
                        foreach ($formInput['grid_questions'] as $key => $value) {
                            $createGridQuestion =  \App\Models\FormBuilderGridQuestion::create([
                                'question_id' => $createQuestion->id,
                                'sort_order' => $key
                            ]);
                            
                            \App\Models\FormBuilderGridQuestionInfo::create([
                                "name" => "label",
                                "value" => $value['label'],
                                "question_id" => $createGridQuestion->id,
                                "language_id" => $event['language_id']
                            ]);
                        }

                    }
                    
                    // ADD GRID ANSWER 
               
                    if (
                    $formInput['type'] === "multiple_choice_grid" ||
                    $formInput['type'] === "tick_box_grid" 
                    ) {
                            foreach ($formInput['answers'] as $key => $value) {
                                $createAnswers =  \App\Models\FormBuilderAnswer::create([
                                    'question_id' => $createQuestion->id,
                                    'sort_order' => $key,
                                    'type' => in_array($value['type'], self::ANSWER_TYPES) ?  self::ANSWER_TYPES[$value['type']] : self::ANSWER_TYPES['regular']
                                ]);
                                
                                \App\Models\FormBuilderAnswerInfo::create([
                                    "name" => "label",
                                    "value" => $value['label'],
                                    "answer_id" => $createAnswers->id,
                                    "language_id" => $event['language_id']
                                ]);
                            }
    
                    }
                    
        }

        $question = $this->getQuestion($createQuestion->id, $event);

        return [
            "status" => 1,
            "data" => $question,
            "message" => "Data saved successfully",
        ];

    }
    
    /**
     * updateQuestions
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function updateQuestion($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }

        $originalQuestion = $this->getOrignalQuestion($formInput['id']);

        // ADD QUESTION
        $updateQuestion = \App\Models\FormBuilderQuestion::where('id', $formInput['id'])->update([
            'type' => $formInput['type'],
            'required' => $formInput['required'],
            'sort_order' => $formInput['sort_order']
        ]);
        
        $infoKeys = ['title', 'description'];
        
        // ADD QUESTION INFO
        if ($updateQuestion) {
            
            foreach ($formInput as $key => $value) {
                if (in_array($key, $infoKeys)) {
                    \App\Models\FormBuilderQuestionInfo::where('question_id', $formInput['id'])->where('name', $key)->where("language_id", $event['language_id'])->update([
                        "value" => $value ?? "",
                    ]);
                }
            }
            
            
            
            // ADD QUESTION OPTIONS
            
            if(isset($formInput['options'])){
                $optionDefault = [
                    "add_other" => null,
                    "response_validation" => null,
                    "section_based" => null,
                    "limit" => null,
                    "shuffle" => null,
                    "date" => null,
                    "time" => null,
                    "year" => null,
                    "min" => null,
                    "max" => null,
                    "min_label" => null,
                    "max_label" => null,
                    "time_type" => null,
                    'description_visible' => null,
                ];
                
                if(isset($formInput['options']) && isset($formInput['options']['id'])){
                    unset($formInput['options']['id']);
                    $options = array_merge($optionDefault, $formInput['options']);
                    \App\Models\FormBuilderQuestionOption::where("question_id", $formInput['id'])->update($options);
                }else{
                    $options = array_merge($optionDefault, $formInput['options']);
                    \App\Models\FormBuilderQuestionOption::create(array_merge($options, ['question_id' => $formInput['id']]));
                }
                    
            }


            
            // ADD QUESTION VALIDATION
            if(isset($formInput['options']['response_validation']) && $formInput['options']['response_validation'] == 1){
                if(isset($formInput['validation']) && isset($formInput['validation']['id'])){
                    \App\Models\FormBuilderQuestionValidation::where("id", $formInput['validation']['id'])->update([
                        "question_id" =>  $formInput['id'],
                        'type' =>$formInput['validation']['type'], 
                        'rule' =>$formInput['validation']['rule'], 
                        'value' =>$formInput['validation']['value'] ?? '', 
                        'value_2' =>$formInput['validation']['value_2'] ?? '', 
                        'custom_error' =>$formInput['validation']['custom_error'] ?? ''
                    ]);
                } else {
                    \App\Models\FormBuilderQuestionValidation::where("question_id", $formInput['id'])->delete();
                    \App\Models\FormBuilderQuestionValidation::create([
                        "question_id" =>  $formInput['id'],
                        'type' =>$formInput['validation']['type'], 
                        'rule' =>$formInput['validation']['rule'], 
                        'value' =>$formInput['validation']['value'] ?? '', 
                        'value_2' =>$formInput['validation']['value_2'] ?? '', 
                        'custom_error' =>$formInput['validation']['custom_error'] ?? ''
                    ]);
                }
            } else {
                \App\Models\FormBuilderQuestionValidation::where("question_id", $formInput['id'])->delete();
            }

            // ADD QUESTION ANSWERS 
            if (
                $formInput['type'] === "multiple_choice" ||
                $formInput['type'] === "checkboxes" ||
                $formInput['type'] === "drop_down" ||
                $formInput['type'] === "multiple_choice_grid" ||
                $formInput['type'] === "tick_box_grid" 
                ) {
                    $answerIds = [];
                    foreach ($formInput['answers'] as $key => $value) {
                        if($value['id']){
                            \App\Models\FormBuilderAnswer::where('id', $value['id'])->update([
                                'sort_order' => $key,
                                'next_section' => $value['next_section'],
                                'type'=> in_array($value['type'], self::ANSWER_TYPES) ?  self::ANSWER_TYPES[$value['type']] : self::ANSWER_TYPES['regular']
                            ]);
                            
                            \App\Models\FormBuilderAnswerInfo::where('answer_id', $value['id'])->where('name', 'label')->where("language_id", $event['language_id'])->update([
                                "value" => $value['label'] ?? "",
                            ]);
                            $answerIds[] = $value['id'];
                        } else {
                            $createAnswers =  \App\Models\FormBuilderAnswer::create([
                                'question_id' => $formInput['id'],
                                'next_section' => isset($value['next_section']) ? $value['next_section'] : 'CONTINUE',
                                'sort_order' => $key,
                                'type' => in_array($value['type'], self::ANSWER_TYPES) ?  self::ANSWER_TYPES[$value['type']] : self::ANSWER_TYPES['regular']
                            ]);
                            
                            \App\Models\FormBuilderAnswerInfo::create([
                                "name" => "label",
                                "value" => $value['label'],
                                "answer_id" => $createAnswers->id,
                                "language_id" => $event['language_id']
                            ]);

                            $answerIds[] = $createAnswers->id;
                        }
                    }
                    // delete answers if any
                    $ans_ids = \App\Models\FormBuilderAnswer::where('question_id', $formInput['id'])->whereNotIn('id', $answerIds)->get()->map(function($item){return $item->id;})->toArray();
                    \App\Models\FormBuilderAnswer::where('question_id', $formInput['id'])->whereNotIn('id', $answerIds)->delete();
                    \App\Models\FormBuilderAnswerInfo::whereIn('answer_id', $ans_ids)->delete();
                }


                // ADD GRID QUESTIONS

                if (
                    $formInput['type'] === "multiple_choice_grid" ||
                    $formInput['type'] === "tick_box_grid" 
                    ) {
                        $gridQuestionIds = [];
                        foreach ($formInput['grid_questions'] as $key => $value) {
                            if($value['id']){
                                $createGridQuestion =  \App\Models\FormBuilderGridQuestion::where('id', $value['id'])->update([
                                    'sort_order' => $key
                                ]);
                                
                                \App\Models\FormBuilderGridQuestionInfo::where('question_id', $value['id'])->where('name', 'label')->where("language_id", $event['language_id'])->update([
                                    "value" => $value['label'] ?? "",
                                ]);
                                $gridQuestionIds[] = $value['id'];
                            
                            } else{

                                $createGridQuestion =  \App\Models\FormBuilderGridQuestion::create([
                                    'question_id' => $formInput['id'],
                                    'sort_order' => $key
                                ]);
                                
                                \App\Models\FormBuilderGridQuestionInfo::create([
                                    "name" => "label",
                                    "value" => $value['label'],
                                    "question_id" => $createGridQuestion->id,
                                    "language_id" => $event['language_id']
                                ]);

                                $gridQuestionIds[] = $value['id'];

                            }
                        }
                        // delete answers if any

                        $gQIds = \App\Models\FormBuilderGridQuestion::where('question_id', $formInput['id'])->whereNotIn('id', $gridQuestionIds)->get()->map(function($item){return $item->id;})->toArray();
                        \App\Models\FormBuilderGridQuestion::where('question_id', $formInput['id'])->whereNotIn('id', $gridQuestionIds)->delete();
                        \App\Models\FormBuilderGridQuestionInfo::whereIn('question_id', $gQIds)->delete();
                    }

                    //Type Change
                    $type = [
                        "multiple_choice",
                        "checkboxes",
                        "drop_down",
                        "multiple_choice_grid",
                        "tick_box_grid",
                    ];

                    if(!in_array($formInput['type'], $type)){
                        $ans_ids =\App\Models\FormBuilderAnswer::where('question_id', $formInput['id'])->get()->map(function($item){return $item->id;})->toArray();
                        \App\Models\FormBuilderAnswer::where('question_id', $formInput['id'])->delete();
                        \App\Models\FormBuilderAnswerInfo::whereIn('answer_id', $ans_ids)->delete();
                    }
                    
                    if(($formInput['type'] !== 'multiple_choice_grid' && $formInput['type'] !== 'tick_box_grid')){
                        $qustion_ids =\App\Models\FormBuilderGridQuestion::where('question_id', $formInput['id'])->get()->map(function($item){return $item->id;})->toArray();
                        \App\Models\FormBuilderGridQuestion::where('question_id', $formInput['id'])->delete();
                        \App\Models\FormBuilderGridQuestionInfo::whereIn('question_id', $qustion_ids)->delete();
                    }
                    
                    
        }

        $question = $this->getQuestion($formInput['id'], $event);

        return [
            "status" => 1,
            "data" => $question,
            "message" => "Data saved successfully",
        ];

    }

    
    /**
     * getEvent
     *
     * @param  mixed $event_id
     * @return void
     */
    public function getEvent($event_id)
    {
        $event = \App\Models\Event::where('id', $event_id)->first();
        return $event ? $event->toArray() : [];
    }
    
    /**
     * mapItemsInfoData
     *
     * @param  mixed $item
     * @return void
     */
    public function mapItemsInfoData($item){
            foreach ($item['info'] as $value) {
                $item[$value['name']] = $value['value'];
            }
            unset($item['info']);
        
        return $item;
    }
        
    /**
     * mapSubitemsInfoData
     *
     * @param  mixed $item
     * @param  mixed $subItems
     * @return void
     */
    public function mapSubitemsInfoData($item, $subItems){
        foreach ($item[$subItems] as $key => $subItem) {
            foreach ($subItem['info'] as $value) {
                $subItem[$value['name']] = $value['value'];
            }
            unset($subItem['info']);
            $item[$subItems][$key] = $subItem;
        }
        return $item;
    }

    
    
       
    /**
     * getQuestion
     *
     * @param  mixed $question_id
     * @param  mixed $event
     * @return void
     */
    public function getQuestion($question_id, $event)
    {
      $question = \App\Models\FormBuilderQuestion::where('id', $question_id)->with([
        'info' => function($q) use($event) { $q->where('language_id', $event['language_id']);},
        'answers',
        'answers.info' => function($q) use($event) { $q->where('language_id', $event['language_id']);},
        'options',
        'validation',
        'gridQuestions',
        'gridQuestions.info' => function($q) use($event) { $q->where('language_id', $event['language_id']);},
      ])->first()->toArray();

      $question = $this->mapItemsInfoData($question);
      $answers = [];
      foreach ($question['answers'] as $key => $value) {
        $answers[] = $this->mapItemsInfoData($value);
      }

      $grid_questions = [];
      foreach ($question['grid_questions'] as $key => $value) {
        $grid_questions[] = $this->mapItemsInfoData($value);
      }

      $question['grid_questions'] =$grid_questions;
      $question['answers'] = $answers;

      if(is_array($question['options'])){
          $question['options'] = array_filter($question['options'],'strlen');
      }

      return $question;
        
    }
    
    /**
     * getOrignalQuestion
     *
     * @param  mixed $question_id
     * @return void
     */
    public function getOrignalQuestion($question_id)
    {
       $question = \App\Models\FormBuilderQuestion::where('id', $question_id)->first()->toArray();
       return $question;
    }
    
        
    /**
     * updateQuestionSection
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function updateQuestionSection($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }
        if(!isset($formInput['question_id']) || !isset($formInput['section_id'])){
            return [
                "status" => 0,
                "message" => "Invalid data"
            ];
        }


        \App\Models\FormBuilderQuestion::where('id', $formInput['question_id'])->update(['form_builder_section_id' => $formInput['section_id']]);
       
        $question = $this->getQuestion($formInput['question_id'], $event);

        return [
            "status" => 1,
            "data" => $question,
            "message" => "Data saved successfully",
        ];
    }
    
    /**
     * updateQuestionSort
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function updateQuestionSort($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }
        if(!isset($formInput['section_one']) ){
            return [
                "status" => 0,
                "message" => "Invalid data"
            ];
        }

        foreach ($formInput['section_one'] as $question_id => $question_sort) {
            \App\Models\FormBuilderQuestion::where('id', $question_id)->update([
                'sort_order' => $question_sort
            ]);
        }
        if(isset($formInput['section_two'])){
            foreach ($formInput['section_one'] as $question_id => $question_sort) {
                \App\Models\FormBuilderQuestion::where('id', $question_id)->update([
                    'sort_order' => $question_sort
                ]);
            }
        }

        return [
            "status" => 1,
            "message" => "Data saved successfully",
        ];
    }

    
    /**
     * deleteSection
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function deleteSection($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }
        if(!isset($formInput['section_id'])){
            return [
                "status" => 0,
                "message" => "Invalid data"
            ];
        }

        \App\Models\FormBuilderSection::where('id', $formInput['section_id'])->first()->delete();
        
        return [
                "status" => 1,
                "message" => "section Deleted successfully"
        ];
    }
    
    /**
     * deleteQuestion
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function deleteQuestion($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }

        if(!isset($formInput['question_id'])){
            return [
                "status" => 0,
                "message" => "Invalid data"
            ];
        }

        \App\Models\FormBuilderQuestion::where('id', $formInput['question_id'])->first()->delete();
        
            return [
                "status" => 1,
                "message" => "Question Deleted successfully"
            ];
        
    }
    
    /**
     * cloneQuestion
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function cloneQuestion($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }

        if(!isset($formInput['question_id'])){
            return [
                "status" => 0,
                "message" => "Invalid data"
            ];
        }

       $clone_id = $this->duplicateQuestion($formInput['question_id'],  $event_id, $event['language_id']);

        $question = $this->getQuestion($clone_id, $event);

        return [
            "status" => 1,
            "data" => $question,
            "message" => "Question Deleted successfully"
        ];

    }
    
    /**
     * cloneSection
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function cloneSection($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }

        if(!isset($formInput['section_id'])){
            return [
                "status" => 0,
                "message" => "Invalid data"
            ];
        }
        
        $item = \App\Models\FormBuilderSection::where('id', $formInput['section_id'])->with([ 'info'  => function($q) use($event) { $q->where('language_id', $event['language_id']);},  'questions' ])->first();

        $clone = $item->replicate();

        $clone->save();
        
        $item = $item->toArray();

        foreach ($item['info'] as $key => $value) {
            \App\Models\FormBuilderSectionInfo::create([
                "name" => $value['name'],
                "value" => $value['value'] ?? "",
                "section_id" => $clone->id,
                "language_id" => $event['language_id']
            ]);
        }

        foreach ($item['questions'] as $key => $question) {
            $this->duplicateQuestion($question['id'],  $event_id, $event['language_id'], $clone->id);
        }


        $section = $this->getSection($clone->id, $event_id, $event['language_id']);


        return [
            "status" => 1,
            "data" => $section,
            "message" => "Question Deleted successfully"
        ];

    }

    
    /**
     * duplicateQuestion
     *
     * @param  mixed $question_id
     * @param  mixed $event_id
     * @param  mixed $language_id
     * @param  mixed $section_id
     * @return void
     */
    public function duplicateQuestion($question_id, $event_id, $language_id, $section_id = null)
    {
        $item = \App\Models\FormBuilderQuestion::where('id', $question_id)->with([ 
            'info' => function($q) use($language_id) { $q->where('language_id', $language_id);}, 
            'options', 
            'validation', 
            'answers', 
            'answers.info' => function($q) use($language_id) { $q->where('language_id', $language_id);},  
            'gridQuestions', 
            'gridQuestions.info' => function($q) use($language_id) { $q->where('language_id', $language_id);}, 
            ])->first();

        $clone = $item->replicate();

        if($section_id !== null){
            $clone->form_builder_section_id = $section_id;
        }

        $clone->save();  

        $clone = $clone->toArray();

        $item = $item->toArray();

        // Info
        foreach ($item['info'] as $key => $value) {
                \App\Models\FormBuilderQuestionInfo::create([
                    "name" => $value['name'],
                    "value" => $value['value'] ?? "",
                    "question_id" => $clone['id'],
                    "language_id" => $language_id
                ]);
           
        }


        // options
        \App\Models\FormBuilderQuestionOption::create([
            "question_id" => $clone['id'],
            "add_other" => $item['options']['add_other'],
            "response_validation" => $item['options']['response_validation'],
            "section_based" => $item['options']['section_based'],
            "limit" => $item['options']['limit'],
            "shuffle" => $item['options']['shuffle'],
            "date" => $item['options']['date'],
            "time" => $item['options']['time'],
            "year" => $item['options']['year'],
            "min" => $item['options']['min'],
            "max" => $item['options']['max'],
            "min_label" => $item['options']['min_label'],
            "max_label" => $item['options']['max_label'],
            "time_type" => $item['options']['time_type'],
            'description_visible' => $item['options']['description_visible'],
        ]);

        // Validation
        if($item['options']['response_validation'] == 1){
            \App\Models\FormBuilderQuestionValidation::create([
                "question_id" => $clone['id'],
                'type' =>$item['validation']['type'], 
                'rule' =>$item['validation']['rule'], 
                'value' =>$item['validation']['value'], 
                'value_2' =>$item['validation']['value_2'] ?? '', 
                'custom_error' =>$item['validation']['custom_error']
            ]);
        }

         // ADD QUESTION ANSWERS 
         if (
            $item['type'] === "multiple_choice" ||
            $item['type'] === "checkboxes" ||
            $item['type'] === "drop_down"
            ) {
                
                foreach ($item['answers'] as $key => $value) {
                    info($value);
                    $createAnswers =  \App\Models\FormBuilderAnswer::create([
                        'question_id' => $clone['id'],
                        'next_section' => $value['next_section'],
                        'sort_order' => $key
                    ]);
                    foreach ($value['info'] as $key => $info) {
                        \App\Models\FormBuilderAnswerInfo::create([
                            "name" => $info['name'],
                            "value" => $info['value'],
                            "answer_id" => $createAnswers->id,
                            "language_id" => $language_id
                        ]);
                    }
                }
            }
            // ADD GRID QUESTIONS

            if (
                $item['type'] === "multiple_choice_grid" ||
                $item['type'] === "tick_box_grid" 
            ) {
                    foreach ($item['grid_questions'] as $key => $value) {
                        $createGridQuestion =  \App\Models\FormBuilderGridQuestion::create([
                            'question_id' => $clone['id'],
                            'sort_order' => $key
                        ]);
                        foreach ($value['info'] as $key => $info) {
                            \App\Models\FormBuilderGridQuestionInfo::create([
                                "name" => $info['name'],
                                "value" => $info['value'],
                                "question_id" => $createGridQuestion->id,
                                "language_id" => $language_id
                            ]);
                        }
                    }

                }
                
                // ADD GRID ANSWER 
           
                if (
                $item['type'] === "multiple_choice_grid" ||
                $item['type'] === "tick_box_grid" 
                ) {
                        foreach ($item['answers'] as $key => $value) {
                            $createAnswers =  \App\Models\FormBuilderAnswer::create([
                                'question_id' => $clone['id'],
                                'sort_order' => $key
                            ]);
                            foreach ($value['info'] as $key => $info) {
                                \App\Models\FormBuilderAnswerInfo::create([
                                    "name" => $info['name'],
                                    "value" => $info['value'],
                                    "answer_id" => $createAnswers->id,
                                    "language_id" => $language_id
                                ]);
                            }   
                        }

                }

                return $clone['id'];
    }

    
    /**
     * getSection
     *
     * @param  mixed $section_id
     * @param  mixed $event_id
     * @param  mixed $language_id
     * @return void
     */
    public function getSection($section_id, $event_id, $language_id)
    {
        $section = \App\Models\FormBuilderSection::where("id", $section_id)
        ->with([
            'info' => function($q) use($language_id) { $q->where('language_id', $language_id);},
            'questions', 
            'questions.info' => function($q) use($language_id) { $q->where('language_id', $language_id);},
            'questions.answers',
            'questions.answers.info' => function($q) use($language_id) { $q->where('language_id', $language_id);},
            'questions.options',
            'questions.validation',
            'questions.gridQuestions',
            'questions.gridQuestions.info' => function($q) use($language_id) { $q->where('language_id', $language_id);},
            ])
        ->first();


        if(!$section){
            return [
                "status" => 0,
                "message" => "Form not Found"
            ];
        }
        
        $section = $section->toArray();

        
        $section = $this->mapItemsInfoData($section);

        $section['questions'] = collect($section["questions"])->map(function ($question) {
            $question = $this->mapItemsInfoData($question);
            $question['answers'] = collect($question["answers"])->map(function ($answers) {
                $answers = $this->mapItemsInfoData($answers);
                return $answers;
            })->toArray();
            $question['grid_questions'] = collect($question["grid_questions"])->map(function ($grid_questions) {
                $grid_questions = $this->mapItemsInfoData($grid_questions);
                return $grid_questions;
            })->toArray();
            if(is_array($question['options'])){
                $question['options'] = array_filter($question['options'], "strlen");
            }
            return $question;
        })->toArray();

        return $section;
    }
    
    /**
     * submitForm
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function submitForm($formInput, $event_id, $registration_form_id)
    {
        $order_id = $formInput['order_id'];
        $form_id = $formInput['form_id'];
        $attendee_id = $formInput['attendee_id'];
        $formData = json_decode($formInput['data'], true);
        
        foreach ($formData as $section_id => $question_answers) {
            
            foreach ($question_answers as $question_id => $question_answer) {

                if($question_answer['was_answered']){
                    if($question_answer['question_type'] === 'short_answer' ||
                       $question_answer['question_type'] === 'paragraph' || 
                       $question_answer['question_type'] === 'date' ||
                       $question_answer['question_type'] === 'time' ||
                       $question_answer['question_type'] === 'linear_scale' ||
                       $question_answer['question_type'] === 'drop_down' ||
                       $question_answer['question_type'] === 'multiple_choice'
                    )   
                    {
                        $types_for_answer_id = ['drop_down', 'multiple_choice'];
    
                        $answer_exist = \App\Models\FormBuilderFormResult::where('form_id', $form_id)
                        ->where('registration_form_id', $registration_form_id)
                        ->where('order_id', $order_id)
                        ->where('attendee_id', $attendee_id)
                        ->where('event_id', $event_id)
                        ->where('section_id', $section_id)
                        ->where('question_id', $question_id)
                        ->first();
    
                        if($answer_exist){
                            $answer_exist = \App\Models\FormBuilderFormResult::where('form_id', $form_id)
                            ->where('registration_form_id', $registration_form_id)
                            ->where('order_id', $order_id)
                            ->where('attendee_id', $attendee_id)
                            ->where('event_id', $event_id)
                            ->where('section_id', $section_id)
                            ->where('question_id', $question_id)
                            ->update([
                                'answer_id' => in_array($question_answer['question_type'], $types_for_answer_id) ? $question_answer['answer_id'] : '',
                                'answer_value' => !in_array($question_answer['question_type'], $types_for_answer_id) ? $question_answer['answer_value'] : '',
                            ]);
                        }
                        else{
                            \App\Models\FormBuilderFormResult::create([
                                'form_id'=> $form_id,
                                'registration_form_id' => $registration_form_id,
                                'order_id' => $order_id,
                                'attendee_id' => $attendee_id,
                                'event_id' => $event_id,
                                'section_id' => $section_id,
                                'question_id' => $question_id,
                                'answer_id' => in_array($question_answer['question_type'], $types_for_answer_id) ? $question_answer['answer_id'] : '',
                                'grid_question_id' => "",
                                'question_type' => $question_answer['question_type'],
                                'answer_value' => !in_array($question_answer['question_type'], $types_for_answer_id) ? $question_answer['answer_value'] : '',
                            ]);
                        }
                        
                    }
                    else if($question_answer['question_type'] === 'checkboxes' ||
                            $question_answer['question_type'] === 'multiple_choice_grid' ||
                            $question_answer['question_type'] === 'tick_box_grid'
                            )
                    {
    
                        $types_for_gird_id = ['multiple_choice_grid', 'tick_box_grid'];
    
                        $answer_exist = \App\Models\FormBuilderFormResult::where('form_id', $form_id)
                        ->where('registration_form_id', $registration_form_id)
                        ->where('order_id', $order_id)
                        ->where('attendee_id', $attendee_id)
                        ->where('event_id', $event_id)
                        ->where('section_id', $section_id)
                        ->where('question_id', $question_id)
                        ->delete();
                        
                        foreach ($question_answer['answer_id'] as $grid_id => $answers) {
                            if($question_answer['question_type'] === 'tick_box_grid'){
                                foreach($answers as $key => $answer){
                                    $create = \App\Models\FormBuilderFormResult::create([
                                            'form_id'=> $form_id,
                                            'registration_form_id' => $registration_form_id,
                                            'order_id' => $order_id,
                                            'attendee_id' => $attendee_id,
                                            'event_id' => $event_id,
                                            'section_id' => $section_id,
                                            'question_id' => $question_id,
                                            'answer_id' => $answer,
                                            'grid_question_id' => in_array($question_answer['question_type'], $types_for_gird_id) ? $grid_id : '',
                                            'question_type' => $question_answer['question_type'],
                                            'answer_value' => '',
                                        ]);
                                        $new_answer[in_array($question_answer['question_type'], $types_for_gird_id) ? $grid_id : ''] = $create->id;
                                    
                                }
                            }
                            else{
                                    $create = \App\Models\FormBuilderFormResult::create([
                                        'form_id'=> $form_id,
                                        'registration_form_id' => $registration_form_id,
                                        'order_id' => $order_id,
                                        'attendee_id' => $attendee_id,
                                        'event_id' => $event_id,
                                        'section_id' => $section_id,
                                        'question_id' => $question_id,
                                        'answer_id' => $answers,
                                        'grid_question_id' => in_array($question_answer['question_type'], $types_for_gird_id) ? $grid_id : '',
                                        'question_type' => $question_answer['question_type'],
                                        'answer_value' => '',
                                    ]);
                                    $new_answer[in_array($question_answer['question_type'], $types_for_gird_id) ? $grid_id : '']=$create->id;
    
                            }
    
    
                        }
    
                        
                    }
                }


            }

        }

        $results = $this->getFormResult(['order_id' =>  $order_id, 'attendee_id' => $attendee_id, "form_id" => $form_id], $event_id, $registration_form_id, mapped:true);
        



        return  [
            "status" => 1,
            "result" => $results,
            "message" => "Question Deleted successfully"
        ];
    }

    
    /**
     * getFormResult
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return array
     */
    public function getFormResult($formInput, $event_id, $registration_form_id, $mapped=false):array
    {
        $order_id = $formInput['order_id'];
        $form_id = $formInput['form_id'];
        $attendee_id = $formInput['attendee_id'];

        $answers = \App\Models\FormBuilderFormResult::where('form_id', $form_id)
                        ->where('order_id', $order_id) 
                        ->where('attendee_id', $attendee_id) 
                        ->get()->toArray();
        if($mapped){
            $section_results = [];
            foreach($answers as $key => $value){
                if(!isset($section_results[$value['question_id']])){
                    if($value['question_type'] === 'checkboxes')
                    {
                        $value['answer_id'] = [$value['answer_id']];
                        $section_results[$value['question_id']]= $value;
                    }
                    else if($value['question_type'] === 'multiple_choice_grid'){
                        $value['answer_id'] = [$value['grid_question_id']=> $value['answer_id']];
                        $section_results[$value['question_id']] = $value;
                    }
                    else if($value['question_type'] === 'tick_box_grid'){
                        $value['answer_id'] = [$value['grid_question_id']=> [$value['answer_id']]];
                        $section_results[$value['question_id']] = $value;
                    }
                    else{
                        $value['answer_id'] = $value['answer_id'];
                        $section_results[$value['question_id']] = $value;
                    }
                }else{
                    $answerIds = $section_results[$value['question_id']]['answer_id'];

                    if($value['question_type'] === 'multiple_choice_grid'){
                        $answerIds[$value['grid_question_id']] = $value['answer_id'];
                        $section_results[$value['question_id']]['answer_id'] = $answerIds;
                    }
                    else if($value['question_type'] === 'tick_box_grid'){
                                $answerIds[$value['grid_question_id']][] = $value['answer_id'];
                                $section_results[$value['question_id']]['answer_id'] = $answerIds;
                    }
                    else if($value['question_type'] === 'checkboxes'){
                        $section_results[$value['question_id']]['answer_id'] = is_array($answerIds) ? [...$answerIds, $value['answer_id']] : [$value['answer_id']];
                    }
                    else{
                        $section_results[$value['question_id']]['answer_id'] =  $value['answer_id'];
                    }

                }
            }
            $answers = $section_results;
        }

        return $answers;
    }
    
    /**
     * getFormAndResult
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return array
     */
    public function getFormAndResult($formInput, $event_id, $registration_form_id):array
    {
        if(!isset($formInput['form_id']) || 
           !isset($formInput['order_id']) || 
           !isset($formInput['attendee_id']))
        {
            return [
                "status" => 1,
                "message" => "Invalid form data"
            ];
        }

        $form = $this->getForm($formInput, $event_id, $registration_form_id);
        $results = $this->getFormResult($formInput, $event_id, $registration_form_id, mapped:true);

        

        return  [
            "status" => 1,
            "data" => [
                "form" => $form['data'],
                "result" => $results
            ],
            "message" => "Form and results"
        ];
    }

    
    /**
     * getFormsStatic
     *
     * @param  mixed $event_id
     * @param  mixed $language_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public static function getFormsStatic($event_id, $language_id, $registration_form_id)
    {
    
        $forms = \App\Models\FormBuilderForm::where("event_id", $event_id)->where("registration_form_id", $registration_form_id)
        ->with(['info' => function($q) use($language_id) { $q->where('language_id', $language_id);}])
        ->where('active', 1)
        ->orderBy('sort_order', 'ASC')
        ->get()
        ->toArray();

        foreach ($forms as $key => $form) {

            foreach ($form['info'] as $value) {
                $form[$value['name']] = $value['value'];
            }

            unset($form['info']);

            $forms[$key] = $form;
        }

        return $forms;
    }
    
    /**
     * renameForm
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function renameForm($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }

        if(!isset($formInput['form_id']) || 
           !isset($formInput['title']))
        {
            return [
                "status" => 1,
                "message" => "Invalid form data"
            ];
        }

        \App\Models\FormBuilderFormInfo::where("form_id", $formInput['form_id'])->where("name", 'title')->where("language_id", $event['language_id'])->update([
            "value" => $formInput['title']
        ]);

        return  [
            "status" => 1,
            "message" => "Form title updated..."
        ];

    }
        
    /**
     * copyForm
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function copyForm($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }

        if(!isset($formInput['form_id']))
        {
            return [
                "status" => 1,
                "message" => "Invalid form data"
            ];
        }

        $form = \App\Models\FormBuilderForm::where("id", $formInput['form_id'])->with([
            'info' => function($q) use($event) { return $q->where('language_id', $event['language_id']);}, 
            'sections'
        ])->first();

        $cloneForm = $form->replicate(); 

        $cloneForm->save();

        $form->toArray();

        $cloneForm->toArray();

        foreach ($form['info'] as $infoItem) {
            $value = $infoItem['name']  === "title" ? $infoItem['value']." Copy" : $infoItem['value'];
            \App\Models\FormBuilderFormInfo::create([
                "name" => $infoItem['name'], 
                'value' => $value ?? "", 
                'language_id'=> $infoItem['language_id'], 
                'form_id' => $cloneForm['id']
            ]);
        }

        $sections_log = [];
        $sections_next_section = [];

        foreach ($form['sections'] as $key => $section) {
           $new_section = $this->duplicateSection($section['id'], $event_id, $event['language_id'], $cloneForm['id']);
           $sections_log[$section['id']] = $new_section['id'];
        //    $sections_next_section[$section['id']] = $new_section['next_section'];
        }

        //
        
        $question_ids = \App\Models\FormBuilderQuestion::whereIn('form_builder_section_id', array_values($sections_log))->pluck('id');
        
        $answers = \App\Models\FormBuilderAnswer::whereIn('question_id', $question_ids)->get()->toArray();
        
        info($sections_log);

        foreach ($answers as $key => $answer) {
            if($answer['next_section'] !== "" && $answer['next_section'] !== "CONTINUE" && $answer['next_section'] !== "SUBMIT"){
                \App\Models\FormBuilderAnswer::where('id', $answer['id'])->update([
                    "next_section" => $sections_log[$answer['next_section']]
                ]);
            }
        }

        // 
        $new_sections = \App\Models\FormBuilderSection::whereIn('id', array_values($sections_log))->get()->toArray();

        foreach ($new_sections as $key => $section) {
            if($section['next_section'] !== "" && $section['next_section'] !== "CONTINUE" && $section['next_section'] !== "SUBMIT"){
                \App\Models\FormBuilderSection::where('id', $section['id'])->update([
                    "next_section" => $sections_log[$section['next_section']]
                ]);
            }
        }




        $forms = $this->getForms([], $event_id, $registration_form_id);
        
        return  [
            "status" => 1,
            "data" => $forms,
            "message" => "Form cloned successfully..."
        ];
    }


        
    /**
     * duplicateSection
     *
     * @param  mixed $section_id
     * @param  mixed $event_id
     * @param  mixed $language_id
     * @return void
     */
    public function duplicateSection($section_id, $event_id, $language_id, $form_id)
    {
        $item = \App\Models\FormBuilderSection::where('id', $section_id)->with([ 'info'  => function($q) use($language_id) { $q->where('language_id', $language_id);},  'questions' ])->first();

        $clone = $item->replicate();

        $clone->form_builder_form_id = $form_id;

        $clone->save();
        
        $item = $item->toArray();

        foreach ($item['info'] as $key => $value) {
            \App\Models\FormBuilderSectionInfo::create([
                "name" => $value['name'],
                "value" => $value['value'] ?? "",
                "section_id" => $clone->id,
                "language_id" => $language_id
            ]);
        }

        foreach ($item['questions'] as $key => $question) {
            $this->duplicateQuestion($question['id'],  $event_id, $language_id, $clone->id);
        }

        $section = $this->getSection($clone->id, $event_id, $language_id);

        return $section;
    }
    
        
    /**
     * deleteForm
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function deleteForm($formInput, $event_id, $registration_form_id)
    {
        $event = $this->getEvent($event_id);
        if(empty($event)){
            return [
                "status" => 0,
                "message" => "Event not Found"
            ];
        }

        if(!isset($formInput['form_id']))
        {
            return [
                "status" => 1,
                "message" => "Invalid form data"
            ];
        } 

        // form 
        \App\Models\FormBuilderForm::where("id", $formInput['form_id'])->delete();
        \App\Models\FormBuilderFormInfo::where("form_id", $formInput['form_id'])->delete();

        // form section 
        $formSections =  \App\Models\FormBuilderSection::where("form_builder_form_id", $formInput['form_id'])->get();
        
        $formSectionIds = [];
        foreach ($formSections as $key => $item) {
            $formSectionIds[] = $item['id'];
        }

        $formSections =  \App\Models\FormBuilderSectionInfo::whereIn("section_id", $formSectionIds)->delete();
        \App\Models\FormBuilderSection::where("form_builder_form_id", $formInput['form_id'])->delete();

        // form questions
        \App\Models\FormBuilderQuestion::whereIn('form_builder_section_id', $formSectionIds)->delete();
        
        $forms = $this->getForms([], $event_id, $registration_form_id);

        return  [
            "status" => 1,
            "data" => $forms,
            "message" => "Form deleted successfully..."
        ];
    }


    public function saveFormGlobal($formInput, $event_id, $registration_form_id)
    {
        
        foreach ($formInput['sections'] as $key => $section) {
            $new_section = $this->saveSection(array_merge($section, ['form_builder_form_id'=>$formInput['id']]), $event_id, $registration_form_id);
                foreach ($section['questions'] as $question) {
                    if(isset($question['id'])){
                        $this->updateQuestion($question, $event_id, $registration_form_id);
                    }else{
                        $this->addQuestion(array_merge($question, ['form_builder_form_id' => $formInput['id'],
                        'form_builder_section_id' => $new_section['data']['section_id']]), $event_id, $registration_form_id);
                    }
                }
        }

        return $this->getForm(['form_id'=>$formInput['id']], $event_id, $registration_form_id);
        
    }
    
}