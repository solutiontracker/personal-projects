<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAgendaGroup extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_agenda_groups';
    protected $fillable = ['agenda_id','group_id'];
    protected $dates = ['deleted_at'];
}



