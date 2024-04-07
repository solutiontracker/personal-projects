<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class LabelRepository extends AbstractRepository
{
    private $request;

    private $dontCopyLabels = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function install($request)
    {
        //Function to create default labels
        $this->createEventModulesLabels(['social_section', 'top_menu', 'eventsite_section', 'eventsite', 'customizelabels', 'generallabels', 'myquestions', 'exportlabels', 'tickets', 'reservationlabels', 'gdpr', "checkIn", "mydocuments", "polls", "social", "attendees", "qa", "agendas", "sponsors", "exhibitors", "myturnlist", "subregistration", "gallery", "mobile_app", "social_wall", "desktoplabels", "internal_booking", "ddirectory", "eventsite_required", "event_description", "hdquestions", "help_desk", "managedocuments", "nativeapp", "plans"], $request['to_event_id'], $request['languages']);
    }

    public function copyModuleLabels($module, $from_event_id, $to_event_id, $languages)
    {
        if ($module == "social_section") {
            $settingFrom = \App\Models\EventSiteSocialSection::where('event_id', '=', $from_event_id)->get();
            if ($settingFrom) {
                foreach ($settingFrom as $setting) {
                    $settingTo = \App\Models\EventSiteSocialSection::where('event_id', '=', $to_event_id)->where('alias', $setting->alias)->first();
                    if ($settingTo) {
                        $settingTo->status = $setting->status;
                        $settingTo->is_purchased = $setting->is_purchased;
                        $settingTo->icon = $setting->icon;
                        $settingTo->sort_order = $setting->sort_order;
                        $settingTo->version = $setting->version;
                        $settingTo->save();
                    }
                }
            }
        } else {
            foreach ($languages as $language) {
                $labelsFrom = \App\Models\EventSiteText::where('event_id', '=', $from_event_id)->where('module_alias', '=', $module)->where('parent_id', '!=', '0')->with(['info' => function ($query) use ($language) {
                    return $query->where('languages_id', '=', $language);
                }])->get();

                if ($labelsFrom) {
                    foreach ($labelsFrom as $label) {
                        $labelTo = \App\Models\EventSiteText::where('event_id', '=', $to_event_id)->where('alias', '=', $label['alias'])->where('parent_id', '!=', '0')->where('module_alias', '=', $module)->with(['info' => function ($query) use ($language) {
                            return $query->where('languages_id', '=', $language);
                        }])->first();

                        if ($labelTo) {
                            if ($labelTo->info && isset($labelTo['info'][0]['id'])) {
                                $labelInfoObject = \App\Models\EventSiteTextInfo::find($labelTo['info'][0]['id']);
                                if ($labelInfoObject) {
                                    if(in_array($labelTo->alias, $this->dontCopyLabels)) {
                                        $labelInfoObject->value = '';
                                    } else {
                                        $labelInfoObject->value = (isset($label['info'][0]['value']) ? $label['info'][0]['value'] : '');
                                    }
                                    $labelInfoObject->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function cloneEventModulesLabels($request)
    {
        $modules = ['social_section', 'top_menu', 'eventsite_section', 'eventsite', 'customizelabels', 'generallabels', 'myquestions', 'exportlabels', 'tickets', 'reservationlabels', 'gdpr', "checkIn", "mydocuments", "polls", "social", "attendees", "qa", "agendas", "sponsors", "exhibitors", "myturnlist", "subregistration", "gallery", "mobile_app", "social_wall", "desktoplabels", "internal_booking", "ddirectory", "eventsite_required", "event_description", "hdquestions", "help_desk", "managedocuments", "nativeapp", "plans"];

        foreach ($modules as $module) {
            if (is_array($module)) {
                foreach ($module as $sub_module) {
                    $this->copyModuleLabels($sub_module, $request['from_event_id'], $request['to_event_id'], $request['languages']);
                }
            } else {
                $this->copyModuleLabels($module, $request['from_event_id'], $request['to_event_id'], $request['languages']);
            }
        }
    }

    public function createEventModulesLabels($modules, $event_id, $languages)
    {
        foreach ($modules as $module_alias) {
            if ($module_alias == "social_section") {
                $labels = [
                    'Facebook', 'Twitter', 'Linkedin', 'Pinterest', 'Google+', 'Email'
                ];
            } else {
                $labels = \App\Models\Label::where('parent_id', '=', '0')
                    ->where('module_alias', $module_alias)
                    ->with(['info' => function($q)  use ($languages) {
                        return $q->where('languages_id', $languages);
                    }])
                    ->with(['children' => function ($r) {
                        return $r->orderBy('constant_order');
                    }, 'children.childrenInfo' => function ($r) use ($languages) {
                        return $r->whereIn('languages_id', $languages);
                    }])
                    ->orderBy('section_order')->get();
            }
            $this->createEventModulesLabelsInfo($labels, $module_alias, $event_id);
        }
    }

    public function createEventModulesLabelsInfo($labels, $module_alias, $event_id)
    {    
        if ($module_alias == "social_section") {
            $event_languages = get_event_languages($event_id);
            foreach ($labels as $key => $social) {
                $social_section['event_id'] = $event_id;
                $social_section['sort_order'] = $key;
                $social_section['alias'] = $social;
                $social_section['version'] = '1.0';
                $social_section['status'] = '1';
                $social_section['is_purchased'] = 0;
                $social_section['created_at'] = \Carbon\Carbon::now();
                $social_section['updated_at'] = \Carbon\Carbon::now();
                $parent_label = \App\Models\EventSiteSocialSection::create($social_section);
                foreach ($event_languages as $language_id) {
                    $social_section_info['name'] = $social;
                    $social_section_info['value'] = $social;
                    $social_section_info['section_id'] = $parent_label->id;
                    $social_section_info['languages_id'] = $language_id;
                    $social_section_info['status'] = 1;
                    $social_section_info['created_at'] = \Carbon\Carbon::now();
                    $social_section_info['updated_at'] = \Carbon\Carbon::now();
                    \App\Models\EventSiteSocialSectionInfo::create($social_section_info);
                }
            }
        } else if ($module_alias == "top_menu") {
            foreach ($labels as $info) {
                $module_order['event_id'] = $event_id;
                $module_order['sort_order'] = '0';
                $module_order['alias'] = $info->alias;
                $module_order['version'] = '1.0';
                if ($info->alias == 'attendees') {
                    $module_order['status'] = 0;
                } else {
                    $module_order['status'] = 1;
                }
                $module_order['is_purchased'] = 0;
                $module_order['created_at'] = \Carbon\Carbon::now();
                $module_order['updated_at'] = \Carbon\Carbon::now();
                $parent_label = \App\Models\EventSiteModuleOrder::create($module_order);
                foreach ($info['info'] as $info) {
                    $module_order_info['name'] = $info->name;
                    $module_order_info['value'] = $info->value;
                    $module_order_info['module_order_id'] = $parent_label->id;
                    $module_order_info['languages_id'] = $info->languages_id;
                    $module_order_info['status'] = 1;
                    $module_order_info['created_at'] = \Carbon\Carbon::now();
                    $module_order_info['updated_at'] = \Carbon\Carbon::now();
                    \App\Models\EventSiteModuleOrderInfo::create($module_order_info);
                }
            }
        } else if ($module_alias == "eventsite_section") {
            foreach ($labels as $key => $info) {
                $eventsite_section['event_id'] = $event_id;
                $eventsite_section['sort_order'] = $key;
                $eventsite_section['alias'] = $info->alias;
                $eventsite_section['version'] = '1.0';
                if ($info->alias == 'custom_html1') {
                    $eventsite_section['status'] = '0';
                } elseif ($info->alias == 'custom_html2') {
                    $eventsite_section['status'] = '0';
                } elseif ($info->alias == 'social_section') {
                    $eventsite_section['status'] = '0';
                } else {
                    $eventsite_section['status'] = '1';
                }
                $eventsite_section['is_purchased'] = 0;
                $eventsite_section['created_at'] = \Carbon\Carbon::now();
                $eventsite_section['updated_at'] = \Carbon\Carbon::now();
                $parent_label = \App\Models\EventsiteSection::create($eventsite_section);
                foreach ($info['info'] as $info) {
                    $eventsite_section_info['name'] = $info->name;
                    $eventsite_section_info['value'] = $info->value;
                    $eventsite_section_info['section_id'] = $parent_label->id;
                    $eventsite_section_info['languages_id'] = $info->languages_id;
                    $eventsite_section_info['status'] = 1;
                    $eventsite_section_info['created_at'] = \Carbon\Carbon::now();
                    $eventsite_section_info['updated_at'] = \Carbon\Carbon::now();
                    \App\Models\EventsiteSectionInfo::create($eventsite_section_info);
                }
            }
        } else if (in_array($module_alias, ['eventsite', 'customizelabels', 'generallabels', 'myquestions', 'exportlabels', 'tickets', 'reservationlabels', 'gdpr', "checkIn", "mydocuments", "polls", "social", "attendees", "qa", "agendas", "sponsors", "exhibitors", "myturnlist", "subregistration", "gallery", "mobile_app", "social_wall", "desktoplabels", "internal_booking", "ddirectory", "eventsite_required", "event_description", "hdquestions", "help_desk", "managedocuments", "nativeapp", "plans"])) {
            foreach ($labels as $label) {
                $count = \App\Models\EventSiteText::where('label_parent_id', '=', $label->id)->where('event_id', '=', $event_id)->count();
                if ($count == 0) {
                    $text['event_id'] = $event_id;
                    $text['section_order'] = $label->section_order;
                    $text['constant_order'] = $label->constant_order;
                    $text['alias'] = $label->alias;;
                    $text['module_alias'] = $label->module_alias;
                    $text['label_parent_id'] = $label->id;
                    $text['parent_id'] = 0;
                    $text['status'] = 1;
                    $text['created_at'] = \Carbon\Carbon::now();
                    $text['updated_at'] = \Carbon\Carbon::now();
                    $parent_label = \App\Models\EventSiteText::create($text);
                    if ($label['info']) {
                        foreach ($label['info'] as $info) {
                            $text_info['name'] = $info->name;
                            if(in_array($label->alias, $this->dontCopyLabels)) {
                                $text_info['value'] = '';
                            } else {
                                $text_info['value'] = $info->value;
                            }
                            $text_info['text_id'] = $parent_label->id;
                            $text_info['languages_id'] = $info->languages_id;
                            $text_info['status'] = 1;
                            $text_info['created_at'] = \Carbon\Carbon::now();
                            $text_info['updated_at'] = \Carbon\Carbon::now();
                            \App\Models\EventSiteTextInfo::create($text_info);
                        }
                    }
                    if ($label['children']) {
                        foreach ($label['children'] as $children) {
                            $count = \App\Models\EventSiteText::where('label_parent_id', '=', $children->id)->where('event_id', '=', $event_id)->count();
                            if ($count == 0) {
                                $text['event_id'] = $event_id;
                                $text['section_order'] = $children->section_order;
                                $text['constant_order'] = $children->constant_order;
                                $text['alias'] = $children->alias;;
                                $text['module_alias'] = $children->module_alias;
                                $text['label_parent_id'] = $children->id;
                                $text['parent_id'] = $parent_label->id;
                                $text['status'] = 1;
                                $text['created_at'] = \Carbon\Carbon::now();
                                $text['updated_at'] = \Carbon\Carbon::now();
                                $inner_parent_label = \App\Models\EventSiteText::create($text);

                                if ($children['childrenInfo']) {
                                    foreach ($children['childrenInfo'] as $child_info) {
                                        $text_info['name'] = $child_info->name;
                                        if(in_array($children->alias, $this->dontCopyLabels)) {
                                            $text_info['value'] = '';
                                        } else {
                                            $text_info['value'] = $child_info->value;
                                        }
                                        $text_info['text_id'] = $inner_parent_label->id;
                                        $text_info['languages_id'] = $child_info->languages_id;
                                        $text_info['status'] = 1;
                                        $text_info['created_at'] = \Carbon\Carbon::now();
                                        $text_info['updated_at'] = \Carbon\Carbon::now();
                                        \App\Models\EventSiteTextInfo::create($text_info);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function getEventLabels($formInput, $module_alias = '', $alias = '')
    {
        $query = \App\Models\EventSiteText::where('event_id', $formInput['event_id']);

        if (is_array($module_alias))
            $query->whereIn('module_alias', $module_alias);
        else if ($module_alias)
            $query->where('module_alias', $module_alias);

        if (is_array($alias))
            $query->whereIn('alias', $alias);
        else if ($alias)
            $query->where('alias', $alias);

        $query->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', '=', $formInput['language_id']);
        }]);

        $query->with(['children' => function ($r) {
            return $r->orderBy('constant_order');
        }, 'children.childrenInfo' => function ($r) use ($formInput) {
            return $r->where('languages_id', '=', $formInput['language_id']);
        }]);

        $labels = $query->orderBy('section_order')
            ->get()
            ->toArray();
        $labels_array = [];

        foreach ($labels as $record) {
            $labels_array[$record['alias']] = (isset($record['info'][0]['value']) ? $record['info'][0]['value'] : '');
            foreach ($record['children'] as $row) {
                if (count($row['children_info']) > 0) {
                    foreach ($row['children_info'] as $val) {
                        $labels_array[$row['alias']] = $val['value'];
                    }
                }
            }
        }

        if ($alias && isset($labels_array[$alias])) {
            return $labels_array[$alias];
        } else {
            return $labels_array;
        }
    }
}
