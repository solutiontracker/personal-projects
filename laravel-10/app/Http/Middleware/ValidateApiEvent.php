<?php

namespace App\Http\Middleware;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use Closure;

class ValidateApiEvent
{
    /**
     * @param mixed $request
     * @param Closure $next
     *
     * @return [type]
     */
    public function handle($request, Closure $next)
    {
        $event = \App\Models\Event::where('id', $request->event_id)->with('info', 'settings', 'attendee_settings')->first();
        
        if ($event) {
            $event = $event->toArray();
            $event['detail'] = readArrayKey($event, [], 'info');
            $event['settings'] = readArrayKey($event, [], 'settings');

            //labels
            $event['labels'] = eventsite_labels(['eventsite', 'exportlabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);

            //timezone
            set_event_timezone($event['id']);

            $request->merge([
                "event_id" => $event['id'],
                "organizer_id" => $event['organizer_id'],
                "language_id" => $event['language_id'],
            ]);

            //eventsite setting
            $event['eventsite_setting'] = EventSiteSettingRepository::getSetting($request->all());

            //payment setting
            $event['payment_setting'] = EventSiteSettingRepository::getPaymentSetting($request->all());

            //event waitinglist setting
            $event['waiting_list_setting'] = EventSiteSettingRepository::getWaitingListSetting($request->all());

            //event attendee setting
            $event['attendee_setting'] = AttendeeRepository::getAttendeeSetting($request->event_id);
             
            $request->merge([
                "event" => $event
            ]);

            return $next($request);
        } else {
            return response()->json(['status' => false, 'error' => "Invalid event!"], 503);
        }
    }
}
