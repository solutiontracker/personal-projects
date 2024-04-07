<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerCalendarApiRequest extends Model {
    use SoftDeletes;
    protected $table = 'conf_organizer_calender_api_request';
    protected $fillable = ['organizer_id', 'user_IP', 'api_key', 'request_date', 'status'];
    protected $dates = ['deleted_at'];
}