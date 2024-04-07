<?php

namespace App\Http\Middleware;

use Closure;

class RequestLog
{
    /**
     * @param mixed $request
     * @param Closure $next
     *
     * @return [type]
     */
    public function handle($request, Closure $next)
    {
        \App\Models\WebServiceRequestLog::create([
            "data" => serialize($request->all()),
            "date" => \Carbon\Carbon::now(),
            "endpoint" => \Request::url(),
        ]);

        return $next($request);
    }
}
