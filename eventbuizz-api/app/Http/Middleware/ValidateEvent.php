<?php

namespace App\Http\Middleware;

use Closure;

class ValidateEvent
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
        $headers = getallheaders();

        if ((isset($headers['Event-Id']) && $headers['Event-Id'] &&  $headers['Event-Id'] != "null")) {

            $language_id = (isset($headers['Language-Id']) && $headers['Language-Id'] && $headers['Language-Id'] != "null" ? $headers['Language-Id'] : 1);

            $event = \App\Models\Event::where('id', $headers['Event-Id'])->where('organizer_id', organizer_id())->first();

            if($event) {

                $event = $event->toArray();

                //labels
                $event['labels'] = eventsite_labels(['agendas', 'desktopLabels', 'generallabels', 'myturnlist', 'gdpr', 'eventsite', 'checkIn'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);

                $request->merge([
                    "event_id" => $headers['Event-Id'],
                    "event" => $event,
                    "language_id" => $language_id,
                    "languages_id" => $language_id,
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