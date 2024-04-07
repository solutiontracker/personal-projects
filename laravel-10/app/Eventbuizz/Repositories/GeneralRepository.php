<?php

namespace App\Eventbuizz\Repositories;

use App\Eventbuizz\Repositories\EventRepository;

use App\Models\EventModuleTabSettings;
use App\Models\Events;
use App\Models\NativeAppSettings;
use App\Models\SpeakerListProjectorAttendeeFields;
use Illuminate\Http\Request;

class GeneralRepository extends AbstractRepository
{
    private $languages = ['en', 'da'];

    private $metadataParams = ['languages', 'country_languages', 'countries', 'country_codes', 'timezones'];

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /** copy event setting
     * @param array
     * @param int
     * @param int
     */
    public function cloneEventGeneralSetting($modules, $from_event_id, $to_event_id, $languages)
    {
        foreach ($modules as $module) {
            if ($module == 'branding') {
                //$this->copyEventBranding($from_event_id, $to_event_id);
            } elseif ($module == 'sections') {
                $this->copyEventSections($from_event_id, $to_event_id, $languages);
            } elseif ($module == 'disclaimer') {
                $this->copyEventDisclaimer($from_event_id, $to_event_id, $languages);
            } elseif ($module == 'gdpr') {
                $this->copyEventGDPR($from_event_id, $to_event_id);
            } elseif ($module == 'event_module_tab_setting') {
                $this->copyEventModuleTabSetting($from_event_id, $to_event_id);
            } elseif ($module == 'event_native_app_settings') {
                $this->copyNativeAppSetting($from_event_id, $to_event_id);
            } elseif ($module == 'speaker_list_projector_attendee_fields') {
                $this->speakerListProjectorAttendeeFields($from_event_id, $to_event_id);
            }
        }
    }

    /**
     * @param int
     * @param int
     */
    private function copyEventBranding($from_event_id, $to_event_id)
    {
        $branding_from = \App\Models\EventSetting::where('event_id', $from_event_id)
            ->where('name', '!=', 'google_analytics_email')
            ->where('name', '!=', 'google_analytics_profile_id')
            ->where('name', '!=', 'google_analytics')
            ->get()->toArray();
        foreach ($branding_from as $branding) {
            $branding_to = \App\Models\EventSetting::where('event_id', $to_event_id)->where('name', '=', $branding['name'])->first();
            if ($branding_to) {
                $branding_to->value = $branding['value'];
                $branding_to->save();
            } else {
                $branding_to = new \App\Models\EventSetting();
                $branding_to->event_id = $to_event_id;
                $branding_to->name = $branding['name'];
                $branding_to->value = $branding['value'];
                $branding_to->save();
            }
        }
    }

    /**
     * @param int
     * @param int
     */
    private function copyEventSections($from_event_id, $to_event_id, $languages)
    {
        $section_from = \App\Models\EventModuleOrder::where('event_id', '=', $from_event_id)->where('is_purchased', '=', 1)->where('type', '<>', 'backend_sub')->whereNull('deleted_at')->get()->toArray();
        foreach ($section_from as $section) {
            $section_to = \App\Models\EventModuleOrder::where('event_id', '=', $to_event_id)->where('is_purchased', '=', 1)
                ->where('type', '<>', 'backend_sub')->whereNull('deleted_at')
                ->where('alias', '=', $section['alias'])->where('type', '=', $section['type'])
                ->first();
            if ($section_to) {
                foreach ($section as $key => $value) {
                    if (in_array($key, ['id', 'event_id', 'created_at', 'updated_at'])) continue;
                    $section_to->$key = $value;
                }
                $section_to->save();
                foreach ($languages as $language) {
                    $module_info_from = \App\Models\ModuleOrderInfo::where('module_order_id', '=', $section['id'])->where('languages_id', '=', $language)->first();
                    if ($module_info_from) {
                        $module_info_to = \App\Models\ModuleOrderInfo::where('module_order_id', '=', $section_to->id)->where('languages_id', '=', $language)->first();
                        if ($module_info_to) {
                            $module_info_to->value = $module_info_from->value;
                            $module_info_to->save();
                        }
                    }
                }
            }
        }
        //native app module order updation
        $modules = \App\Models\EventModuleOrder::where('event_id', '=', $to_event_id)->get();
        foreach ($modules as $module) {
            $native_module = \App\Models\EventNativeAppModule::where('event_id', '=', $to_event_id)->where('module_alias', '=', $module->alias)->first();
            if ($native_module) {
                $native_module->status = $module->status;
                $native_module->save();
            }
        }
    }

    /**
     * @param int
     * @param int
     */
    private function copyEventModuleTabSetting($from_event_id, $to_event_id) {
        $counter = 1;
        $i=1;
        $tabsLists = ['about','program','groups','contact_info','sub_registration','description','speaker','attendees','groups','documents','polls','ask_to_speak','notes','rating','streams','program','about','groups','contact_info','documents','category','about','contact_persons','documents','contact_info','notes','surveys', 'about','contact_persons','documents','contact_info','notes','surveys'];
        $fromTabSettings = EventModuleTabSettings::where("event_id",$from_event_id)->whereIn('tab_name', $tabsLists)->get();
        foreach ($fromTabSettings as $fromTabSetting){
            $eventTabSetting = EventModuleTabSettings::where('module', $fromTabSetting->module)->where('tab_name',$fromTabSetting->tab_name)->where('event_id', $to_event_id)->first();
            if($eventTabSetting) {
                $eventTabSetting->event_id = $to_event_id;
                $eventTabSetting->tab_name = $fromTabSetting->tab_name;
                $eventTabSetting->status = $fromTabSetting->status;
                $eventTabSetting->sort_order = $fromTabSetting->sort_order;
                $eventTabSetting->module = $fromTabSetting->module;
                $eventTabSetting->save();
            } else {
                $eventTabSetting =new EventModuleTabSettings();
                $eventTabSetting->event_id = $to_event_id;
                $eventTabSetting->tab_name = $fromTabSetting->tab_name;
                $eventTabSetting->status = '1';
                $eventTabSetting->sort_order = $i;
                $eventTabSetting->module =  $fromTabSetting->module;
                $eventTabSetting->save();
            }
            if(in_array($counter, [5,15,21,27,33])){
                $i = 0;
            }
            $counter++;
            $i++;
        }
    }

    /**
     * @param int
     * @param int
     */
    private function copyNativeAppSetting($from_event_id, $to_event_id) {
        $nativeAppSettings= NativeAppSettings::where("event_id",$from_event_id)->first();
        $toNativeAppSettings= NativeAppSettings::where("event_id",$to_event_id)->first();
        if($toNativeAppSettings) {
            $toNativeAppSettings->event_location = $nativeAppSettings->event_location;
            $toNativeAppSettings->event_date = $nativeAppSettings->event_date;
            $toNativeAppSettings->save();
        } else {
            $native_app_settings['event_id'] = $to_event_id;
            $native_app_settings['event_location'] = 1;
            $native_app_settings['event_date'] = 1;
            NativeAppSettings::create($native_app_settings);
        }
    }

    /**
     * @param int
     * @param int
     */
    public function speakerListProjectorAttendeeFields($from_event_id, $to_event_id) {
        //add turn list attendee fields
        $fields = ['title','company_name','delegate_number','department','network_group'];
        $speakerListProjectorAttendeeFields =SpeakerListProjectorAttendeeFields::where('event_id', $from_event_id)->whereIn('fields_name',$fields)->get();
        $i = 0;
        foreach ($speakerListProjectorAttendeeFields as $speakerFields) {
            $speakerListProjectorAttendeeField= SpeakerListProjectorAttendeeFields::where('event_id', $to_event_id)->where('fields_name',$speakerFields->fields_name)->first();
            if($speakerListProjectorAttendeeField) {
                $speakerListProjectorAttendeeField->event_id = $to_event_id;
                $speakerListProjectorAttendeeField->fields_name = $speakerFields->fields_name;
                $speakerListProjectorAttendeeField->sort_order = $speakerFields->sort_order;
                $speakerListProjectorAttendeeField->save();
            } else {
                $speakerListProjectorAttendeeField =new SpeakerListProjectorAttendeeFields();
                $speakerListProjectorAttendeeField->event_id = $to_event_id;
                $speakerListProjectorAttendeeField->fields_name = $speakerFields->fields_name;
                $speakerListProjectorAttendeeField->sort_order = $i;
                $speakerListProjectorAttendeeField->save();
            }
            $i++;
        }
    }

    /**
     * @param int
     * @param int
     */
    private function copyEventDisclaimer($from_event_id, $to_event_id, $languages)
    {
        foreach ($languages as $language) {
            $disclaimer_from = \App\Models\EventDisclaimer::where('event_id', $from_event_id)->where('languages_id', '=', $language)->first();
            $disclaimer_to = \App\Models\EventDisclaimer::where('event_id', $to_event_id)->where('languages_id', '=', $language)->first();
            if ($disclaimer_to) {
                $disclaimer_to->disclaimer = $disclaimer_from->disclaimer;
                $disclaimer_to->save();
            } else {
                $disclaimer_to = new \App\Models\EventDisclaimer();
                $disclaimer_to->event_id = $to_event_id;
                $disclaimer_to->languages_id = $language;
                $disclaimer_to->disclaimer = empty($disclaimer_from->disclaimer) ? '' : $disclaimer_from->disclaimer;
                $disclaimer_to->save();
            }
        }
    }

    /**
     * @param int
     * @param int
     */
    private function copyEventGDPR($from_event_id, $to_event_id)
    {
        //GDPR Description
        $gdpr_from = \App\Models\EventGdpr::where('event_id', $from_event_id)->whereNull('deleted_at')->first();
        if ($gdpr_from) {
            $gdpr_to = \App\Models\EventGdpr::where('event_id', $to_event_id)->whereNull('deleted_at')->first();
            if ($gdpr_to) {
                $gdpr_to->subject = $gdpr_from->subject;
                $gdpr_to->inline_text = $gdpr_from->inline_text;
                $gdpr_to->description = $gdpr_from->description;
                $gdpr_to->save();
            } else {
                $gdpr_to = new \App\Models\EventGdpr();
                $gdpr_to->subject = $gdpr_from->subject;
                $gdpr_to->inline_text = $gdpr_from->inline_text;
                $gdpr_to->description = $gdpr_from->description;
                $gdpr_to->save();
            }
        } else {
            $gdpr_to = new \App\Models\EventGdpr();
            $gdpr_to->event_id = $to_event_id;
            $gdpr_to->subject = 'General Data Protection Regulation (GDPR';
            $gdpr_to->inline_text = 'I agree to the GDPR. {detail_link} Read more.{/detail_link}';
            $gdpr_to->description = 'If you do not accept these terms only your "First Name" and "Last Name" will appear in following modules: <ul><li>Attendee</li><li>Chat</li><li>Survey</li><li>Polls</li><li>Q&amp;A</li><li>Social Wall</li><li>Groups</li><li>Speaker list</li><li>Sponsor/Exhibitor will not be able to scan your name badges</li></ul>&nbsp;<br /><strong>Invisible activated</strong><br />If you do not accept these terms then you will not be visible in the app and the functionality will be limited to display only<br />&nbsp;<br />&nbsp;';
            $gdpr_to->save();
        }

        //GDPR settings
        $gdpr_settings_from = \App\Models\EventGdprSetting::where('event_id', $from_event_id)->whereNull('deleted_at')->first();
        if ($gdpr_settings_from) {
            $gdpr_settings_to = \App\Models\EventGdprSetting::where('event_id', $to_event_id)->whereNull('deleted_at')->first();
            if ($gdpr_settings_to) {
                $gdpr_settings_to->enable_gdpr = $gdpr_settings_from->enable_gdpr;
                $gdpr_settings_to->attendee_invisible = $gdpr_settings_from->attendee_invisible;
                $gdpr_settings_to->save();
            } else {
                $gdpr_settings_to = new \App\Models\EventGdprSetting();
                $gdpr_settings_to->event_id = $to_event_id;
                $gdpr_settings_to->enable_gdpr = $gdpr_settings_from->enable_gdpr;
                $gdpr_settings_to->attendee_invisible = $gdpr_settings_from->attendee_invisible;
                $gdpr_settings_to->save();
            }
        } else {
            $gdpr_settings_to = new \App\Models\EventGdprSetting();
            $gdpr_settings_to->event_id = $to_event_id;
            $gdpr_settings_to->enable_gdpr = '0';
            $gdpr_settings_to->attendee_invisible = '0';
            $gdpr_settings_to->save();
        }
    }

    /*
     * get generic interface labels for wizard site
     * default return english labels
     * @param $lang
     * @return only wizard array based on requested language
     */
    public function getGenericInterfaceLabels($lang = 'en')
    {
        if (in_array($lang, $this->languages)) {
            app()->setLocale($lang);
        } else {
            app()->setLocale('en');
        }
        return __('wizard');
    }
    /*
     * get metadata of languages, countries, country_codes, timezones
     * @param pass a single param or multi params on same request
     * @return mixed
     * example of param,
     * 1. languages
     * 2. countries,country_codes
     * 3. empty (return all nodes)
     */
    public function getMetadata($param = 'all', $event_id = NULL)
    {
        $param = explode(',', $param);
        $commonMetaParams = array_intersect($this->metadataParams, $param);
        $data = [];
        if (count($commonMetaParams) > 0) {
            foreach ($commonMetaParams as $param) {
                if ($param == 'languages') {
                    $data['languages'] = get_all_languages();
                } else if ($param == 'country_languages') {
                    $data['languages'] = get_all_countries_languages();
                } elseif ($param == 'countries') {
                    $data['countries'] = get_all_countries();
                } elseif ($param == 'country_codes') {
                    $data['country_codes'] = get_all_country_codes();
                } elseif ($param == 'timezones') {
                    $data['timezones'] = get_all_timezones();
                } elseif ($param == 'date_formats') {
                    $data['timezones'] = get_date_formats();
                }
            }
        } else {
            $data['languages'] = get_all_languages();
            $data['countries'] = get_all_countries();
            $data['country_codes'] = get_all_country_codes();
            $data['timezones'] = get_all_timezones();
            $data['date_formats'] = get_date_formats();
        }

        //Fetch event country
        if ($event_id) {
            $data['event_country_code'] = EventRepository::getEventCallingCode(['event_id' => $event_id]);
        }

        return $data;
    }
}
