<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeInvite extends Model
{
    protected $table = 'conf_attendee_invites';

    protected $attributes = [
        'last_name' => '',
        'phone' => ''
    ];

    protected $fillable = ['event_id','organizer_id','first_name','last_name','email','phone','sms_sent','status', 'not_send', 'date_sent', 'is_resend', 'is_attending', 'ss_number', 'allow_vote', 'ask_to_speak', 'allow_qa', 'registration_form_id'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];


    public function events()
    {
        return $this->belongsTo('Events','event_id','id');
    }

    public function attendee()
    {
        return $this->belongsTo('Attendees','email','email');
    }

}
