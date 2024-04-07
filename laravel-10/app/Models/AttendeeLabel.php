<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeLabel extends Model
{
    protected $table = 'conf_attendees_labels';
    protected $fillable = ['event_id','label_alias','language_id','value'];
}
