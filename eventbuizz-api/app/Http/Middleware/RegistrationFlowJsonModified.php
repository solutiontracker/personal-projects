<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\JsonResponse;

class RegistrationFlowJsonModified
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

            $response->setData($current_data);

        }

        return $response;
    }
}
