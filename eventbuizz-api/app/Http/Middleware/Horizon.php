<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Horizon
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($_SERVER['HTTP_X_FORWARDED_FOR'], explode(',', config('app.horizon_allowed_ips')))) {
            abort(403);
        }
        
        return $next($request);
    }
}