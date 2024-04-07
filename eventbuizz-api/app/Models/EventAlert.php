<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAlert extends Model
{
    protected $table = 'conf_event_alerts';
    protected $fillable = ['event_id','pre_schedule','alert_date','alert_time','sendto','alert_email','alert_sms',
        'status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\EventAlertInfo', 'alert_id');
    }

    public function attendees()
    {
        return $this->hasMany('\App\Models\EventAlertAttendee', 'alert_id');
    }

    public function agendas()
    {
        return $this->hasMany('\App\Models\EventAlertAgenda', 'alert_id');
    }

    public function individuals()
    {
        return $this->hasMany('\App\Models\EventAlertIndividual', 'alert_id');
    }

    public function groups()
    {
        return $this->hasMany('\App\Models\EventAlertGroup', 'alert_id');
    }

    public function workshops()
    {
        return $this->hasMany('\App\Models\EventWorkshopAlert', 'alert_id');
    }

    public function polls()
    {
        return $this->hasMany('\App\Models\EventPollAlert', 'alert_id');
    }

    public function surveys()
    {
        return $this->hasMany('\App\Models\EventSurveyAlert', 'alert_id');
    }

    public function sponsors()
    {
        return $this->hasMany('\App\Models\EventSponsorAlert', 'alert_id');
    }

    public function exhibitors()
    {
        return $this->hasMany('\App\Models\EventExhibitorAlert', 'alert_id');
    }

    public function attendeeTypes()
    {
        return $this->hasMany('\App\Models\EventAlertAttendeeType', 'alert_id');
    }
}
