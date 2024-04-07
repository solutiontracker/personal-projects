<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaitingListAttendee extends Model
{
    protected $table = 'conf_wiatinglist_attendees';
    protected $fillable = ['event_id', 'attendee_id', 'status', 'order_data', 'date_sent'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function attendee()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    public function event()
    {
        return $this->belongsTo('\App\Models\Event', 'event_id', 'id');
    }

    /**
     * get updated status for waiting
     * list attendees
     * @param int $status
     * @param $data
     * @return string
     */
    static public function getOrderAttendeeStatus(int $status, $data = []) {

        switch ($status){
            case 0:
                return 'Pending';
            case 1:
                
                $current_time = time();
                
                $waiting_list_setting = EventWaitingListSetting::where("event_id", $data["event_id"])->first();

                if($waiting_list_setting){
                    $validity_duration = (int)$waiting_list_setting->validity_duration * 60 * 60;
                    $expiry_time =strtotime($data['date_sent']) + $validity_duration;

                    //if the offer is expired
                    if($expiry_time <= $current_time && $validity_duration !== 0) {

                        //update status to expired => 4
                        WaitingListAttendee::where('attendee_id', $data['attendee_id'])
                            ->where('event_id', $data["event_id"])
                            ->update(['status' => 4]);

                        return 'Expired';
                    }
                    else {
                        return 'Sent';
                    }
                }
            case 2:
                return 'Attending';
            case 3:
                return 'Not interested';
            case 4:
                return 'Expired';
            default:
                return "Invalid Status";
        }

    }
}
