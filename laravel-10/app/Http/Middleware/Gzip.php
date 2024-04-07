<?php

namespace App\Http\Middleware;

use Closure;

class Gzip
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
        
        $response =  $next($request);
        $content = $response->content();
        if($request['mode']){
            $content = json_decode($content);
            if(is_array($content)){
                if($content['data']){
                    $content['data']['mode'] = $request['mode'];
                }else{
                    $content['mode'] = $request['mode'];
                }
            }else{
                if($content->data){
                    $content->data->mode = $request['mode'];
                }else{
                    $content->mode = $request['mode'];
                }
            }
            $content = json_encode($content);
        }
        if(!$request['debug']){
            $data = gzcompress($content, 9);
            return response($data)->withHeaders([
                    'Access-Control-Allow-Origin' => '*',         
                    'Content-type' => 'application/json; charset=utf-8',
                    'Content-Length'=> strlen($data),
                    'Content-Encoding' => 'compress, gzip'
            ]);

        }
        $data = gzencode($content, 9);
        return response($data)->withHeaders([           
            'Content-type' => 'application/json; charset=utf-8',
            'Content-Length'=> strlen($data),
            'Content-Encoding' => 'gzip'
        ]);
    }
}
