<?php

namespace App\Http\Middleware;

use App\Models\Organizer;
use Closure;

class AuthOrganizerToken
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

        if(isset($headers['Email']) && isset($headers['Token'])) {

            $organizer = Organizer::where('email', $headers['Email'])->first();

            if($organizer) {

                if ($organizer->user_type == 'super') {
                    $organizer_id = $organizer->id;
                } elseif ($organizer->user_type == 'demo') {
                    $organizer_id = $organizer->id;
                } else {
                    $organizer_id = $organizer->parent_id;
                }
    
                $organizer = Organizer::where('id', $organizer_id)->where('token', $headers['Token'])->where('token_expire_at', '>', date('Y-m-d H:i:s'))->first();
    
                if($organizer) {
    
                    $request->merge([
                        "organizer" => $organizer,
                        "organizer_id" => $organizer->id
                    ]);
    
                    return $next($request);
    
                }
                
            }

        } else if($request->token && $request->organizer_id) {

            $organizer = Organizer::where('id', $request->organizer_id)->where('token', $request->token)->where('token_expire_at', '>', date('Y-m-d H:i:s'))->first();
    
            if($organizer) {

                $request->merge([
                    "organizer" => $organizer,
                    "organizer_id" => $organizer->id
                ]);

                return $next($request);

            }
        }
        
        return response()->json(['error' => "Authentication Failed"], 401);
    }
}
