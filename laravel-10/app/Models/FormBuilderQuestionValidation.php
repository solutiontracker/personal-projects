<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderQuestionValidation extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_question_validations';
    protected $fillable = ['id', 'question_id', 'type', 'rule', 'value', 'value_2', 'custom_error'];
}
