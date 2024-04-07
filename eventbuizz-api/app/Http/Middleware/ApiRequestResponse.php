<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\JsonResponse;

use App\Jobs\ApiRequestLog;

class ApiRequestResponse
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
        // get response
        $response = $next($request);

        // if response is JSON
        if ($response instanceof JsonResponse) {

            $responseData = $response->getData();
            
            if($response->status() == 200) {

                //Set UTC Timezone
                date_default_timezone_set('UTC');
                    
                //Save api request
                $formInput['organizer_id'] = $request->organizer_id;
                $formInput['user_IP'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
                $formInput['api_key'] = $request->api_key;
                $formInput['request_date'] = date('Y-m-d H:i:s');
                $formInput['status'] = '1';

                \App\Models\OrganizerCalendarApiRequest::create($formInput);

                //Request logs
                $formInput = array();
                $formInput['organizer_id'] = $request->organizer_id;
                $formInput['api_key'] = $request->api_key;
                $formInput['request_type'] = \Request::route()->getName();
                $formInput['request_responce'] = serialize($responseData);
                $formInput['request'] = $request->all();
                
                ApiRequestLog::dispatch($formInput);
                
            }

            $response->setData($responseData);

        }

        return $response;
    }
}
