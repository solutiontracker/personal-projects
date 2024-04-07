<?php

namespace App\Http\Middleware;

use Closure;

class ValidateInterfaceLanguage
{
    private $_languages = ['en', 'da', 'no', 'de', 'lt', 'fi', 'se', 'nl','be'];
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
        if (isset($headers['Interface-Language-Id']) && $headers['Interface-Language-Id'] && $headers['Interface-Language-Id'] != "null") {
            \App::setLocale($this->_languages[$headers['Interface-Language-Id'] - 1]);
            $request->merge([
                "interface_language_id" => $headers['Interface-Language-Id']
            ]);
            return $next($request);
        } else {
            return $next($request);
            return response()->json(['status' => false, 'error' => "Some error occur due to some reason."], 503);
        }
    }
}