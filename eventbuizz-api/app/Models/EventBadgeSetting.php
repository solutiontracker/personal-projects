<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventBadgeSetting extends Model
{
    protected $table = 'conf_event_badges_settings';
    protected $fillable = ['event_id', 'email_template_id', 'event_template_id', 'description'];



}

