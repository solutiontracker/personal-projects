<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class ReservationRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
        
    /**
     * reservationExist
     *
     * @param  mixed $eventId
     * @param  mixed $date
     * @param  mixed $time_from
     * @param  mixed $time_to
     * @param  mixed $entity_id
     * @param  mixed $entity_type
     * @return void
     */
    public static function reservationExist($eventId, $date, $time_from, $time_to, $entity_id, $entity_type)
    {
        $check_reservation = \DB::select(DB::raw("select id from conf_reservations where
            date = '" . date('Y-m-d', strtotime($date)) . "' and entity_id=" . $entity_id . " and event_id=" . $eventId . " and  entity_type = '" . $entity_type . "' and ((time_from > '" . $time_from . "' and time_to < '" . $time_to . "') or
            ( (time_from > '" . $time_from . "' and time_from < '" . $time_to . "') and time_to > '" . $time_to . "') or
            (time_from < '" . $time_from . "' and ( time_to > '" . $time_from . "' and  time_to < '" . $time_to . "')) or
            (time_from < '" . $time_from . "' and time_to > '" . $time_to . "') or (time_from='" . $time_from . "') or (time_to='" . $time_to . "'))"));

        if (empty($check_reservation)) {
            return 0;
        } else {
            return $check_reservation[0]->id;
        }
    }

    /**
     * slotExist
     *
     * @param  mixed $date
     * @param  mixed $time_from
     * @param  mixed $time_to
     * @param  mixed $entity_id
     * @param  mixed $entity_type
     * @param  mixed $contact_id
     * @param  mixed $event_id
     * @return void
     */
    public static function slotExist($date, $time_from, $time_to, $entity_id, $entity_type, $contact_id, $event_id = '') {
        $reservation = \App\Models\ReservationSlot::where('date', date('Y-m-d', strtotime($date)))->where('entity_id', $entity_id)
                            ->where('entity_id', $entity_id)
                            ->where('entity_type', $entity_type)
                            ->where('event_id', $event_id)
                            ->where('contact_id', $contact_id)
                            ->where(function($query) => {
                                return $query->where(function($query) {
                                    return $query->where('time_from', '>', date("H:i", strtotime($time_from)))
                                        ->where('time_to', '<', $time_to);
                                })
                                ->orWhere(function($query) {
                                    return $query->where(function($query) {
                                        return $query->where('time_from', '>', $time_from)
                                        ->where('time_from', '<', $time_to);
                                    })
                                    ->where('time_to', '>', $time_to)
                                })
                                ->orWhere(function($query) {
                                    return $query->where(function($query) {
                                        return $query->where('time_to', '>',  $time_from)
                                        ->where('time_to', '<', $time_to);
                                    })
                                    ->where('time_from', '<', $time_from)
                                })
                                ->orWhere(function($query) {
                                    return $query->where(function($query) {
                                        return $query->where('time_from', '<',  $time_from)
                                        ->where('time_to', '>', $time_to);
                                    })
                                    ->orWhere('time_from', '=', $time_from)
                                })
                                ->orWhere('time_to', '=', $time_to)
                            })
                            ->first()
    
        if (empty($reservation)) {
            return true;
        } else {
            return false;
        }
    }
}
