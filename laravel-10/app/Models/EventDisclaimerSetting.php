<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventDisclaimerSetting extends Model
{
    protected $table = 'conf_event_disclaimer_settings';
    protected $fillable = ['event_id', 'mobile_app', 'reg_site', 'reg_site_login'];
}

