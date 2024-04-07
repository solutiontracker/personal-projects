<?php

namespace App\Eventbuizz\Repositories;

use App\Models\EventDisclaimer;
use App\Models\EventGdpr;
use App\Models\EventGdprLog;
use App\Eventbuizz\Repositories\MediaLibraryRepository;
use App\Models\EventSetting;

class EventSettingRepository extends AbstractRepository
{
    protected $mediaLibraryRepository;

    public function __construct(MediaLibraryRepository $mediaLibraryRepository)
    {
        $this->mediaLibraryRepository = $mediaLibraryRepository;
    }

    public function getDisclaimer($event_id, $language_id)
    {
        $eventDisclaimer = EventDisclaimer::where('event_id', $event_id)
            ->where('languages_id', $language_id)->first();
        return $eventDisclaimer ? $eventDisclaimer->disclaimer : '';
    }

    /*
     * @Required keys in request event_id,language_id, disclaimer
     */
    public function updateDisclaimer($formData)
    {
        $eventDisclaimer = EventDisclaimer::where('event_id', $formData['event_id'])
            ->where('languages_id', $formData['language_id'])->first();
        if ($eventDisclaimer) {
            $eventDisclaimer->disclaimer = ($formData['disclaimer'] ? $formData['disclaimer'] : ' ');
            $eventDisclaimer->save();
        } else {
            EventDisclaimer::create($formData);
        }
    }

    public function getGdprDisclaimer($event_id)
    {
        $gdprDisclaimer = EventGdpr::where('event_id', $event_id)->first();
        return $gdprDisclaimer ? [
            'subject' => $gdprDisclaimer->subject,
            'inline_text' => $gdprDisclaimer->inline_text,
            'description' => $gdprDisclaimer->description
        ] : ['subject' => '', 'inline_text' => '', 'description' => ''];
    }

    public function updateGdprDisclaimer($formData)
    {
        $gdprDisclaimer = EventGdpr::where('event_id', $formData['event_id'])->first();
        if ($gdprDisclaimer) {
            $gdprDisclaimer->subject = ($formData['subject'] ? $formData['subject'] : '');
            $gdprDisclaimer->inline_text = ($formData['inline_text'] ? $formData['inline_text'] : '');
            $gdprDisclaimer->description = ($formData['description'] ? $formData['description'] : '');
            $gdprDisclaimer->save();
        } else {
            $gdprDisclaimer = EventGdpr::create($formData);
        }

        /*
         * gdpr logs
         */
        $dataLog = [];
        $dataLog['gdpr_id'] = $gdprDisclaimer->id;
        $dataLog['event_id'] = $gdprDisclaimer->event_id;
        $dataLog['subject'] = $gdprDisclaimer->subject;
        $dataLog['inline_text'] = $gdprDisclaimer->inline_text;
        $dataLog['description'] = $gdprDisclaimer->description;

        EventGdprLog::create($dataLog);
    }

    /**
     * Event app/registration site modules
     * @param array
     */
    static public function modules($formData, $alias, $type = "web-app-modules")
    {
        $survey_label = eventsite_labels('polls', $formData, 'SURVAY');

        $web_app_modules = \App\Models\EventModuleOrder::where('event_id', $formData['event_id'])
            ->where('is_purchased', 1)
            ->where('type', 'backend')
            ->whereIn('alias', $alias)
            ->with([
                'info' => function ($query) use ($formData) {
                    return $query->where('languages_id', $formData['language_id']);
                }
            ])
            ->orderBy('sort_order', 'asc')
            ->get();

        $response = array();

        foreach ($web_app_modules as $key => $val) {
            $response[$key]['id'] = $val['id'];
            $response[$key]['alias'] = $val['alias'];
            $response[$key]['status'] = $val['status'];
            $response[$key]['name'] = (isset($val->info[0]->name) ? $val->info[0]->name : '');
            $response[$key]['value'] = (isset($val->info[0]->value) ? ($val['alias'] == "polls" ? $survey_label : $val->info[0]->value) : '');
        }

        if ($type == "both") {
            $eventsite_modules = \App\Models\EventSiteModuleOrder::where('event_id', $formData['event_id'])
                ->whereIn('alias', config('module.eventsite_module_alias'))
                ->with([
                    'info' => function ($query) use ($formData) {
                        return $query->where('languages_id', $formData['language_id']);
                    }
                ])
                ->orderBy('sort_order', 'asc')
                ->get();

            foreach ($eventsite_modules as $key => $val) {
                if ($val['alias'] == "attendees") {
                    $index = array_search('attendees', array_column($response, 'alias'));
                    if (isset($index) && $response[$index] && $response[$index]['status'] == 0 && $val['status'] == 1) {
                        $response[$index]['status'] = $val['status'];
                    }
                } else if ($val['alias'] == "program") {
                    $index = array_search('agendas', array_column($response, 'alias'));
                    if (isset($index) && $response[$index] && $response[$index]['status'] == 0 && $val['status'] == 1) {
                        $response[$index]['status'] = $val['status'];
                    }
                } else if ($val['alias'] == "speakers") {
                    $index = array_search('speakers', array_column($response, 'alias'));
                    if (isset($index) && $response[$index] && $response[$index]['status'] == 0 && $val['status'] == 1) {
                        $response[$index]['status'] = $val['status'];
                    }
                } else if ($val['alias'] == "practicalinformation") {
                    $index = array_search('infobooth', array_column($response, 'alias'));
                    if (isset($index) && $response[$index] && $response[$index]['status'] == 0 && $val['status'] == 1) {
                        $response[$index]['status'] = $val['status'];
                    }
                } else if ($val['alias'] == "additional_information") {
                    $index = array_search('additional_info', array_column($response, 'alias'));
                    if (isset($index) && $response[$index] && $response[$index]['status'] == 0 && $val['status'] == 1) {
                        $response[$index]['status'] = $val['status'];
                    }
                } else if ($val['alias'] == "general_information") {
                    $index = array_search('general_info', array_column($response, 'alias'));
                    if (isset($index) && $response[$index] && $response[$index]['status'] == 0 && $val['status'] == 1) {
                        $response[$index]['status'] = $val['status'];
                    }
                }
            }
        }

        return $response;
    }

    /**
     * event app modules setting
     * @param array
     */
    public function modules_setting($formData)
    {
        $data = \App\Models\EventModuleOrder::where('event_id', $formData['event_id'])
            ->where('is_purchased', 1)
            ->where('type', '=', 'frontend')
            ->whereIn('alias', config('module.setting'))
            ->with([
                'info' => function ($query) use ($formData) {
                    return $query->where('languages_id', $formData['language_id']);
                }
            ])
            ->orderBy('sort_order', 'asc')
            ->get();

        $response = array();

        foreach ($data as $key => $val) {
            $response[$key]['id'] = $val['id'];
            $response[$key]['alias'] = $val['alias'];
            $response[$key]['status'] = $val['status'];
            $response[$key]['name'] = (isset($val->info[0]->name) ? $val->info[0]->name : '');
            $response[$key]['value'] = (isset($val->info[0]->value) ? $val->info[0]->value : '');
        }

        return $response;
    }

    /**
     * update event app modules
     * @param array
     */
    public function updateModules($formInput)
    {
        $event = \App\Models\Event::where('id', $formInput['event_id'])->first();
        if ($event) {

            if (isset($formInput['is_app']) && isset($formInput['is_registration'])) {
                $event->is_app = $formInput['is_app'];
                $event->is_registration = $formInput['is_registration'];
                $event->save();
            }

            //update module order info
            if (isset($formInput['modules']) && is_array($formInput['modules'])) {
                $sort = 0;
                foreach ($formInput['modules'] as $module) {
                    $event_modules_order = \App\Models\EventModuleOrder::where('event_id', $event->id)->where('alias', $module['alias'])->where('type', 'backend')->first();
                    $event_modules_order->sort_order = $sort;
                    $event_modules_order->status = $module['status'];
                    $event_modules_order->save();

                    \App\Models\ModuleOrderInfo::where('languages_id', $event->language_id)->where('module_order_id', $event_modules_order->id)->update([
                        "value" => $module['value']
                    ]);
                    $sort++;
                }
            }
        }
    }

    public function updateUserInterfaceLanguage($formData)
    {
        $organizer = \App\Models\Organizer::find($formData['id']);
        if ($organizer && in_array($formData['interface_language_id'], [1, 2, 3, 4, 5, 6, 7, 8, 9])) {
            $organizer->language_id = $formData['interface_language_id'];
            $organizer->update();
        }
    }

    /**
     * update branding
     * @param array
     */
    public function updateBranding($formInput)
    {
        $event = \App\Models\Event::find($formInput['event_id']);

        $header_logo = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'header_logo')->first();

        if (isset($formInput['header_logo']) && is_base64($formInput['header_logo'])) {
            $value = 'branding_header_logo_' . time() . '.png';
            //clone image
            copyFile(
                $formInput['header_logo'],
                config('cdn.cdn_upload_path') . 'assets/event/branding/' . $value
            );

            if ($header_logo) {
                //deleteFile(config('cdn.cdn_upload_path') . 'assets/event/branding/' . $header_logo->value);
                $header_logo->value = $value;
                $header_logo->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'header_logo',
                    'value' => $value
                ]);
            }

            //insert into media library
            $this->mediaLibraryRepository->InsertDirectoryImage($value, 'header_logo');
        } else if ($header_logo) {
            $header_logo->value = ($formInput['header_logo'] ? fetchImageName($formInput['header_logo']) : '');
            $header_logo->save();
        }

        $app_icon = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'app_icon')->first();

        if (isset($formInput['app_icon']) && is_base64($formInput['app_icon'])) {
            $value = 'branding_app_icon_' . time() . '.png';
            //clone image
            copyFile(
                $formInput['app_icon'],
                config('cdn.cdn_upload_path') . 'assets/event/branding/' . $value
            );

            if ($app_icon) {
                //deleteFile(config('cdn.cdn_upload_path') . 'assets/event/branding/' . $app_icon->value);
                $app_icon->value = $value;
                $app_icon->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'app_icon',
                    'value' => $value
                ]);
            }

            //insert into media library
            $this->mediaLibraryRepository->InsertDirectoryImage($value, 'app_icon');
        } else if ($app_icon) {
            $app_icon->value = ($formInput['app_icon'] ? fetchImageName($formInput['app_icon']) : '');
            $app_icon->save();
        }

        $fav_icon = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'fav_icon')->first();

        if (isset($formInput['fav_icon']) && is_base64($formInput['fav_icon'])) {
            $value = 'branding_fav_icon_' . time()  . '.png';
            //clone image
            copyFile(
                $formInput['fav_icon'],
                config('cdn.cdn_upload_path') . 'assets/event/branding/' . $value
            );

            if ($fav_icon) {
                //deleteFile(config('cdn.cdn_upload_path') . 'assets/event/branding/' . $fav_icon->value);
                $fav_icon->value = $value;
                $fav_icon->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'fav_icon',
                    'value' => $value
                ]);
            }

            //insert into media library
            $this->mediaLibraryRepository->InsertDirectoryImage($value, 'favicon');
        } else if ($fav_icon) {
            $fav_icon->value = ($formInput['fav_icon'] ? fetchImageName($formInput['fav_icon']) : '');
            $fav_icon->save();
        }

        $social_media_logo = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'social_media_logo')->first();

        if (isset($formInput['social_media_logo']) && is_base64($formInput['social_media_logo'])) {
            $value = 'branding_social_media_logo_' . time() . '.png'; //clone image
            copyFile(
                $formInput['social_media_logo'],
                config('cdn.cdn_upload_path') . 'assets/event/social_media/' . $value
            );

            if ($social_media_logo) {
                //deleteFile(config('cdn.cdn_upload_path') . 'assets/event/social_media/' . $social_media_logo->value);
                $social_media_logo->value = $value;
                $social_media_logo->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'social_media_logo',
                    'value' => $value
                ]);
            }

            //insert into media library
            $this->mediaLibraryRepository->InsertDirectoryImage($value, 'social_media_logo');
        } else if ($social_media_logo && !$formInput['social_media_logo']) {
            $social_media_logo->value = ($formInput['social_media_logo'] ? fetchImageName($formInput['social_media_logo']) : '');
            $social_media_logo->save();
        }

        if (isset($formInput['primary_color']) && $formInput['primary_color']) {
            $primary_color = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'primary_color')->first();
            if ($primary_color) {
                $primary_color->value = $formInput['primary_color'];
                $primary_color->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'primary_color',
                    'value' => $formInput['primary_color']
                ]);
            }
        }

        if (isset($formInput['secondary_color']) && $formInput['secondary_color']) {
            $secondary_color = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'secondary_color')->first();
            if ($secondary_color) {
                $secondary_color->value = $formInput['secondary_color'];
                $secondary_color->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'secondary_color',
                    'value' => $formInput['secondary_color']
                ]);
            }
        }
    }
    /**
     * update column status
     * @param array
     */
    static public function updateColumnStatus($formInput)
    {
        $query = \DB::table($formInput['table']);
        if (isset($formInput['event_id'])) {
            $query->where('event_id', $formInput['event_id']);
        }
        $query->where('id', $formInput['id'])->whereNull('deleted_at');
        $query->update([
            $formInput['column'] => $formInput['value']
        ]);
    }

    /**
     *Event setting
     * @param array
     */
    static public function getEventSetting($formInput)
    {
        $data = array();
        //Event and event settings
        $event = \App\Models\Event::where('id', $formInput['event_id'])->with('settings')->first();
        if ($event) {
            if (count($event->settings) > 0) {
                foreach ($event->settings as $val) {
                    if ($val->value) $data[$val->name] = $val->value;
                }
            }
            //information with domain names
            $event_info = \App\Models\EventInfo::where('event_id', $formInput['event_id'])
                ->where('languages_id', $formInput['language_id'])->get();
            foreach ($event_info as $info) {
                if ($info->name == 'domain_name') {
                    $data[$info->name][] = $info->value;
                } else {
                    $data[$info->name] = $info->value;
                }
            }
        }
        return $data;
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    static public function getEventModule($formInput)
    {
        return \App\Models\EventModuleOrder::where('event_id', $formInput['event_id'])->where('alias', $formInput['alias'])->with(['info'])->first();
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    static public function getGdprSetting($formInput) {
        return \App\Models\EventGdprSetting::where('event_id', $formInput['event_id'])->first();
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    static public function getGdprInfo($formInput)
    {
        return \App\Models\EventGdpr::where('event_id', $formInput['event_id'])->first();
    }

    static public function getSettings($event_id, $binding = false)
    {
        $settings = EventSetting::where('event_id', $event_id)->get();

        if($binding) {
            return convertToAssociativeArray($settings);
        } else {
            return $settings;
        }
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    static public function getFoodAllergies($formInput) {
        $row = \App\Models\EventFoodAllergies::where('event_id', $formInput['event_id'])->first();
        if($row) {
            $row->food_link = between('{detail_link}', '{/detail_link}', $row->inline_text);
            $remove_text = '{detail_link}'.$row->food_link.'{/detail_link}';
            $row->inline_text = str_replace($remove_text, '', $row->inline_text);
        }
        return $row;
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    static public function getDisclaimerSetting($formInput) {
        return \App\Models\EventDisclaimerSetting::where('event_id', $formInput['event_id'])->first();
    }
}
