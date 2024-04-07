<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

use App\Models\EventMap;

use App\Eventbuizz\Repositories\EventRepository;

class MapRepository extends AbstractRepository
{
    protected $model;

    public function __construct(Request $request, EventMap $model)
    {
        $this->request = $request;
        $this->model = $model;
    }

    /**
     *Event map info clone/default
     *
     * @param array
     */
    public function install($request)
    {
        if ($request["content"]) {
            //event map 
            $from_event_maps = \App\Models\EventMap::where("event_id", $request['from_event_id'])->get();
            if ($from_event_maps) {
                foreach ($from_event_maps as $from_event_map) {
                    $to_event_map = $from_event_map->replicate();
                    $to_event_map->event_id = $request['to_event_id'];
                    $to_event_map->save();

                    //info 
                    $from_event_map_info = \App\Models\MapInfo::where("map_id", $from_event_map->id)->get();
                    foreach ($from_event_map_info as $from_info) {
                        $info = $from_info->replicate();
                        $info->map_id = $to_event_map->id;
                        $info->languages_id = $request["languages"][0];
                        $info->save();
                    }
                }
            }
        }
    }

    /**
     * set form values
     *
     * @param array
     */

    public function setCreateForm($formInput)
    {
        if ($formInput['google_map'] == '1') {
            if (strpos($formInput['url'], 'iwloc=addr') !== false) {
                $url = str_replace('iwloc=addr', 'iwloc=near', $formInput['url']);
            } elseif (strpos($formInput['url'], 'iwloc=A') !== false) {
                $url = str_replace('iwloc=A', 'iwloc=near', $formInput['url']);
            } else {
                $url = $formInput['url'];
            }
            $formInput['url'] = $url;
        }
        $formInput['status'] = '1';
        $this->setFormInput($formInput);
        return $this;
    }

    /**
     * save data
     *
     * @param array
     */
    public function store($formInput)
    {
        $formInput = get_trim_all_data($formInput);
        $instance = $this->setCreateForm($formInput);
        $instance->create();
        $instance->insertInfo();
        $instance->uploadImage();
        EventRepository::add_module_progress($formInput, "map");
        return $this->getObject();
    }

    /**
     * upload image
     *
     * @param array
     */
    public function uploadImage()
    {
        $formInput = $this->getFormInput();

        $map_object = $this->getObject();

        $info = \App\Models\MapInfo::where('map_id', $map_object->id)->where('name', 'image')->first();

        if ($formInput['google_map'] == '0' && $formInput['image']) {
            $image = 'event_map_' . time() . '.' . $formInput['image']->getClientOriginalExtension();
            $formInput['image']->storeAs('/assets/maps', $image);

            //clone image
            moveFile(
                storage_path('app/assets/maps/' . $image),
                config('cdn.cdn_upload_path') . 'assets/maps/' . $image
            );

            if ($info) {
                //unset image if exist
                if ($info->value && file_exists(config('cdn.cdn_upload_path') . 'assets/maps/' . $info->value)) {
                    deleteFile(config('cdn.cdn_upload_path') . 'assets/maps/' . $info->value);
                }

                $info->value = $image;
                $info->save();
            } else {
                \App\Models\MapInfo::create([
                    "map_id" => $map_object->id,
                    "name" => "image",
                    "value" => $image,
                    "languages_id" => $formInput['language_id'],
                    "status" => 1
                ]);
            }
        } else if ($info) {
            $info->value = "";
            $info->save();
        }

        return $this;
    }

    /**
     * save info
     *
     * @param array
     */
    public function insertInfo()
    {
        $formInput = $this->getFormInput();
        $languages = get_event_languages($formInput['event_id']);
        $info_fields = array('url');
        $map_object = $this->getObject();
        foreach ($info_fields as $field) {
            foreach ($languages as $language_id) {
                $info[] = new \App\Models\MapInfo(array(
                    'name' => $field,
                    'value' => (isset($formInput[$field]) ? $formInput[$field] : ''),
                    'map_id' => $map_object->id,
                    'languages_id' => $language_id,
                    'status' => 1
                ));
            }
        }
        $map_object->info()->saveMany($info);
        return $this;
    }

    /**
     * update data
     *
     * @param array
     * @param object
     */
    public function edit($formInput, $map)
    {
        $formInput = get_trim_all_data($formInput);
        $instance = $this->setCreateForm($formInput);
        $instance->update($map);
        $instance->updateInfo($map);
        $instance->uploadImage();
    }

    /**
     * update info
     *
     * @param object
     */
    public function updateInfo($map)
    {
        $formInput = $this->getFormInput();
        $info_fields = array('url');
        foreach ($info_fields as $field) {
            \App\Models\MapInfo::where('map_id', $map->id)->where('languages_id', $formInput['language_id'])
                ->where('name', $field)->update(array('value' => (isset($formInput[$field]) ? $formInput[$field] : '')));
        }
        return $this;
    }

    public function fetch($event_id, $language_id)
    {
        $response = [];
        $eventMap = EventMap::where('event_id', $event_id)->first();
        if ($eventMap) {
            $response['id'] = $eventMap->id;
            $response['google_map'] = $eventMap->google_map;
            $mapInfo = $eventMap->info()->where('languages_id', $language_id)->get();
            foreach ($mapInfo as $info) {
                if (!empty($info->value)) {
                    if ($info->name == "url") $response['url'] = $info->value;
                    if ($info->name == "image") $response['image'] = cdn('assets/maps/' . $info->value);
                }
            }
        }
        return $response;
    }

    public function getMap($formInput)
    {
        $event_id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];
       return EventMap::with([ 'info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); },])
           ->where('event_id', $event_id)
           ->first();
    }
}
