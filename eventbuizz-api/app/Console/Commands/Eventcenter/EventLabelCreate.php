<?php

namespace App\Console\Commands\Eventcenter;

use Illuminate\Console\Command;

use App\Models\Event;

class EventLabelCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Create:Eventlabels {offset} {limit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating missing labels of event';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $offset = $this->argument('offset');
        $limit = $this->argument('limit');
        if($offset && $limit) {
            $labels = \App\Models\Label::where('parent_id', '=', '0')
                        ->with(['info'])
                        ->with(['children' => function ($r) {
                            return $r->orderBy('constant_order');
                        }, 'children.childrenInfo'])
                        ->orderBy('section_order')
                        ->get(); 
            $query = Event::where('id', '>=', $offset)->where('id', '<=', $limit)->whereNull('deleted_at');
            $events = $query->get();
            foreach ($events as $event) {
                $event_id = $event->id;
                foreach ($labels as $master_label) {
                    $parent_label = \App\Models\EventSiteText::where('alias', '=', $master_label->alias)->where('module_alias', '=', $master_label->module_alias)->where('event_id', '=', $event_id)->first();
                    if (!$parent_label) {
                        $formInput = array();
                        $formInput['event_id'] = $event_id;
                        $formInput['section_order'] = $master_label->section_order;
                        $formInput['constant_order'] = $master_label->constant_order;
                        $formInput['alias'] = $master_label->alias;
                        $formInput['module_alias'] = $master_label->module_alias;
                        $formInput['label_parent_id'] = $master_label->id;
                        $formInput['parent_id'] = 0;
                        $formInput['status'] = 1;
                        $formInput['created_at'] = \Carbon\Carbon::now();
                        $formInput['updated_at'] = \Carbon\Carbon::now();
                        $parent_label = \App\Models\EventSiteText::create($formInput);
                    }
                    $master_label_infos = array_filter($master_label['info']->toArray(), function($val) use($event) {
                        return $val['languages_id'] == $event->language_id;
                    });
                    foreach ($master_label_infos as $info) {
                        $text_info = \App\Models\EventSiteTextInfo::where('text_id', '=', $parent_label->id)->where('languages_id', '=', $info['languages_id'])->first();
                        if (!$text_info) {
                            $formInput = array();
                            $formInput['name'] = $info['name'];
                            $formInput['value'] = $info['value'];
                            $formInput['text_id'] = $parent_label->id;
                            $formInput['languages_id'] = $info['languages_id'];
                            $formInput['status'] = 1;
                            $formInput['created_at'] = \Carbon\Carbon::now();
                            $formInput['updated_at'] = \Carbon\Carbon::now();
                            \App\Models\EventSiteTextInfo::create($formInput);
                        }
                    }
                    
                    foreach ($master_label['children'] as $children) {
                        $child_label = \App\Models\EventSiteText::where('alias', '=', $children->alias)->where('module_alias', '=', $children->module_alias)->where('event_id', '=', $event_id)->first();
                        if (!$child_label) {
                            $formInput = array();
                            $formInput['event_id'] = $event_id;
                            $formInput['section_order'] = $children->section_order;
                            $formInput['constant_order'] = $children->constant_order;
                            $formInput['alias'] = $children->alias;;
                            $formInput['module_alias'] = $children->module_alias;
                            $formInput['label_parent_id'] = $children->id;
                            $formInput['parent_id'] = $parent_label->id;
                            $formInput['status'] = 1;
                            $formInput['created_at'] = \Carbon\Carbon::now();
                            $formInput['updated_at'] = \Carbon\Carbon::now();
                            $child_label = \App\Models\EventSiteText::create($formInput);
                        }
                        $children_label_infos = array_filter($children['childrenInfo']->toArray(), function($val) use($event) {
                            return $val['languages_id'] == $event->language_id;
                        });
                        foreach ($children_label_infos as $child_info) {
                            $child_text_info = \App\Models\EventSiteTextInfo::where('text_id', '=', $child_label->id)->where('languages_id', '=', $child_info['languages_id'])->first();
                            if (!$child_text_info) {
                                $formInput = array();
                                $formInput['name'] = $child_info['name'];
                                $formInput['value'] = $child_info['value'];
                                $formInput['text_id'] = $child_label['id'];
                                $formInput['languages_id'] = $child_info['languages_id'];
                                $formInput['status'] = 1;
                                $formInput['created_at'] = \Carbon\Carbon::now();
                                $formInput['updated_at'] = \Carbon\Carbon::now();
                                \App\Models\EventSiteTextInfo::create($formInput);
                            }
                        }
                    }
                }
            }
        }
        
        $this->info('Event missing labels created successfully!');
    }
}
