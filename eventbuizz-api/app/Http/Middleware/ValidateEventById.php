<?php

namespace App\Http\Middleware;

use Closure;

class ValidateEventById
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->event_id) {
            $event = \App\Models\Event::where('id', $request->event_id)->where('organizer_id', organizer_id())->first();
            if($event) {
                $request->merge([
                    "event_id" => $event->id,
                    "language_id" => $event->language_id,
                    "languages_id" => $event->language_id,
                ]);
            } else {
                return response()->json(['status' => false, 'error' => "Some error occur due to some reason."], 503);
            }
            return $next($request);
        } else {
            return response()->json(['status' => false, 'error' => "Some error occur due to some reason."], 503);
        }
    }
}
