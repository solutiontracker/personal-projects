<?php

namespace App\Models;

use App\Traits\Observable;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventCheckInSetting extends Model
{
    protected $table = 'conf_event_checkin_settings';

    protected $fillable = ['id', 'event_id', 'status', 'type', 'single_type', 'radius', 'latitude', 'longitude', 'address', 'gps_checkin', 'event_checkin','program_checkin','group_checkin','ticket_checkin','validate_program_checkin', 'show_qrcode', 'show_wp', 'show_vp', 'created_at', 'updated_at', 'deleted_at'];
    
    use Observable;
   
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}
