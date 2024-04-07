<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMessage extends Model
{
    protected $table = 'conf_event_messages';
    protected $fillable = ['event_id','group_id','seq','created_on','created_by','subject','body'];
}
