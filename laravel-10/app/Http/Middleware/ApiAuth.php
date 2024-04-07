<?php

namespace App\Http\Middleware;

use App\Models\Organizer;
use Closure;

class ApiAuth
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
        if (isset($headers['Authorization']) || isset($headers['authorization'])) {
            $api_key = $headers['Authorization'] ? $headers['Authorization'] : $headers['authorization'];
            $organizer = Organizer::where('api_key', $api_key)->first();
            if ($organizer) {
                //Merge organizer into request
                $request->merge([
                    "organizer" => $organizer,
                    "organizer_id" => $organizer->id,
                    "api_key" => $organizer->api_key
                ]);

                $feature = \App\Models\AssignAdditionalFeature::where('organizer_id', $organizer->id)->where('alias', 'allow_api')->orderBy('id', 'desc')->first();

                // Check API Key Expiry.
                if ($feature->status == 0) {
                    return response()->json(['error' => "This API Key has been expired"], 401);
                }

                // Check API Limit
                if ($this->requestLimit($organizer->id)) {
                    return response()->json(['error' => "Requests limit exceed"], 401);
                }

                // Validate Event Id.
                if (isset($headers['Event-Id']) && !empty($headers['Event-Id'])) {
                    $event_id = $headers['Event-Id'];
                    if (!$this->validEvent($organizer->id, $event_id)) {
                        return response()->json(['error' => "Invalid event ID Request"], 401);
                    }
                }

            } else {
                return response()->json(['error' => "Invalid API Key Request"], 401);
            }
        } else {
            return response()->json(['error' => "Invalid API Key Request"], 401);
        }

        return $next($request);
    }

    /**
     * @param mixed $id
     *
     * @return [type]
     */
    private function requestLimit($id)
    {
        $current_time = date('Y-m-d H:i:s');

        $privouse_time = date('Y-m-d H:i:s', strtotime('-1 minutes'));

        $count = \App\Models\OrganizerCalendarApiRequest::where('organizer_id', '=', $id)->where('request_date', '>=', $privouse_time)->where('request_date', '<=', $current_time)->whereNull('deleted_at')->orderBy('id', 'DESC')->count();
        $api_access_limit = 10;

        if ($count >= $api_access_limit) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $organizer_id
     * @param mixed $event_id
     *
     * @return [type]
     */
    private function validEvent($organizer_id, $event_id)
    {
        $event = \App\Models\Event::where('organizer_id', $organizer_id)->where('id', $event_id)->first();

        if ($event) {
            return true;
        }

        return false;
    }
}
