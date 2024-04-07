<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderQuestion extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_questions';
    protected $fillable = ['id', 'form_builder_form_id', 'form_builder_section_id', 'type', 'required', 'sort_order'];
    
    // relations
    public function info()
    {
        return $this->hasMany('\App\Models\FormBuilderQuestionInfo', 'question_id');
    }
    public function options()
    {
        return $this->hasOne('\App\Models\FormBuilderQuestionOption', 'question_id');
    }
    public function answers()
    {
        return $this->hasMany('\App\Models\FormBuilderAnswer', 'question_id')->orderby('sort_order');
    }
    public function validation()
    {
        return $this->hasOne('\App\Models\FormBuilderQuestionValidation', 'question_id');
    }
    public function gridQuestions()
    {
        return $this->hasMany('\App\Models\FormBuilderGridQuestion', 'question_id')->orderby('sort_order');
    }
    public function result()
    {
        return $this->hasMany('\App\Models\FormBuilderFormResult', 'question_id')->where('order_id', request()->order_id)->where('attendee_id', request()->attendee_id);
    }

    //Event Handlers
   

    public function delete()
    {
        $this->info()->delete();
        $this->options()->delete();
        $this->validation()->delete();
        foreach($this->answers as $answer){
            $answer->delete();
        }
        foreach($this->gridQuestions as $gridQuestion){
            $gridQuestion->delete();
        }
        // delete the user
        return parent::delete();
    }
}
