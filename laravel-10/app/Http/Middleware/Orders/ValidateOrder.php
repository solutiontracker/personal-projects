<?php

namespace App\Http\Middleware\Orders;

use Closure;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

class ValidateOrder
{
    /**
     * @param mixed $request
     * @param Closure $next
     *
     * @return [type]
     */
    public function handle($request, Closure $next)
    {
        if($request->order_id && $request->event_id) {
            
            $order = \App\Models\BillingOrder::where('conf_billing_orders.id', $request->order_id)->where('conf_billing_order_attendees.order_id', $request->order_id)->where('conf_billing_orders.event_id', $request->event_id)->join('conf_billing_order_attendees', 'conf_billing_order_attendees.attendee_id', '=', 'conf_billing_orders.attendee_id')->select('conf_billing_order_attendees.attendee_type', 'conf_billing_orders.*')->where('conf_billing_orders.is_archive', 0)->first();
        
            $labels = $request->event['labels'];

            if ($order) {

                $registration_form = (object)EventSiteSettingRepository::getRegistrationForm(["event_id" => $request->event_id, 'type_id' => $order->attendee_type]);
                
                $registration_form_id = $registration_form->id;

                $waiting_list_setting = EventSiteSettingRepository::getWaitingListSetting(['event_id' => $request->event_id, 'registration_form_id' => $registration_form_id]);
                
                if($waiting_list_setting->status == 1 || $waiting_list_setting->after_stocked_to_waitinglist == 1) {
                   
                    if($order->status == 'completed' && $order->is_waitinglist == 1) {

                        $attendee = \App\Models\WaitingListAttendee::withTrashed()->where('event_id', $request->event_id)->where('attendee_id', $order->attendee_id)->first();
                        
                        if($attendee && $attendee->status == 1) {
                            
                            //timezone
                            set_event_timezone($request->event_id);
            
                            $current_time = time();
                            
                            $validity_duration = $waiting_list_setting->validity_duration * 60 * 60;

                            $expiry_time = strtotime($attendee->date_sent) + $validity_duration;

                            if ($expiry_time <= $current_time && $validity_duration != 0 && $attendee->date_sent != '0000-00-00 00:00:00') {
                                return response()->json(
                                    [
                                        'redirect' => 'waiting-link-expired',
                                        'status' => false,
                                        'error' => $labels['WAITING_LIST_LINK_EXPIRED']
                                    ], 303
                                );
                            } else {
                                return $next($request);
                            }
                            
                        } else if($attendee && in_array($attendee->status, [2, 3, 4])) {
                            return response()->json(
                                [
                                    'redirect' => 'waiting-link-expired',
                                    'status' => false,
                                    'error' => $labels['WAITING_LIST_LINK_EXPIRED']
                                ], 303
                            );
                        } else {
                            return $next($request);
                        }

                    } else {
                        return $next($request);
                    }

                } else {
                    return $next($request);
                }

            } else {
                return response()->json(
                    [
                        'redirect' => 'no-order-found',
                        'status' => false,
                        'error' => $labels['WAITING_LIST_ORDER_NOT_FOUND']
                    ], 303
                );
            }
            
        } else {
            return $next($request);
        }
    }
}
