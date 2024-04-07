<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class VirtualAppJsonModified
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

            $current_data = $response->getData();

            $current_data->event = $request->event;

            if($request->attendee_detail) {
                $current_data->attendee_detail = $request->attendee_detail;
            }

            $response->setData($current_data);

        }

        return $response;
    }
}
