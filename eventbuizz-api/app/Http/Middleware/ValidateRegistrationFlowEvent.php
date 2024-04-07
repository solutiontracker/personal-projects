<?php

namespace App\Http\Middleware;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Eventbuizz\Repositories\EventSiteRepository;
use App\Eventbuizz\Repositories\MyTurnListRepository;
use App\Http\Controllers\RegistrationFlow\Requests\AttendeeRequest;
use Closure;
use Illuminate\Support\Str;

class ValidateRegistrationFlowEvent
{

    private $_languages = ['en', 'da', 'no', 'de', 'lt', 'fi', 'se', 'nl','be'];
    
    /**
     * @param mixed $request
     * @param Closure $next
     *
     * @return [type]
     */
    public function handle($request, Closure $next)
    {
        $event = \App\Models\Event::where('url', $request->slug)->with('info', 'settings', 'attendee_settings', 'timezone')->first();
        
        if ($event) {
            
            $request->merge([
                'utc_date_time' => \Carbon\Carbon::now()
            ]);
            
            //Organizer
            $organizer  = $event->organizer;

            $event = $event->toArray();

            $event['registration_flow_theme'] = EventSiteRepository::getRegistrationFormTheme($event['registration_flow_theme_id'], $event['id']);
            
            $event['organizer']['name'] = $organizer->first_name.' '.$organizer->last_name;

            $event['organizer']['country_name'] = getCountryName($organizer->country_id);

            $event['organizer']['city'] = $organizer->city;

            $event['organizer']['address'] = $organizer->address;

            $event['organizer']['house_number'] = $organizer->house_number;

            $event['organizer']['zip_code'] = $organizer->zip_code;

            $event['organizer']['phone'] = $organizer->phone;

            $event['organizer']['email'] = $organizer->email;

            $event['detail'] = readArrayKey($event, [], 'info');

            $event['settings'] = readArrayKey($event, [], 'settings');

            //labels
            $event['labels'] = eventsite_labels(['eventsite', 'exportlabels', 'generallabels', 'gdpr'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
            
            $event['interface_labels'] = trans('registration-flow');

            //timezone
            set_event_timezone($event['id']);

            $request->merge([
                "event_id" => $event['id'],
                "organizer_id" => $event['organizer_id'],
                "language_id" => $event['language_id'],
            ]);

            //eventsite setting
            $event['eventsite_setting'] = EventSiteSettingRepository::getSetting($request->all());

            $event['eventsite_registration_forms'] = EventSiteSettingRepository::getRegistrationForms($request->all());

            //payment setting
            $event['payment_setting'] = EventSiteSettingRepository::getPaymentSetting($request->all());

            //event waitinglist setting
            $event['waiting_list_setting'] = EventSiteSettingRepository::getWaitingListSetting($request->all());

            //event attendee setting
            $event['attendee_setting'] = AttendeeRepository::getAttendeeSetting($request->event_id);

            //Event disclaimer setting
            $event['event_disclaimer_setting'] = EventSettingRepository::getDisclaimerSetting($request->all());

            //Map
            $event['map'] = \App\Models\EventMap::where('event_id', $event['id'])->with(['info'])->first();
            $event['map']['detail'] = readArrayKey($event['map'], [], 'info');

            //Eventsite description
            $event_description = \App\Models\EventSiteDescription::where('event_id', $event['id'])->with(['info' => function ($query) use($event) {
                return $query->where('languages_id', $event['language_id']);
            }])->first();

            //Payment cards
            $payment_cards_types = EventSiteSettingRepository::getPaymentCards($request->all());

            if($payment_cards_types->card_type) {

                $payment_cards_types = collect((array)unserialize($payment_cards_types->card_type));
    
                $payment_cards_types = $payment_cards_types->toArray();

                $payment_cards = array();

                foreach($payment_cards_types as $payment_cards_type) {
                    if(in_array($payment_cards_type, ['V-DK', 'VISA(SE)'])) {
                        $payment_cards[] = 'VISA';
                    } else if(in_array($payment_cards_type, ['MC(DK)', 'MC(SE)', 'MC'])) {
                        $payment_cards[] = 'Master';
                    } else if(in_array($payment_cards_type, ['DIN(DK)', 'DIN'])) {
                        $payment_cards[] = 'Dinner_club';
                    } else if(in_array($payment_cards_type, ['AMEX(DK)', 'AMEX'])) {
                        $payment_cards[] = 'American_express';
                    } else if(in_array($payment_cards_type, ['MTRO(DK)', 'MTRO'])) {
                        $payment_cards[] = 'Maestro';
                    } else if(in_array($payment_cards_type, ['ELEC'])) {
                        $payment_cards[] = 'ELEC';
                    } else {
                        $payment_cards[] = $payment_cards_type;
                    }
                }
                
            } else {
                $payment_cards = array();
            }

            $event['payment_cards'] = array_values(array_unique($payment_cards));

            $event_description['detail'] = readArrayKey($event_description, [], 'info');

            $event['event_description'] = $event_description;
            
            $event['social_media'] = EventSiteRepository::getSocialShare($request->all());

            $event['gdpr_setting'] = EventSettingRepository::getGdprSetting($request->all());
            
            $request->merge([
                "event" => $event
            ]);

            // SetLocal
            \App::setLocale($this->_languages[$event['language_id'] - 1]);
            
            return $next($request);
            
        } else {
            return response()->json(['status' => false, 'error' => "Invalid event!"], 503);
        }
    }
}
