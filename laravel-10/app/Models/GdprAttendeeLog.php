<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GdprAttendeeLog extends Model
{
    protected $table = 'conf_gdpr_attendee_log';
    
    protected $fillable = ['event_id', 'attendee_id', 'gdpr_accept', 'gdpr_description','admin_id'];

    use SoftDeletes;

    public function info()
    {
        return $this->hasMany('\App\Models\Attendee', 'id', 'attendee_id');
    }

    public function scopeAttendeeByEmail($query, $order = 'asc', $group_by)
    {
        if ($group_by == 0) {
            $query->join('conf_attendees', 'conf_attendees.id', '=', 'conf_gdpr_attendee_log.attendee_id')
                ->orderBy('conf_gdpr_attendee_log.created_at', 'desc')
                ->orderBy('conf_gdpr_attendee_log.gdpr_accept', $order)
                ->select('conf_gdpr_attendee_log.*');
        } else {
            $query->join('conf_attendees', 'conf_attendees.id', '=', 'conf_gdpr_attendee_log.attendee_id')
                ->orderBy('conf_gdpr_attendee_log.created_at', 'desc')
                ->orderBy('conf_gdpr_attendee_log.gdpr_accept', $order)
                ->select('conf_gdpr_attendee_log.*')
                ->groupBy('conf_attendees.email');
        }
    }
}
