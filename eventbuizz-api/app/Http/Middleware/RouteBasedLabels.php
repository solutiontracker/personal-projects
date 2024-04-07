<?php

    namespace App\Http\Middleware;

    use App\Models\Event;
    use Closure;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Str;
    class RouteBasedLabels
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
                 
           $response = $next($request);

           $slug = $request->slug;

           $event = \App\Models\Event::where('url', $slug)->first();

            if ($event) {
                if (Str::contains($request->path(), "api/v2/event/" . $slug . "/speakers")) {
                    $labels = eventsite_labels(['attendees','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/attendees") || Str::contains($request->path() , "api/v2/event/" . $slug . "/attendee")) {
                    $labels = eventsite_labels(['attendees','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/sponsors")) {
                    $labels = eventsite_labels(['sponsors','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/sponsors") || Str::contains($request->path() , "api/v2/event/" . $slug . "/sponsors-listing") || Str::contains($request->path() , "api/v2/event/" . $slug . "/sponsor-detail")) {
                    $labels = eventsite_labels(['sponsors','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/exhibitors") || Str::contains($request->path() , "api/v2/event/" . $slug . "/exhibitors-listing") || Str::contains($request->path() , "api/v2/event/" . $slug . "/exhibitor-detail")) {
                    $labels = eventsite_labels(['exhibitors','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/photos") || Str::contains($request->path() , "api/v2/event/" . $slug . "/video")) {
                    $labels = eventsite_labels(['gallery','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/programs")  || Str::contains($request->path() , "api/v2/event/" . $slug . "program/search")) {
                    $labels = eventsite_labels(['agendas','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/alerts")) {
                    $labels = eventsite_labels(['alerts','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/news")) {
                    $labels = eventsite_labels(['news','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/documents")) {
                    $labels = eventsite_labels(['ddirectory','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/sub-registration") || Str::contains($request->path() , "api/v2/event/" . $slug . "/my-sub-registration")) {
                    $labels = eventsite_labels(['subregistration','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/survey") || Str::contains($request->path() , "api/v2/event/" . $slug . "/save-survey")) {
                    $labels = eventsite_labels(['survey','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }
                elseif (Str::contains($request->path() , "api/v2/event/" . $slug . "/network-interest")) {
                    $labels = eventsite_labels(['mykeywords','generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
                }


                if ($response instanceof JsonResponse) {
                    $current_data = $response->getData();
                    $current_data->labels = $labels;
                    $response->setData($current_data);
                }

                return $response;

            } else {
                return response()->json(['status' => false, 'error' => "Some error occur due to some reason."], 503);
            }

        }
    }