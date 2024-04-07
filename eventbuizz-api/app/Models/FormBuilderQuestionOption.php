<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderQuestionOption extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_question_options';
    protected $fillable = [
        'id',
        "question_id",
        "add_other",
        "response_validation",
        "section_based",
        "limit",
        "shuffle",
        "date",
        "time",
        "year",
        "min",
        "max",
        "min_label",
        "max_label",
        "time_type",
        'description_visible'
    ];
}
