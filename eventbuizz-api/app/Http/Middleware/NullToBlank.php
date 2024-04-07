<?php

namespace App\Http\Middleware;

use Closure;

class NullToBlank
{
    /**
     * @param mixed $request
     * @param Closure $next
     *
     * @return [type]
     */
    public function handle($request, Closure $next)
    {
        $output = $next($request);

        if ($output) {
            $modelAsArray =json_decode($output->getContent(), true);
            if (json_last_error() != JSON_ERROR_NONE) {
                return $output;
            }else{
                if (is_array($modelAsArray)) {
                    array_walk_recursive($modelAsArray, function (&$item, $key) {
                        $item = $item === null ? '' : $item;
                    });
                    return response($modelAsArray, $output->getStatusCode());
                } else {
                    return $output;
                }
            }
        } else {
            return $output;
        }
    }
}
