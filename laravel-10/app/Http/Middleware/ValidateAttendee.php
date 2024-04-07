<?php

namespace App\Http\Middleware;

use Closure;

class ValidateAttendee
{
    /**
     * @param mixed $request
     * @param Closure $next
     *
     * @return [type]
     */
    public function handle($request, Closure $next)
    {
        $event = $request->event;
        $attendee_detail = array();
        $attendee = $request->user();
        if ($attendee) {
            $info = $attendee->info;
            $attendee_detail = $attendee->toArray();
            if ($info) {
                $attendee_detail['info'] = $info->toArray();
                $info = readArrayKey($attendee_detail, [], 'info');
                $attendee_detail['detail'] = $info;
            }

            //Event attendee
            $attendee_detail['event_attendee'] = \App\Models\EventAttendee::where('event_id', $event['id'])->where('attendee_id', $attendee_detail['id'])->orderBy('id', 'Desc')->first();
            if (!$attendee_detail['event_attendee']) {
                return response()->json(['status' => false, 'error' => "Unauthorized!"], 401);
            } else {
                $request->merge([
                    "attendee_detail" => $attendee_detail,
                    "attendee_id" => $attendee_detail['id'],
                ]);

                return $next($request);
            }
        } else {
            return response()->json(['status' => false, 'error' => "please logged in!"], 503);
        }
    }
}
