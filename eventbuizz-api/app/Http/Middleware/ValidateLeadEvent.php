<?php

namespace App\Http\Middleware;

use Closure;

class ValidateLeadEvent
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
        $request->headers->set('Accept', 'application/json');
        $event_id = $request->route('event_id') ? (int)$request->route('event_id') : ($request['event_id'] ? (int)$request['event_id'] : null);
        $event =
        \App\Models\Event::select([
            'id',
            'organizer_name',
            'organizer_id',
            'name', 
            'url',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
            'status',
            'timezone_id',
            'language_id',
            'country_id'
        ])->where('id', $event_id)->whereNull('deleted_at')->first();
        if (!is_null($event)) {
            if ((int) $event['status'] === 0) {
                return response()->json([
                    'status' => 0,
                    'response_type' => LEAD_EVENT_INACTIVE,
                    'message' => 'Event not Active.'
                ], 413);
            }
        } else {
            return response()->json([
                'status' => 0,
                'response_type' => LEAD_EVENT_NOT_FOUND,
                'message' => 'Event not found.'
            ], 413);
        }

        $request['event'] = $event;
        $leadSettingsLeadUserMode = \App\Models\LeadSetting::select('lead_user_without_contact_person', 'login_with_auth_code')->where("event_id", $event_id)->first();
        $moduleStatus = \App\Models\EventModuleOrder::select(['status'])->where('event_id', $event_id)->where('alias', 'leadsmanagment')->first();
        
        if($moduleStatus->status === 0 || $leadSettingsLeadUserMode['lead_user_without_contact_person'] === -1){
            return response()->json([
                'status' => 0, 
                'response_type' => INVALID_USER_MODE, 
                'message' => "Lead module is not activated. Please contact the administrator" 
            ], 413);
        } 
            
        
        $mode =  $leadSettingsLeadUserMode['lead_user_without_contact_person'] === 1 && $moduleStatus->status === 1 ? "lead_user" : "contact_person" ;
        if($request['mode'] && $request['mode'] !== $mode){
            return response()->json([
                'status' => 0, 
                'response_type' => INVALID_USER_MODE, 
                'message' => "Server setting has been changed please export your leads records and logout" 
            ], 413);
        }
        $request['mode'] = $mode;
        $request['login_with_auth_code'] = $leadSettingsLeadUserMode['login_with_auth_code'];
        return $next($request);
    }
}
