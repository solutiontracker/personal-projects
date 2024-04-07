<?php

namespace App\Eventbuizz\Repositories;

use App\Models\AdditionalInfoPage;
use App\Models\EventInfo;
use App\Models\EventInfoPage;
use App\Models\GeneralInfoPage;
use Illuminate\Http\Request;

use App\Models\GeneralInfoMenu;

use App\Models\AdditionalInfoMenu;

use App\Models\EventInfoMenu;

use App\Eventbuizz\Repositories\EventRepository;

use App\Models\InformationSection;


class EventInfoRepository extends AbstractRepository
{
    private $request;

    protected $generalInfoMenuModel;

    protected $additionalInfoMenuModel;

    protected $eventInfoMenuModel;

    protected $informationSectionModel;

    protected $model;

    public function __construct(Request $request, GeneralInfoMenu $generalInfoMenuModel, AdditionalInfoMenu $additionalInfoMenuModel, EventInfoMenu $eventInfoMenuModel, InformationSection $informationSection)
    {
        $this->request = $request;
        $this->generalInfoMenuModel = $generalInfoMenuModel;
        $this->additionalInfoMenuModel = $additionalInfoMenuModel;
        $this->eventInfoMenuModel = $eventInfoMenuModel;
        $this->informationSectionModel = $informationSection;
    }

    /**
     * when new event create / cloning event
     *
     * @param array
     */
    public function install($request)
    {
        if ($request["content"]) {
            $data = $this->getbyEventId("practical-info", $request["from_event_id"]);
            $this->clone($data, "practical-info", $request);
            $data = $this->getbyEventId("additional-info", $request["from_event_id"]);
            $this->clone($data, "additional-info", $request);
            $data = $this->getbyEventId("general-info", $request["from_event_id"]);
            $this->clone($data, "general-info", $request);
            $data = $this->getbyEventId("information-page", $request["from_event_id"]);
            $this->clone($data, "information-page", $request);
        }
    }

    //event info clone => practical info/general info/additional info
    public function clone($data, $type, $request)
    {
        if ($type == "general-info") {
            $menu_model = \App\Models\GeneralInfoMenu::query();
            $menu_info_model = \App\Models\GeneralInfoMenuInfo::query();
            $page_model = \App\Models\GeneralInfoPage::query();
            $page_info_model = \App\Models\GeneralInfoPageInfo::query();
        } else if ($type == "additional-info") {
            $menu_model = \App\Models\AdditionalInfoMenu::query();
            $menu_info_model = \App\Models\AdditionalInfoMenuInfo::query();
            $page_model = \App\Models\AdditionalInfoPage::query();
            $page_info_model = \App\Models\AdditionalInfoPageInfo::query();
        } else if ($type == "practical-info") {
            $menu_model = \App\Models\EventInfoMenu::query();
            $menu_info_model = \App\Models\EventInfoMenuInfo::query();
            $page_model = \App\Models\EventInfoPage::query();
            $page_info_model = \App\Models\EventInfoPageInfo::query();
        } else if($type == "information-page"){
            $menu_model = \App\Models\InformationSection::query();
            $menu_info_model = \App\Models\InformationSectionInfo::query();
            $page_model = \App\Models\InformationPage::query();
            $page_info_model = \App\Models\InformationPageInfo::query();
            $top_menu = \App\Models\EventsiteTopMenu::query();
            $top_menu_info = \App\Models\EventsiteTopMenuInfo::query();
        }

        if($type === "information-page"){
           
                $InformationPageMenuIdsReplacment = [];
                if (count($data) > 0) {
                    foreach ($data as $data) {
                         
                        if($data['page_type'] == 'menu') {
                            $menu_data['sort_order'] = $data['sort_order'];
                            $menu_data['event_id'] = $request["to_event_id"];
                            $menu_data['status'] = $data['status'];
                            $menu_data['alias'] = $data['alias'];
                            $menu_data['show_in_app'] = $data['show_in_app'];
                            $menu_data['show_in_reg_site'] = $data['show_in_reg_site'];
                            
                            $menu_data_inserted = $menu_model->create($menu_data);

                            $menu_data_id = $menu_data_inserted->id;
                            $menu_info_data['name'] = 'name';
                            $menu_info_data['value'] = $data['info'][0]['value'];
                            $menu_info_data['section_id'] = $menu_data_id;
                            $menu_info_data['language_id'] = $data['info'][0]['language_id'];
                            $menu_info_data['status'] = $data['info'][0]['status'];

                            $menu_info_model->create($menu_info_data);

                            $db_data['alias'] = 'info_pages';
                            $db_data['event_id'] = $request["to_event_id"];
                            $db_data['url'] = '';
                            $db_data['status'] = $data['status'];

                            $new_create = $top_menu->create($db_data);
                            $module_id = $new_create->id;
                            if($module_id){
                                $top_info['name'] = $data['info'][0]['value'];
                                $top_info['value'] = $menu_data_id;
                                $top_info['module_order_id'] = $module_id;
                                $top_info['event_id'] = $request["to_event_id"];
                                $top_info['languages_id'] = $data['info'][0]['language_id'];
                                $top_info['status'] = $data['status'];
                                 $top_menu_info->create($top_info);
                            }

                            if(is_array($data['section_pages'])&&count($data['section_pages'])>0){
                                foreach ($data['section_pages'] as $page){
                                    if($page['parent_id']==0 || $page['parent_id']==null){
                                        $page_data=[
                                            'event_id'=>$request["to_event_id"],
                                            'section_id'=>$menu_data_id,
                                            'parent_id'=>0,
                                            'page_type'=>$page['page_type'],
                                            'sort_order'=>$page['sort_order'],
                                            'image'=>$page['image'],
                                            'image_position'=>$page['image_position'],
                                            'pdf'=>$page['pdf'],
                                            'url'=>$page['url'],
                                            'website_protocol'=>$page['website_protocol'],
                                            'icon'=>$page['icon'],
                                            'target'=>$page['target'],
                                            'status'=>$page['status']
                                        ];
                                        $page_object = $page_model->create($page_data);
                                        $page_id = $page_object->id;
                                        foreach ($page['info'] as $field) {
                                            $page_info_model->create(array('name'=>$field['name'],
                                                'value'=>trim($field['value']),'page_id'=>$page_id,'language_id'=>$field['language_id'],
                                                'status'=>$field['status']));

                                        }
                                        if($page['page_type']==1){
                                            $child_pages= $this->getSubMenus($page['id'],$request["from_event_id"]);
                                            if(count($child_pages)){
                                                foreach ($child_pages as $child_page){
                                                    $page_data=[
                                                        'event_id'=>$request["to_event_id"],
                                                        'section_id'=>$menu_data_id,
                                                        'parent_id'=>$page_id,
                                                        'page_type'=>$child_page['page_type'],
                                                        'image'=>$child_page['image'],
                                                        'image_position'=>$child_page['image_position'],
                                                        'pdf'=>$child_page['pdf'],
                                                        'url'=>$child_page['url'],
                                                        'website_protocol'=>$child_page['website_protocol'],
                                                        'icon'=>$child_page['icon'],
                                                        'target'=>$child_page['target'],
                                                        'sort_order'=>$child_page['sort_order'],
                                                        'status'=>$child_page['status']
                                                    ];
                                                    $child_page_object = $page_model->create($page_data);
                                                    $child_page_id = $child_page_object->id;
                                                    foreach ($child_page['info'] as $child_page_field) {
                                                        $page_info_model->create(array('name'=>$child_page_field['name'],
                                                            'value'=>trim($child_page_field['value']),'page_id'=>$child_page_id,'language_id'=>$child_page_field['language_id'],
                                                            'status'=>$child_page_field['status']));

                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

        } else {
            $parentIdsReplacment = [];
            if (count($data ?? []) > 0) {
                foreach ($data as $data) {
                    if ($data['page_type'] == 'menu') {
                        $menu_data['name'] = $data['name'];
                        if ($data['parent_id'] == '0') {
                            $menu_data['parent_id'] = $data['parent_id'];
                        } else {
                            if (isset($parentIdsReplacment[$data['parent_id']])) {
                                $menu_data['parent_id'] = $parentIdsReplacment[$data['parent_id']];
                            } else {
                                continue;
                            }
                        }
                        $menu_data['sort_order'] = $data['sort_order'];
                        $menu_data['event_id'] = $request["to_event_id"];
                        $menu_data['status'] = $data['status'];
                        $menu_data_inserted = $menu_model->create($menu_data);
                        $menu_data_id = $menu_data_inserted->id;
                        $parentIdsReplacment[$data['id']] = $menu_data_id;
                        $menu_info_data['name'] = 'name';
                        $menu_info_data['value'] = $data['info'][0]['value'];
                        $menu_info_data['menu_id'] = $menu_data_id;
                        $menu_info_data['languages_id'] = $request["languages"][0];
                        $menu_info_data['status'] = $data['info'][0]['status'];
                        $menu_info_model->create($menu_info_data);
                    } elseif ($data['page_type'] == '1' || $data['page_type'] == '2') {
                        $page_data['sort_order'] = $data['sort_order'];
                        if ($data['menu_id'] == '0') {
                            $page_data['menu_id'] = $data['menu_id'];
                        } else {
                            if (isset($parentIdsReplacment[$data['menu_id']])) {
                                $page_data['menu_id'] = $parentIdsReplacment[$data['menu_id']];
                            } else {
                                continue;
                            }
                        }
                        $page_data['event_id'] = $request["to_event_id"];
                        $page_data['page_type'] = $data['page_type'];
                        $page_data['image'] = $data['image'];
                        $page_data['image_position'] = $data['image_position'];
                        $page_data['pdf'] = $data['pdf'];
                        $page_data['icon'] = $data['icon'];
                        $page_data['url'] = $data['url'];
                        $page_data['website_protocol'] = $data['website_protocol'];
                        $page_data['status'] = $data['status'];
                        $page_data_inserted = $page_model->create($page_data);
                        $page_data_id = $page_data_inserted->id;
                        foreach ($data['info'] as $page_info) {
                            $page_info_data['name'] = $page_info['name'];
                            $page_info_data['value'] = $page_info['value'];
                            $page_info_data['page_id'] = $page_data_id;
                            $page_info_data['languages_id'] = $request["languages"][0];
                            $page_info_data['status'] = $page_info['status'];
                            $page_info_model->create($page_info_data);
                        }
                    }
                }
            }
        }
    }

    /*fetch by event id*/
    public function getbyEventId($type, $id)
    {
        $event_data = \App\Models\Event::find($id);

        if ($type == "general-info") {
            $menu_model = \App\Models\GeneralInfoMenu::query();
            $page_model = \App\Models\GeneralInfoPage::query();
        } else if ($type == "additional-info") {
            $menu_model = \App\Models\AdditionalInfoMenu::query();
            $page_model = \App\Models\AdditionalInfoPage::query();
        } else if ($type == "practical-info") {
            $menu_model = \App\Models\EventInfoMenu::query();
            $page_model = \App\Models\EventInfoPage::query();
        } else if ($type == "information-page") {
            $menu_model = \App\Models\InformationSection::query();
            $page_model = \App\Models\InformationPage::query();
        }

        if($type === "information-page"){

            $menus = array();
            $menus_query = $menu_model->where('event_id', '=', $id)
                ->with(['Info'=>function($query) use ($event_data){
                    return $query->where('language_id', '=', $event_data->language_id);
                },'section_pages','section_pages.info']) // this orderby is important for function used in EventController->copyEventUpdate
                ->orderBy('sort_order','asc')->get()->toArray();
            foreach ($menus_query as $menu) {
                $menu['page_type'] = 'menu';
                $menus[] = $menu;
            }

            $pages = array();
            $menu_pages = $page_model->where('event_id', '=', $id)
                ->with(['info'=>function($query) use ($event_data){
                    return $query->where('language_id', '=', $event_data->language_id);
                }])->orderBy('sort_order','asc')->get()->toArray();
            if (count($menu_pages) > 0) {
                foreach ($menu_pages as $menu_page) {
                    $pages[] = $menu_page;
                }
            }

            $bothArrays = array();

            if ($menus != '' && $pages != '' ) {
                $bothArrays = array_merge($menus, $pages);
            } elseif ($menus == '') {
                $bothArrays = $pages;
            } elseif ($pages == '') {
                $bothArrays = $menus;
            }

            return $bothArrays;

        } else {

            $menus = array();
            $menu_model = $menu_model->where('event_id', $id)
                ->with(['Info' => function ($query) use ($event_data) {
                    return $query->where('languages_id', $event_data->language_id);
                }])->orderBy('parent_id', 'asc')
                ->get()->toArray();
            foreach ($menu_model as $menu) {
                $menu['page_type'] = 'menu';
                $menus[] = $menu;
            }

            $pages = array();
            $page_model = $page_model->where('event_id', $id)
                ->with(['info' => function ($query) use ($event_data) {
                    return $query->where('languages_id', $event_data->language_id);
                }])->get()->toArray();;
            if (count($page_model) > 0) {
                foreach ($page_model as $page) {
                    $pages[] = $page;
                }
            }

            $bothArrays = array();

            if (!empty($menus) && !empty($pages)) {
                $bothArrays = array_merge($menus, $pages);
            } elseif (!empty($pages)) {
                $bothArrays = $pages;
            } elseif (!empty($menus)) {
                $bothArrays = $menus;
            }
            return $bothArrays;
        }
    }
    public function getSubMenus($id,$event_id)
    {
        $event_data = \App\Models\Event::where('id',$event_id)->first();
        $event_data = $event_data ? $event_data->toArray() :  ['language_id'=>1];
        $bothArrays = array();
            $pages = array();
            $menu_pages = \App\Models\InformationPage::where('parent_id', '=', $id)
                ->with(['info'=>function($query) use ($event_data){
                    return $query->where('language_id', '=', $event_data['language_id']);
                }])->orderBy('sort_order','asc')->get()->toArray();
            if (count($menu_pages) > 0) {
                foreach ($menu_pages as $menu_page) {
                    $pages[] = $menu_page;
                }
            }
            $bothArrays = $pages;

        usort($bothArrays, array($this,"sortBySortOrder"));

        return $bothArrays;

    }

    /**
     * Save data for cms
     *
     * @param array
     * @param string
     * @param string
     */

    public function store($formInput, $type, $cms)
    {

        if($cms == "information-pages" && $type == 'menu' && ((int) $formInput['menu_id'] > 0)){
            
            $sort_order = \App\Models\InformationPage::where('event_id', '=', $formInput['event_id'])->max('sort_order') + 1;
            $page = new \App\Models\InformationPage();
            $page->event_id = $formInput['event_id'];
            $page->section_id = $formInput['menu_id'];
            $page->parent_id = 0;
            $page->page_type = 1;
            $page->sort_order = $sort_order;
            $page->status = '1';
            $page->save();

            $languages = get_event_languages($formInput['event_id']);

            $info = array();
            foreach (['name'] as $field) {
                foreach ($languages as $lang) {
                    if (isset($formInput[$field])) {
                        $info[] = new \App\Models\InformationPageInfo(array(
                            'name' => $field,
                            'value' => $formInput[$field],
                            'page_id' => $page->id,
                            'language_id' => $lang,
                            'status' => 1
                        ));
                    }
                }
            }

            $page->info()->saveMany($info);

            return "created";
            
        }

        $instance = $this->setForm($formInput, $type, $cms);
        
        if ($type == "menu") {
            $instance->create();
        }

        EventRepository::add_module_progress($formInput, $cms);

        $instance->insertInfo();

    }

    /**
     * returns alias based on the name
     */
    public function getFolderAlias(string $name = "")
    {
        return strtolower(preg_replace('/\s+/', '-', $name));
    }

    /**
     * Set form values for creation/updation
     *
     * @param array
     * @param string
     * @param string
     */

    public function setForm($formInput, $type, $cms)
    {

        $formInput['event_id'] = $formInput['event_id'];
        $formInput['status'] = '1';
        $formInput['type'] = $type;
        $formInput['cms'] = $cms;
        $formInput['menu_id'] = (isset($formInput['menu_id']) ? $formInput['menu_id'] : 0);
        $formInput["alias"] = $this->getFolderAlias($formInput["name"]);

        //image movement
        if ($cms == "general-info") $dir = "general_info";
        else if ($cms == "additional-info") $dir = "additional_info";
        else if ($cms == "practical-info") $dir = "event_info";
        else if ($cms == "information-pages") $dir = "information_pages/temp";

        if (isset($formInput['image']) && $formInput['image'] && $formInput['has_image'] && $formInput['include_image']) {
            $image = 'image_' . time() . '.' . $formInput['image']->getClientOriginalExtension();
            $formInput['image']->move(config('cdn.cdn_upload_path') . '/assets/' . $dir . '/', $image);
            $formInput['image'] = $image;
        }

        if (isset($formInput['pdf']) && $formInput['pdf'] && $formInput['has_pdf'] && $formInput['include_pdf']) {
            $pdf = 'pdf_' . time() . '.' . $formInput['pdf']->getClientOriginalExtension();
            $formInput['pdf']->move(config('cdn.cdn_upload_path') . '/assets/' . $dir . '/', $pdf);
            $formInput['pdf'] = $pdf;
        }

        if ($type == "menu") {
            if ($cms == "general-info") $this->model = $this->generalInfoMenuModel;
            if ($cms == "practical-info") $this->model = $this->eventInfoMenuModel;
            if ($cms == "additional-info") $this->model = $this->additionalInfoMenuModel;
            if ($cms == "information-pages") $this->model = $this->informationSectionModel;
            $formInput['sort_order'] = ($this->model->where('event_id', '=', $formInput['event_id'])->max('sort_order') + 1);
        }

        $this->setFormInput($formInput);
        return $this;
    }

    /**
     * insert info for cms
     *
     */
    public function insertInfo()
    {
        $formInput = $this->getFormInput();
        $menu = $this->getObject();
        $languages = get_event_languages($formInput['event_id']);
        if ($formInput['cms'] == "general-info") {
            if ($formInput['type'] == 'menu') {
                foreach (['name'] as $field) {
                    foreach ($languages as $lang) {
                        if (isset($formInput[$field])) {
                            $info[] = new \App\Models\GeneralInfoMenuInfo(array(
                                'name' => $field,
                                'value' => trim($formInput[$field]),
                                'parent_id' => $menu->id,
                                'languages_id' => $lang,
                                'status' => 1
                            ));
                        }
                    }
                }
                $menu->info()->saveMany($info);
                return $this;
            } else if (in_array($formInput['type'], ['link', 'page'])) {
                $sort_order = \App\Models\GeneralInfoPage::where('event_id', '=', $formInput['event_id'])->max('sort_order') + 1;
                $page = new \App\Models\GeneralInfoPage();
                $page->event_id = $formInput['event_id'];
                $page->menu_id = $formInput['menu_id'];
                $page->sort_order = $sort_order;
                $page->page_type = $formInput['page_type'];
                $page->url = (isset($formInput['url']) ? $formInput['url'] : '');
                $page->website_protocol = (isset($formInput['website_protocol']) ? $formInput['website_protocol'] : '');
                $page->status = '1';
                if (isset($formInput['include_image']) && $formInput['include_image']) {
                    $page->image = (isset($formInput['image']) ? $formInput['image'] : '');
                    $page->image_position = (isset($formInput['image_position']) ? $formInput['image_position'] : 'top');
                }
                if (isset($formInput['include_pdf']) && $formInput['include_pdf']) {
                    $page->pdf = (isset($formInput['pdf']) ? $formInput['pdf'] : '');
                }
                $page->save();

                $fields = ($formInput['type'] == 'link' ? ['name'] : ['name', 'description', 'pdf_title']);

                foreach ($fields as $field) {
                    foreach ($languages as $lang) {
                        if (isset($formInput[$field])) {
                            $info[] = new \App\Models\GeneralInfoPageInfo(array(
                                'name' => $field,
                                'value' => $formInput[$field],
                                'page_id' => $page->id,
                                'languages_id' => $lang,
                                'status' => 1
                            ));
                        }
                    }
                }
                $page->info()->saveMany($info);
            }
        } else if ($formInput['cms'] == "additional-info") {
            if ($formInput['type'] == 'menu') {
                foreach (['name'] as $field) {
                    foreach ($languages as $lang) {
                        if (isset($formInput[$field])) {
                            $info[] = new \App\Models\AdditionalInfoMenuInfo(array(
                                'name' => $field,
                                'value' => trim($formInput[$field]),
                                'parent_id' => $menu->id,
                                'languages_id' => $lang,
                                'status' => 1
                            ));
                        }
                    }
                }
                $menu->info()->saveMany($info);
                return $this;
            } else if (in_array($formInput['type'], ['link', 'page'])) {
                $sort_order = \App\Models\AdditionalInfoPage::where('event_id', '=', $formInput['event_id'])->max('sort_order') + 1;
                $page = new \App\Models\AdditionalInfoPage();
                $page->event_id = $formInput['event_id'];
                $page->menu_id = $formInput['menu_id'];
                $page->page_type = $formInput['page_type'];
                $page->sort_order = $sort_order;
                $page->url = (isset($formInput['url']) ? $formInput['url'] : '');
                $page->website_protocol = (isset($formInput['website_protocol']) ? $formInput['website_protocol'] : '');
                $page->status = '1';
                if (isset($formInput['include_image']) && $formInput['include_image']) {
                    $page->image = (isset($formInput['image']) ? $formInput['image'] : '');
                    $page->image_position = (isset($formInput['image_position']) ? $formInput['image_position'] : 'top');
                }
                if (isset($formInput['include_pdf']) && $formInput['include_pdf']) {
                    $page->pdf = (isset($formInput['pdf']) ? $formInput['pdf'] : '');
                }
                $page->save();

                $fields = ($formInput['type'] == 'link' ? ['name'] : ['name', 'description', 'pdf_title']);

                foreach ($fields as $field) {
                    foreach ($languages as $lang) {
                        if (isset($formInput[$field])) {
                            $info[] = new \App\Models\AdditionalInfoPageInfo(array(
                                'name' => $field,
                                'value' => $formInput[$field],
                                'page_id' => $page->id,
                                'languages_id' => $lang,
                                'status' => 1
                            ));
                        }
                    }
                }
                $page->info()->saveMany($info);
            }
        } else if ($formInput['cms'] == "practical-info") {
            if ($formInput['type'] == 'menu') {
                foreach (['name'] as $field) {
                    foreach ($languages as $lang) {
                        if (isset($formInput[$field])) {
                            $info[] = new \App\Models\EventInfoMenuInfo(array(
                                'name' => $field,
                                'value' => trim($formInput[$field]),
                                'parent_id' => $menu->id,
                                'languages_id' => $lang,
                                'status' => 1
                            ));
                        }
                    }
                }
                $menu->info()->saveMany($info);
                return $this;
            } else if (in_array($formInput['type'], ['link', 'page'])) {
                $sort_order = \App\Models\EventInfoPage::where('event_id', '=', $formInput['event_id'])->max('sort_order') + 1;
                $page = new \App\Models\EventInfoPage();
                $page->event_id = $formInput['event_id'];
                $page->menu_id = $formInput['menu_id'];
                $page->page_type = $formInput['page_type'];
                $page->sort_order = $sort_order;
                $page->url = (isset($formInput['url']) ? $formInput['url'] : '');
                $page->website_protocol = (isset($formInput['website_protocol']) ? $formInput['website_protocol'] : '');
                $page->status = '1';
                if (isset($formInput['include_image']) && $formInput['include_image']) {
                    $page->image = (isset($formInput['image']) ? $formInput['image'] : '');
                    $page->image_position = (isset($formInput['image_position']) ? $formInput['image_position'] : 'top');
                }
                if (isset($formInput['include_pdf']) && $formInput['include_pdf']) {
                    $page->pdf = (isset($formInput['pdf']) ? $formInput['pdf'] : '');
                }
                $page->save();

                $fields = ($formInput['type'] == 'link' ? ['name'] : ['name', 'description', 'pdf_title']);

                $info = array();
                foreach ($fields as $field) {
                    foreach ($languages as $lang) {
                        if (isset($formInput[$field])) {
                            $info[] = new \App\Models\EventInfoPageInfo(array(
                                'name' => $field,
                                'value' => $formInput[$field],
                                'page_id' => $page->id,
                                'languages_id' => $lang,
                                'status' => 1
                            ));
                        }
                    }
                }
                $page->info()->saveMany($info);
            }
        } else if ($formInput['cms'] == "information-pages") {
            if ($formInput['type'] == 'menu') {
                foreach (['name'] as $field) {
                    foreach ($languages as $lang) {
                        if (isset($formInput[$field])) {
                            $info[] = new \App\Models\InformationSectionInfo(array(
                                'name' => $field,
                                'value' => trim($formInput[$field]),
                                'language_id' => $lang,
                                'section_id' => $menu->id,
                                'status' => 1
                            ));
                        }
                    }
                }
                $menu->info()->saveMany($info);
                return $this;
            } else if (in_array($formInput['type'], ['link', 'page'])) {
                $sort_order = \App\Models\InformationPage::where('event_id', '=', $formInput['event_id'])->max('sort_order') + 1;
                $page = new \App\Models\InformationPage();
                $page->event_id = $formInput['event_id'];
                $page->section_id = $formInput['menu_id'];
                $page->parent_id = $formInput['parent_id'];
                $page->page_type = $formInput['page_type'];
                $page->sort_order = $sort_order;
                $page->url = (isset($formInput['url']) ? $formInput['url'] : '');
                $page->website_protocol = (isset($formInput['website_protocol']) ? $formInput['website_protocol'] : '');
                $page->status = '1';
                if (isset($formInput['include_image']) && $formInput['include_image']) {
                    $page->image = (isset($formInput['image']) ? $formInput['image'] : '');
                    $page->image_position = (isset($formInput['image_position']) ? $formInput['image_position'] : 'top');
                }
                if (isset($formInput['include_pdf']) && $formInput['include_pdf']) {
                    $page->pdf = (isset($formInput['pdf']) ? $formInput['pdf'] : '');
                }

                $page->save();
                
                $fields = ($formInput['type'] == 'link' ? ['name'] : ['name', 'description', 'pdf_title']);

                $info = array();
                foreach ($fields as $field) {
                    foreach ($languages as $lang) {
                        if (isset($formInput[$field])) {
                            $info[] = new \App\Models\InformationPageInfo(array(
                                'name' => $field,
                                'value' => $formInput[$field],
                                'page_id' => $page->id,
                                'language_id' => $lang,
                                'status' => 1
                            ));
                        }
                    }
                }
                $page->info()->saveMany($info);
            }
        }
    }

    /**
     * Update data for cms
     *
     * @param array
     * @param string
     * @param string
     * @param int
     */

    public function edit($formInput, $type, $cms, $id)
    {
        if($cms == "information-pages" && $type == 'menu' && ((int) $formInput['menu_id'] > 0)){
            
            $model = \App\Models\InformationPageInfo::query();           
            $menu = $model->where('page_id', $id)->where('language_id', $formInput['language_id'])->where('name', 'name')->first();
            if ($menu) {

                $menu->value = $formInput['name'];
                $menu->save();

                if ($formInput['cms'] == "information-pages") {
                    \App\Models\InformationSection::where('id', $id)->update([
                        'show_in_app' => $formInput['showInApp'],
                        'show_in_reg_site' => $formInput['showInWebsite']
                    ]);
                }

                return array(
                    "status" => true,
                    "message" => __('messages.update')
                );

            } else {
                return array(
                    "status" => false,
                    "message" => __('messages.not_exist')
                );
            };
            
        }
        else{

            $instance = $this->setForm($formInput, $type, $cms);
            return $instance->updateInfo($id);
        }

    }

    /**
     * update info for cms
     * @param int
     *
     */
    public function updateInfo($id)
    {
        $formInput = $this->getFormInput();
        if ($formInput['type'] == 'menu') {
            $lang_col="languages_id";
            $menu_col="menu_id";
            if ($formInput['cms'] == "general-info") {
                $model = \App\Models\GeneralInfoMenuInfo::query();
            } else if ($formInput['cms'] == "additional-info") {
                $model = \App\Models\AdditionalInfoMenuInfo::query();
            } else if ($formInput['cms'] == "practical-info") {
                $model = \App\Models\EventInfoMenuInfo::query();
            } else if ($formInput['cms'] == "information-pages") {
                $model = \App\Models\InformationSectionInfo::query();
                $lang_col="language_id";
                $menu_col="section_id";
            }
            $menu = $model->where($menu_col, $id)->where($lang_col, $formInput['language_id'])->where('name', 'name')->first();
            if ($menu) {
                $menu->value = $formInput['name'];
                $menu->save();

                if ($formInput['cms'] == "information-pages") {
                    \App\Models\InformationSection::where('id', $id)->update([
                        'show_in_app' => $formInput['showInApp'],
                        'show_in_reg_site' => $formInput['showInWebsite']
                    ]);
                }

                return array(
                    "status" => true,
                    "message" => __('messages.update')
                );

            } else {
                return array(
                    "status" => false,
                    "message" => __('messages.not_exist')
                );
            }
        } else if (in_array($formInput['type'], ['link', 'page'])) {
            if ($formInput['cms'] == "general-info") {
                $page_model = \App\Models\GeneralInfoPage::query();
            } else if ($formInput['cms'] == "additional-info") {
                $page_model = \App\Models\AdditionalInfoPage::query();
            } else if ($formInput['cms'] == "practical-info") {
                $page_model = \App\Models\EventInfoPage::query();
            } else if ($formInput['cms'] == "information-pages") {
                $page_model = \App\Models\InformationPage::query();
            }
            $page = $page_model->where('id', $id)->first();
            if ($page) {
                $page->event_id = $formInput['event_id'];
                
                if($formInput['cms'] != "information-pages"){
                    $page->menu_id = $formInput['menu_id'];
                }else{
                    $page->section_id = $formInput['menu_id'];
                }
                $page->page_type = $formInput['page_type'];
                $page->url = (isset($formInput['url']) ? $formInput['url'] : '');
                $page->website_protocol = (isset($formInput['website_protocol']) ? $formInput['website_protocol'] : '');
                $page->status = '1';
                if (isset($formInput['include_image']) && $formInput['include_image']) {
                    $page->image = (isset($formInput['image']) ? $formInput['image'] : '');
                    $page->image_position = (isset($formInput['image_position']) ? $formInput['image_position'] : 'top');
                }
                if (isset($formInput['include_pdf']) && $formInput['include_pdf']) {
                    $page->pdf = (isset($formInput['pdf']) ? $formInput['pdf'] : '');
                }
                $page->save();

                $fields = ($formInput['type'] == 'link' ? ['name'] : ['name', 'description', 'pdf_title']);
                foreach ($fields as $field) {
                    if (isset($formInput[$field])) {
                        $lang_col="languages_id";
                        if ($formInput['cms'] == "general-info") {
                            $info_model = \App\Models\GeneralInfoPageInfo::query();
                        } else if ($formInput['cms'] == "additional-info") {
                            $info_model = \App\Models\AdditionalInfoPageInfo::query();
                        } else if ($formInput['cms'] == "practical-info") {
                            $info_model = \App\Models\EventInfoPageInfo::query();
                        } else if ($formInput['cms'] == "information-pages") {
                            $info_model = \App\Models\InformationPageInfo::query();
                            $lang_col="language_id";
                        }

                        $info = $info_model->where('page_id', $id)->where($lang_col, $formInput['language_id'])->where('name', $field)->first();
                        if ($info) {
                            $info->value = $formInput[$field];
                            $info->save();
                        } else {
                            if ($formInput['cms'] == "general-info") {
                                $info[] = new \App\Models\GeneralInfoPageInfo(array(
                                    'name' => $field,
                                    'value' => $formInput[$field],
                                    'page_id' => $page->id,
                                    'languages_id' => $formInput['language_id'],
                                    'status' => 1
                                ));
                            } else if ($formInput['cms'] == "additional-info") {
                                $info[] = new \App\Models\AdditionalInfoPageInfo(array(
                                    'name' => $field,
                                    'value' => $formInput[$field],
                                    'page_id' => $page->id,
                                    'languages_id' => $formInput['language_id'],
                                    'status' => 1
                                ));
                            } else if ($formInput['cms'] == "practical-info") {
                                $info[] = new \App\Models\EventInfoPageInfo(array(
                                    'name' => $field,
                                    'value' => $formInput[$field],
                                    'page_id' => $page->id,
                                    'languages_id' => $formInput['language_id'],
                                    'status' => 1
                                ));
                            } else if ($formInput['cms'] == "information-page") {
                                $info[] = new \App\Models\EventInfoPageInfo(array(
                                    'name' => $field,
                                    'value' => $formInput[$field],
                                    'page_id' => $page->id,
                                    'language_id' => $formInput['language_id'],
                                    'status' => 1
                                ));
                            }
                            $page->info()->saveMany($info);
                        }
                    }
                }

                return array(
                    "status" => true,
                    "message" => __('messages.update')
                );
            } else {
                return array(
                    "status" => false,
                    "message" => __('messages.not_exist')
                );
            }
        }
    }

    /**
     *Destroy cms data
     * @param string
     * @param string
     * @param int
     */

    public function destroy($type, $cms, $id)
    {
        if ($cms == "general-info") {
            if ($type == "menu") {
                \App\Models\GeneralInfoMenu::where('parent_id', '=', $id)->delete();
                \App\Models\GeneralInfoPage::where('menu_id', '=', $id)->delete();
                \App\Models\GeneralInfoMenu::where('id', '=', $id)->delete();
                \App\Models\GeneralInfoMenuInfo::where('menu_id', '=', $id)->delete();
            } else if (in_array($type, ['page', 'link'])) {
                \App\Models\GeneralInfoPage::where('id', '=', $id)->delete();
                \App\Models\GeneralInfoPageInfo::where('page_id', '=', $id)->delete();
            }
        } else if ($cms == "additional-info") {
            if ($type == "menu") {
                \App\Models\AdditionalInfoMenu::where('parent_id', '=', $id)->delete();
                \App\Models\AdditionalInfoPage::where('menu_id', '=', $id)->delete();
                \App\Models\AdditionalInfoMenu::where('id', '=', $id)->delete();
                \App\Models\AdditionalInfoMenuInfo::where('menu_id', '=', $id)->delete();
            } else if (in_array($type, ['page', 'link'])) {
                \App\Models\AdditionalInfoPage::where('id', '=', $id)->delete();
                \App\Models\AdditionalInfoPageInfo::where('page_id', '=', $id)->delete();
            }
        } else if ($cms == "practical-info") {
            if ($type == "menu") {
                \App\Models\EventInfoMenu::where('parent_id', '=', $id)->delete();
                \App\Models\EventInfoPage::where('menu_id', '=', $id)->delete();
                \App\Models\EventInfoMenu::where('id', '=', $id)->delete();
                \App\Models\EventInfoMenuInfo::where('menu_id', '=', $id)->delete();
            } else if (in_array($type, ['page', 'link'])) {
                \App\Models\EventInfoPage::where('id', '=', $id)->delete();
                \App\Models\EventInfoPageInfo::where('page_id', '=', $id)->delete();
            }
        } else if ($cms == "information-pages") {
            $mainSection = request()->mainSection;
            if ($mainSection == "true") {
                \App\Models\InformationSection::where('id', '=', $id)->delete();
                \App\Models\InformationSectionInfo::where('section_id', '=', $id)->delete();
                \App\Models\InformationPage::where('section_id', '=', $id)->delete();

            } else  {
                \App\Models\InformationPage::where('id', '=', $id)->delete();
                \App\Models\InformationPageInfo::where('page_id', '=', $id)->delete();
            }
        }

    }

    /**
     *listing cms pages
     * @param array
     * @param string
     * @param int
     */
    public function listing($formInput, $cms, $id)
    {

        if ($cms == "general-info") {
            $menu_model = \App\Models\GeneralInfoMenu::query();
            $page_model = \App\Models\GeneralInfoPage::query();
        } else if ($cms == "additional-info") {
            $menu_model = \App\Models\AdditionalInfoMenu::query();
            $page_model = \App\Models\AdditionalInfoPage::query();
        } else if ($cms == "practical-info") {
            $menu_model = \App\Models\EventInfoMenu::query();
            $page_model = \App\Models\EventInfoPage::query();
        }

        $menus = array();

        $menus_data = $menu_model->where('event_id', '=', $formInput['event_id'])->where('parent_id', '=', $id)
            ->with(['Info' => function ($query) use ($formInput) {
                return $query->where('languages_id', '=', $formInput['language_id']);
            }])
            ->get()
            ->toArray();

        foreach ($menus_data as $key => $menu) {
            $rowData = array();
            $infoData = readArrayKey($menu, $rowData, 'info');
            $rowData['name'] = isset($infoData['name']) ? $infoData['name'] : '';
            $menu['type'] = 'folder';
            $menus[$key] = $menu;
            $menus[$key]['detail'] = $rowData;
        }

        $pages = array();

        $pages_data = $page_model->where('menu_id', '=', $id)->where('event_id', '=', $formInput['event_id'])
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', '=', $formInput['language_id']);
            }])
            ->get()
            ->toArray();

        foreach ($pages_data as $key => $page) {
            $rowData = array();
            $infoData = readArrayKey($page, $rowData, 'info');
            $rowData['name'] = isset($infoData['name']) ? $infoData['name'] : '';
            $rowData['description'] = isset($infoData['description']) ? $infoData['description'] : '';
            $rowData['pdf_title'] = isset($infoData['pdf_title']) ? $infoData['pdf_title'] : '';
            $pages[$key] = $page;
            $pages[$key]['type'] = ($page['page_type'] == 1 ? 'page' : 'link');
            $pages[$key]['detail'] = $rowData;
        }

        $response = array();

        if (!empty($menus) && !empty($pages)) {
            $response = array_merge($menus, $pages);
        } else if (!empty($menus)) {
            $response = $menus;
        } else if (!empty($pages)) {
            $response = $pages;
        }

        usort($response, array($this, "sortBySortOrder"));

        return $response;
    }

    /**
     *sort listing results
     * @param array
     * @param array
     */

    function sortBySortOrder($a, $b)
    {
        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }
        return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
    }

    /**
     *update sort order
     * @param string
     * @param array
     */

    function updateOrder($cms, $list)
    {
        if (!empty($list)) {
            $sort = 0;
            foreach ($list as $row) {
                if($cms !== 'information-pages'){
                    if ($cms == "general-info") {
                        $menu_model = \App\Models\GeneralInfoMenu::query();
                        $page_model = \App\Models\GeneralInfoPage::query();
                    } else if ($cms == "additional-info") {
                        $menu_model = \App\Models\AdditionalInfoMenu::query();
                        $page_model = \App\Models\AdditionalInfoPage::query();
                    } else if ($cms == "practical-info") {
                        $menu_model = \App\Models\EventInfoMenu::query();
                        $page_model = \App\Models\EventInfoPage::query();
                    }
                    if ($row['type'] == "folder") {
                        $menu = $menu_model->find($row['id']);
                        $menu->sort_order = $sort;
                        $menu->save();
                    } else {
                        $page = $page_model->find($row['id']);
                        $page->sort_order = $sort;
                        $page->save();
                    }
                }
                else{

                    if ($row['mainSection'] == "true") {
                        $model = \App\Models\InformationSection::query();
                    }else{
                        $model = \App\Models\InformationPage::query();
                    }
                    $menu = $model->find($row['id']);
                    $menu->sort_order = $sort;
                    $menu->save();
                     
                }

                $sort++;
            }
        }

        return true;
    }

    public function getFrontMenus($formInput, $cms,  $menu_id)
    {
        $event_id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];

        if ($cms == "general-info") {
            $menu_model = \App\Models\GeneralInfoMenu::class;
            $page_model = \App\Models\GeneralInfoPage::class;
        } else if ($cms == "additional-info") {
            $menu_model = \App\Models\AdditionalInfoMenu::class;
            $page_model = \App\Models\AdditionalInfoPage::class;
        } else if ($cms == "practical-info") {
            $menu_model = \App\Models\EventInfoMenu::class;
            $page_model = \App\Models\EventInfoPage::class;
        }

        $menus = $menu_model::where('event_id', $event_id)->where('parent_id', $menu_id)->with([
            'info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); },
        ])->orderBy('sort_order')->get();

        $pages = $page_model::where('event_id', $event_id)->where('menu_id', $menu_id)->with([
            'info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); },
        ])->orderBy('sort_order')->get();

        $concatenated  = $menus->concat($pages)->sortBy('sort_order');

        foreach ($concatenated as $menu){
            if($menu instanceof GeneralInfoMenu || $menu instanceof AdditionalInfoMenu || $menu instanceof EventInfoMenu ){
                $menu->type = 'folder';
            }
            elseif($menu instanceof GeneralInfoPage || $menu instanceof AdditionalInfoPage || $menu instanceof EventInfoPage ){
                $menu->type = 'page';
            }
        }
        return $concatenated;
    }
    
    
    public function getPageData($formInput, $cms,  $id)
    {
        $event_id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];

        if ($cms == "general-info") {
            $page_model = \App\Models\GeneralInfoPage::class;
        } else if ($cms == "additional-info") {
            $page_model = \App\Models\AdditionalInfoPage::class;
        } else if ($cms == "practical-info") {
            $page_model = \App\Models\EventInfoPage::class;
        }

        $page = $page_model::where('event_id', $event_id)->where('id', $id)->with([
            'info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); },
        ])->first();

        foreach ($page['info'] as $key => $value) {
            $page[$value['name']] = $value['value'];
        }
        
        unset($page['info']);

        return $page;
    }

}
