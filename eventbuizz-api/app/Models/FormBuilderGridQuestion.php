<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderGridQuestion extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_grid_questions';
    protected $fillable = ['id', 'sort_order', 'question_id'];
    
    // relations
    public function info()
    {
        return $this->hasMany('\App\Models\FormBuilderGridQuestionInfo', 'question_id');
    }

    //Handlers
    public static function boot() {
        parent::boot();

        static::deleting(function($gridQuestion) { // before delete() method call this
             $gridQuestion->info()->delete();
        });
    }
}
