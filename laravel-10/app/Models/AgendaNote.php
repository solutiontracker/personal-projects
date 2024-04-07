<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaNote extends Model
{
	use SoftDeletes;
	protected $table = 'conf_agenda_notes';
    protected $fillable = ['event_id', 'attendee_id', 'agenda_id', 'notes', 'status'];
	protected $dates = ['deleted_at'];

}