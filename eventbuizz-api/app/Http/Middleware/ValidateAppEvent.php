<?php

namespace App\Http\Middleware;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Eventbuizz\Repositories\MyTurnListRepository;
use Closure;

class ValidateAppEvent
{
    /**
     * @param mixed $request
     * @param Closure $next
     *
     * @return [type]
     */
    public function handle($request, Closure $next)
    {
        $event = \App\Models\Event::where('url', $request->slug)->with('info', 'settings', 'attendee_settings')->first();
        if ($event) {
            $event = $event->toArray();
            $event['detail'] = readArrayKey($event, [], 'info');
            $event['settings'] = readArrayKey($event, [], 'settings');

            //labels
            $event['labels'] = eventsite_labels(['agendas', 'desktopLabels', 'generallabels', 'myturnlist', 'gdpr', 'eventsite', 'checkIn'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);

            //timezone
            set_event_timezone($event['id']);

            $request->merge([
                "event_id" => $event['id'],
                "organizer_id" => $event['organizer_id'],
                "language_id" => $event['language_id'],
            ]);

            //Gdpr
            $event['gdpr_setting'] = EventSettingRepository::getGdprSetting($request->all());
            $event['gdpr'] = EventSettingRepository::getGdprInfo($request->all());
            $event['gdpr_log_count'] = AttendeeRepository::gdprLog($request->all(), true);

            //Myturnlist setting
            $event['myturnlist_setting'] = MyTurnListRepository::getSetting($request->all());

            //Logout module
            $request->merge(["alias" => "logout"]);
            $module = EventSettingRepository::getEventModule($request->all());
            $event['labels']['LOGOUT'] = isset($module->info[0]['value']) ? $module->info[0]['value'] : 'Logout';
             
            $request->merge([
                "event" => $event
            ]);

            return $next($request);
        } else {
            return response()->json(['status' => false, 'error' => "Invalid event!"], 503);
        }
    }
}
