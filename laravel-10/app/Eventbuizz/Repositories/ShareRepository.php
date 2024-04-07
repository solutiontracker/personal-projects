<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class ShareRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function install($request)
    {
        $data['template'] = 'EventBuizz is your entire event gathered in a single app. Available for all mobile platforms. Weve pioneered a dynamic tool that delights audiences by enhancing the way they network, receive program updates in real time, schedule activities, and engage with content.
            Event organisers and meeting planners reach this audience through a customised, branded user interface that optimises the complete lifecycle of events such as conferences, conventions, trade shows, or employee training seminars. Before, During and After: From the first invitation is sent, to the last evaluation is received, everything is handled through EventBuizz.
        For more information please go to: https://www.eventbuizz.com/
        Best regards,
        Eventbuizz';

        $data['subject'] = 'Share this with your colleague';

        $infoFields = array('subject', 'template');

        $event_share = \App\Models\EventShareTemplate::where('event_id', '=', $request['to_event_id'])->first();
        if (!$event_share) {
            $model_object_settings = new \App\Models\EventShareTemplate();
            $model_object_settings->event_id  = $request['to_event_id'];
            $model_object_settings->save();
        } else {
            $model_object_settings = \App\Models\EventShareTemplate::find($event_share->id);
        }

        $setting = array();
        $setting['event_id'] = $request['to_event_id'];

        $event_share_info = \App\Models\EventShareTemplateInfo::where('template_id', '=', $model_object_settings->id)->get();
        foreach ($request['languages'] as $lang) {
            $info = array();
            $found = false;
            foreach ($event_share_info as $temp) {
                if ($temp['languages_id'] == $lang) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                foreach ($infoFields as $field) {
                    $info[] = new \App\Models\EventShareTemplateInfo(array('name' => $field, 'value' => $data[$field], 'languages_id' => $lang, 'status' => 1));
                }
            }
        }

        $model_object_settings->info()->saveMany($info);
    }
}
