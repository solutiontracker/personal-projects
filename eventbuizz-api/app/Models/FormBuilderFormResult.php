<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderFormResult extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_form_result';
    protected $fillable = [
        'id', 
        'form_id',
        'registration_form_id',
        'order_id',
        'attendee_id',
        'event_id',
        'section_id',
        'question_id',
        'answer_id',
        'grid_question_id',
        'question_type',
        'answer_value',
        'created_at',
        'updated_at',
    ];

}
