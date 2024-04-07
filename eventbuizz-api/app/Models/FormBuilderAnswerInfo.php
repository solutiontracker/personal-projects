<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderAnswerInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_answer_info';
    protected $fillable = ['id', 'name', 'value', 'language_id', 'answer_id'];
}
