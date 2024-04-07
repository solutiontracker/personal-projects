<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class EventBrandingRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     *EventsiteBranding clone/default
     *
     * @param array
     */
    public function install($request)
    {
        $setting = \App\Models\EventsiteBranding::where('event_id', $request['from_event_id'])->get();

        if (count($setting)) {
            foreach ($setting as $record) {
                $record = $record->replicate();
                $record->event_id = $request['to_event_id'];
                $record->save();
            }
        } else {
            $settings = array(
                'site_logo' => '', 'eventsite_register_button' => '#F28121',
                'eventsite_other_buttons' => '#69C7CF'
            );
            $model = new \App\Models\EventsiteBranding();
            if (count($settings) > 0) {
                $setting = array();
                $setting['event_id'] = $request['to_event_id'];
                foreach ($settings as $name => $value) {
                    $setting[$name] = $value;
                }
                $model->create($setting);
            }
        }
    }
}
