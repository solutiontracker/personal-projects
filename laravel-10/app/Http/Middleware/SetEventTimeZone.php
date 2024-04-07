<?php

namespace App\Http\Middleware;

use Closure;

class SetEventTimeZone
{
    /**
     * @param mixed $request
     * @param Closure $next
     *
     * @return [type]
     */
    public function handle($request, Closure $next)
    {
        if ($request->event_id) {
            set_event_timezone($request->event_id);
            return $next($request);
        } else {
            return response()->json(['status' => false, 'error' => "Invalid event!"], 503);
        }
    }
}
