<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;

class BlockIpAddressMiddleware
{
    /**
     * @var string[]
     */
    public $allowIps = [
        '39.61.51.233'
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($_SERVER['HTTP_X_FORWARDED_FOR'], $this->allowIps)) {
            abort(403, "You are restricted to access the site.");
        }

        return $next($request);
    }
}