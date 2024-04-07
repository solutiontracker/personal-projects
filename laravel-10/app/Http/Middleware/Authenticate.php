<?php

namespace App\Http\Middleware;

use Closure;

class Authenticate
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
        $user = $request->user();
        if ($user) {
            $user->tokens()->where('expires_at', '>=', \Carbon\Carbon::now())->where('revoked', 0)->orderBy('created_at', 'DESC')->get()->map(function ($token) {
                if($token) {
                    $token->last_access_at = \Carbon\Carbon::now();
                    $token->save();
                }
            });
        }
        return $next($request);
    }
}
