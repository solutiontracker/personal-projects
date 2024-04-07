<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAgendaRating extends Model {

    use SoftDeletes;
    protected $table = 'conf_event_agenda_rating';
    protected $fillable = ['event_id','agenda_id','attendee_id', 'comment', 'rate'];
    protected $dates = ['deleted_at'];

}