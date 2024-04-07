<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeRegistrationLog extends Model
{
    protected $table = 'conf_attendees_registration_log';
    protected $fillable = [
        'organizer_id',
        'event_id',
        'attendee_id',
        'reg_date',
        'status',
        'cancel_date',
        'comments',
        'model',
        'app_type',
        'action',
        'register_by',
        'register_by_id',
        'ip',
        'user_agent'
    ];
    public $timestamps = false;

    public static function UpdateStatus($event_id, $attendees, $status)
    {
        if ($status == 'cancel') {
            $update_col = [
                'status' => $status,
                'cancel_date' => \Carbon\Carbon::now()->toDateTimeString(),
            ];
        } else {
            $update_col = [
                'status' => $status,
            ];
        }

        return self::where('event_id', $event_id)->whereIn('attendee_id', $attendees)->update($update_col);
    }
}
