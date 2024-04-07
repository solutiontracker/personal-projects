<?php

    namespace App\Http\Middleware;

    use App\Models\Event;
    use Closure;

    class ValidateEventUrl
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

            $slug = $request->slug;

            $event = \App\Models\Event::where('url', $slug)->with('info', 'settings', 'attendee_settings', 'speaker_settings', 'sponsor_settings', 'exhibitor_settings', 'gdprSettings', 'agenda_settings')->first();

            if($event) {

                $event = $event->toArray();

                $event['detail'] = readArrayKey($event, [], 'info');

                $event['settings'] = readArrayKey($event, [], 'settings');

                //labels
                $event['labels'] = eventsite_labels(['eventsite', 'generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                
                //timezone
                set_event_timezone($event['id']);

                $request->merge([
                    "event" => $event,
                    "event_id" => $event['id'],
                    "organizer_id" => $event['organizer_id'],
                    "language_id" => $event['language_id'],
                ]);

                return $next($request);

            } else {
                return response()->json(['status' => false, 'error' => "Some error occur due to some reason."], 503);
            }
        }
    }