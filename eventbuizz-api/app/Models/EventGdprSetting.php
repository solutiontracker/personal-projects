<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventGdprSetting extends Model
{
    protected $table = 'conf_event_gdpr_settings';
    protected $fillable = ['event_id', 'enable_gdpr', 'attendee_invisible','gdpr_required'];
}

