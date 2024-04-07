<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeChangeLog extends Model
{

    use SoftDeletes;
    protected $table = 'conf_attendee_change_log';
    protected $fillable = ['event_id', 'attendee_id','logged_by_id', 'logged_by_user_type', 'attribute_name', 'old_value', 'new_value', 'created_at', 'updated_at', 'deleted_at', 'action', 'action_model', 'app_type'];
    protected $dates = ['deleted_at'];

}