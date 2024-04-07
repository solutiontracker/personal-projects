<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSubRegistrationSetting extends Model
{
    use Observable;
    protected $table = 'conf_event_sub_registration_settings';
    protected $fillable = ['event_id', 'listing', 'answer', 'link_to', 'show_optional', 'update_answer_email', 'result_email'];
}

