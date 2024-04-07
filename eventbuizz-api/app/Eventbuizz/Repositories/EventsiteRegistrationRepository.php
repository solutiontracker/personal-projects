<?php

namespace App\Eventbuizz\Repositories;

use App\Eventbuizz\Repositories\ModuleRepository;

use Illuminate\Http\Request;

class EventsiteRegistrationRepository extends AbstractRepository
{
    private $request;

    private $moduleRepository;

    private $_registration_site_settings = [
        'setting_modules' => ['payment_card', 'general_settings', 'eventsitebaner', 'payment_settings', 'additional_fields', 'hotel', 'waiting_list'],
        'additional_attendee' => ['evensite_additional_attendee', 'evensite_additional_company', 'evensite_additional_department', 'evensite_additional_organization', 'evensite_additional_phone', 'evensite_additional_custom_fields', 'evensite_additional_title', 'evensite_additional_last_name'],
        'hotel' => ['show_hotels', 'hotel_vat', 'hotel_vat_status', 'hotel_from_date', 'hotel_to_date', 'hotel_currency', 'show_hotel_prices']
    ];

    public function __construct(Request $request, ModuleRepository $moduleRepository)
    {
        $this->request = $request;
        $this->moduleRepository = $moduleRepository;
    }

    public function cloneEventRegistrationModuleSettings($modules, $from_event_id, $to_event_id, $languages)
    {
        foreach ($modules as $module) {
            if (in_array($module, $this->_registration_site_settings['setting_modules'])) {
                $model = $this->moduleRepository->getModulesSetting($module);
                $model = $model['settings'];
                $this->copyEventRegistrationModuleSettings($module, $model, $from_event_id, $to_event_id);
            } elseif ($module == 'top_menu') {
                $this->copyEventTopMenu($from_event_id, $to_event_id, $languages);
            } elseif ($module == 'sections') {
                $this->copyEventSection($from_event_id, $to_event_id, $languages);
            } elseif ($module == 'sub_heading') {
                $this->copyEventSubHeading($from_event_id, $to_event_id, $languages);
            } elseif ($module == 'section_fields') {
                $this->copyEventSectionFields($from_event_id, $to_event_id, $languages);
            }
        }
    }

    private function copyEventRegistrationModuleSettings($module, $model, $from_event_id, $to_event_id)
    {
        if (!empty($model)) {
            $temp_name = 'App\Models\\' . $model;
            if (class_exists($temp_name)) {
                $model_name = $temp_name;
            } else {
                $model_name = "\\" . $model;
            }

            $from_setting = $model_name::where('event_id', $from_event_id)->first();

            if ($from_setting) {
                $setting = $model_name::where('event_id', $to_event_id)->first();
                if ($setting) {
                    $to_setting = $model_name::find($setting->id);
                } else {
                    $to_setting = new $model_name();
                    $to_setting->event_id = $to_event_id;
                }


                foreach ($from_setting as $key => $value) {
                    $continue = false;
                    if ($module == 'hotel') {
                        if (!in_array($key, $this->_registration_site_settings['hotel'])) {
                            $continue = true;
                        }
                    } elseif ($module == 'additional_fields') {
                        if (!in_array($key, $this->_registration_site_settings['additional_attendee'])) {
                            $continue = true;
                        }
                    } elseif ($module == 'payment_settings') {
                        if (in_array($key, $this->_registration_site_settings['hotel']) || in_array($key, $this->_registration_site_settings['additional_attendee'])) {
                            $continue = true;
                        }
                    }

                    if (in_array($key, ['id', 'event_id', 'created_at', 'updated_at']) || $continue) continue;
                    $to_setting->$key = $value;
                }
                $to_setting->save();
            }
        }
    }

    private function copyEventTopMenu($from_event_id, $to_event_id, $languages)
    {
        $top_menu_from = \App\Models\EventSiteModuleOrder::where('event_id', '=', $from_event_id)->get()->toArray();
        foreach ($top_menu_from as $top_menu) {
            $top_menu_to = \App\Models\EventSiteModuleOrder::where('event_id', '=', $to_event_id)->where('alias', '=', $top_menu['alias'])->first();
            if ($top_menu_to) {
                foreach ($top_menu as $key => $value) {
                    if (in_array($key, ['id', 'event_id', 'created_at', 'updated_at'])) continue;
                    $top_menu_to->$key = $value;
                }
                $top_menu_to->save();

                foreach ($languages as $language) {
                    $module_info_from = \App\Models\EventSiteModuleOrderInfo::where('module_order_id', $top_menu['id'])->where('languages_id', $language)->first();
                    if ($module_info_from) {
                        $module_info_to = \App\Models\EventSiteModuleOrderInfo::where('module_order_id', $top_menu_to->id)->where('languages_id', $language)->first();
                        if ($module_info_to) {
                            $module_info_to->value = $module_info_from->value;
                            $module_info_to->save();
                        }
                    }
                }
            }
        }
    }

    private function copyEventSection($from_event_id, $to_event_id, $languages)
    {
        $sections_from = \App\Models\EventsiteSection::where('event_id', $from_event_id)->get()->toArray();
        foreach ($sections_from as $section) {
            $section_to = \App\Models\EventsiteSection::where('event_id', $to_event_id)->where('alias', $section['alias'])->first();
            if ($section_to) {
                foreach ($section as $key => $value) {
                    if (in_array($key, ['id', 'event_id', 'created_at', 'updated_at'])) continue;
                    $section_to->$key = $value;
                }
                $section_to->save();

                foreach ($languages as $language) {
                    $module_info_from = \App\Models\EventsiteSectionInfo::where('section_id', $section['id'])->where('languages_id', $language)->first();
                    if ($module_info_from) {
                        $module_info_to = \App\Models\EventsiteSectionInfo::where('section_id', $section_to->id)->where('languages_id', $language)->first();
                        if ($module_info_to) {
                            $module_info_to->value = $module_info_from->value;
                            $module_info_to->save();
                        }
                    }
                }
            }
        }
    }

    private function copyEventSubHeading($from_event_id, $to_event_id, $languages)
    {
        $sub_heading_from = \App\Models\EventSiteDescription::where('event_id', $from_event_id)->first();
        if ($sub_heading_from) {
            $sub_heading_to = \App\Models\EventSiteDescription::where('event_id', $to_event_id)->first();
            if (!$sub_heading_to) {
                $sub_heading_to =  new \App\Models\EventSiteDescription();
                $sub_heading_to->event_id = $to_event_id;
                $sub_heading_to->save();
                $sub_heading_to = \App\Models\EventSiteDescription::where('event_id', $to_event_id)->first();
            }

            foreach ($languages as $language) {
                $module_info_from = \App\Models\EventSiteDescriptionInfo::where('description_id', $sub_heading_from->id)->where('languages_id', $language)->get();
                if ($module_info_from) {
                    $module_info_from = $module_info_from->toArray();
                    if (count($module_info_from) > 0) {
                        foreach ($module_info_from as $info) {
                            $module_info_to = \App\Models\EventSiteDescriptionInfo::where('name', $info['name'])->where('description_id', $sub_heading_to->id)->where('languages_id', $language)->first();
                            if ($module_info_to) {
                                $module_info_to->name = $info['name'];
                                $module_info_to->value = $info['value'];
                            } else {
                                $module_info_to = new \App\Models\EventSiteDescriptionInfo();
                                $module_info_to->name = $info['name'];
                                $module_info_to->value = $info['value'];
                                $module_info_to->description_id = $sub_heading_to->id;
                                $module_info_to->description_id = $language;
                                $module_info_to->save();
                            }
                        }
                    }
                }
            }
        }
    }

    private function copyEventSectionFields($from_event_id, $to_event_id, $languages)
    {
		$fields = \App\Models\BillingField::where('event_id', $from_event_id)->with(['info'])->get();

		if (empty($fields)) {
			$fields = \App\Models\Field::with(['info'])->get();
		}

		//Delete old fields
		\App\Models\BillingField::where('event_id', $to_event_id)->delete();
		
		foreach ($fields as $field) {

			$found = false;

			if ($field instanceof \App\Models\Field) {

				$new_field = new \App\Models\BillingField();

			} else {

				$new_field = $field->replicate();

				if (session()->has('clone.event.event_registration_form.' . $field->registration_form_id) && $field->registration_form_id > 0) {
					$new_field->registration_form_id = session()->get('clone.event.event_registration_form.' . $field->registration_form_id);
				}

			}

			$new_field->event_id = $to_event_id;

			$new_field->sort_order = $field->sort_order;

			$new_field->status = $field->status;

			$new_field->mandatory = $field->mandatory;

			$new_field->field_alias = $field->field_alias;

			$new_field->type = $field->type;

			$new_field->section_alias = $field->section_alias;

			$new_field->save();

			foreach ($field->info as $info) {
				$new_info = \App\Models\BillingFieldInfo::where('field_id', $new_field->id)->where('name', $info->name)->where('languages_id', $info->languages_id)->first();
                if(!$new_info) {
                    $new_info = new \App\Models\BillingFieldInfo();
                    $new_info->field_id = $new_field->id;
                    $new_info->name = $info->name;
                    $new_info->languages_id = $info->languages_id;
                }
                $new_info->value = $info->value;
                $new_info->save();
			}
		}
    }

    public function installation($request)
    {
        $this->cloneEventRegistrationModuleSettings(['general_settings', 'hotel', 'top_menu', 'sub_heading', 'waiting_list', 'eventsitebaner', 'payment_settings', 'sections', 'section_fields', 'payment_card'], $request['from_event_id'], $request['to_event_id'], $request['languages']);
    }
}
