<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CheckInLog extends Model
{
    protected $table = 'conf_checkin_log';
    
    protected $fillable = ['checkin', 'checkout', 'event_id', 'organizer_id', 'attendee_id', 'admin_id', 'type_name', 'type_id', 'data', 'status', 'delegate'];

    use SoftDeletes;

    public function attendees()
    {
        return $this->hasMany('\App\Models\Attendee', 'id', 'attendee_id');
    }

    public function adminUser()
    {
        return $this->belongsTo('\App\Models\CheckInUser', 'admin_id', 'id');
    }

    public function attendee()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    public function delegates()
    {
        return $this->hasMany('\App\Models\CheckInLog', 'delegate', 'delegate');
    }
}
