<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventWaitingListSetting extends Model
{
    protected $table = 'conf_event_waiting_list_settings';

    protected $fillable = ['event_id', 'status', 'offerletter', 'validity_duration', 'registration_form_id'];


    /**
     * @param $attendee
     * @param $event_id
     * @return bool
     */
    static public function canSendOfferLetter($attendee, $event_id): bool
    {
        $waitinglist_attendee = WaitingListAttendee::where('attendee_id','=', $attendee['id'])
            ->where('event_id','=',$event_id)
            ->get()
            ->toArray();

        $waitingListSetting = EventWaitingListSetting::where('event_id','=', $event_id)
            ->get()
            ->toArray();

        //if status is pending or offer has expired
        //we can send invite
        if ($waitinglist_attendee[0]['status'] == '0' || $waitinglist_attendee[0]['status'] == '4') {
            return true;
        } else {
            $validity_duartion = $waitingListSetting['validity_duration'];
            $validity_duartion = ($validity_duartion*60)*60;
            $current_time = time();
            $sent_date = strtotime($waitinglist_attendee[0]['date_sent']);
            $total_time = $sent_date+$validity_duartion;
            if($total_time<$current_time) {
                return false;
            } else {
                return true;
            }
        }

    }
    
}
