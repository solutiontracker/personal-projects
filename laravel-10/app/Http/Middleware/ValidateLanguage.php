<?php

namespace App\Http\Middleware;

use Closure;

class ValidateLanguage
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
        if (isset($headers['Language-Id']) && $headers['Language-Id'] && $headers['Language-Id'] != "null") {
            $request->merge([
                "language_id" => $headers['Language-Id'],
                "languages_id" => $headers['Language-Id'],
            ]);
            return $next($request);
        } else {
            return response()->json(['status' => false, 'error' => "Some error occur due to some reason."], 503);
        }
    }
}
