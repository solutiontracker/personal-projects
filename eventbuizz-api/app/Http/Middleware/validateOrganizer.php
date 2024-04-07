<?php

namespace App\Http\Middleware;

use App\Models\Organizer;
use Closure;

class validateOrganizer
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

        if(isset($headers['Authorization']) || isset($headers['authorization'])) {
            $api_key = $headers['Authorization'] ? $headers['Authorization'] : $headers['authorization'];
            $organizer = Organizer::where('api_key', $api_key)->first();
            if($organizer) {
                $allow_api = \App\Models\AssignAdditionalFeature::where('organizer_id', $organizer->id)->where('alias', 'allow_api')->orderBy('id', 'desc')->first();

                // Check API Key Expiry.
                if($allow_api->status == 0) {
                    return response()->json(['error' => 'This API Key has been expired'], 402);
                }

                // Check API Limit
                if($this->isAPILimitExceed($organizer->id)) {
                    return response()->json(['error' => 'Requests limit exceed'], 402);
                }

            } else {
                return response()->json(['error' => 'Invalid API Key Request'], 402);
            }
        } else {
            return response()->json(['error' => 'Invalid API Key Request'], 402);
        }

        $request->merge(["api_key" => $api_key]);

        return $next($request);  
    }
    
    /**
     * isAPILimitExceed
     *
     * @param  mixed $id
     * @return void
     */
    function isAPILimitExceed($id) {
        $current_time = date('Y-m-d H:i:s');
        $previous_time = date('Y-m-d H:i:s', strtotime('-1 minutes'));
        $count = \App\Models\OrganizerCalendarApiRequest::where('organizer_id', '=', $id)->where('request_date', '>=', $previous_time)->where('request_date', '<=', $current_time)->whereNull('deleted_at')->orderBy('id', 'DESC')->count();
        $api_access_limit = config('setting.organizer_api_rate_limit');
        if($count >= $api_access_limit) {
            return true;
        }
        return false;
    }
}
