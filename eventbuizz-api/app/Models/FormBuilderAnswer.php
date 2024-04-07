<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderAnswer extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_answers';
    protected $fillable = ['id', 'question_id', 'sort_order', 'next_section', 'type'];
    
    // relations
    public function info()
    {
        return $this->hasMany('\App\Models\FormBuilderAnswerInfo', 'answer_id');
    }

    //Handlers
    public static function boot() {
        parent::boot();

        static::deleting(function($answer) { // before delete() method call this
             $answer->info()->delete();
        });
    }
}
