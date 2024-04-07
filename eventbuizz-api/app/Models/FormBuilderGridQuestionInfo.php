<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderGridQuestionInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_grid_question_info';
    protected $fillable = ['id', 'name', 'value', 'language_id', 'question_id'];
}
