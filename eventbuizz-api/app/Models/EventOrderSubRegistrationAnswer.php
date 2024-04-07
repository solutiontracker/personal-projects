<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventOrderSubRegistrationAnswer extends Model
{
    protected $table = 'conf_event_order_sub_registration_answer';

    protected $fillable = ['id', 'order_id', 'question_id', 'answer_id', 'matrix_id', 'comment', 'answer', 'attendee_id', 'agenda_id'];

	protected $dates = ['deleted_at'];

}
