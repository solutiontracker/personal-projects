<?php

namespace App\Console\Commands\EventSiteTheme;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Helpers\DynamicsCRM\DynamicsContactHelper;
use App\Models\AddAttendeeLog;
use App\Models\DynamicsToken;
use Illuminate\Console\Command;

class EventThemeModuleVariations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:eventModuleVariations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add all the Variations From Master to event specific table';

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
        // irfan
        di();
        $events = \App\Models\Event::where('theme_cron_status', 0)->whereNull("deleted_at")->get();

        foreach ($events as $key => $event) {

                // event theme module variations
                $themeModuleVariations = \App\Models\ThemeModuleVariation::where('theme_id', $event->registration_site_theme_id)->get();
                // event theme layout
                foreach ($themeModuleVariations as $key => $value) {
                    $exists = \App\Models\EventThemeModuleVariation::where('event_id', $event->id)
                                ->where('theme_id', $value->theme_id)
                                ->where('alias', $value->alias)
                                ->where('module_name', $value->module_name)
                                ->where('variation_name', $value->variation_name)
                                ->where('variation_slug', $value->variation_slug)
                                ->first();
                    if(!$exists){
                        \App\Models\EventThemeModuleVariation::create([
                            'event_id' => $event->id, 
                            'theme_id' => $value->theme_id, 
                            "alias" => $value->alias, 
                            "module_name" => $value->module_name, 
                            "variation_name" => $value->variation_name, 
                            "variation_slug" => $value->variation_slug, 
                            "text_align" => "center", 
                            "background_image" => "",
                        ]);
                    }
                }
    
            $layouts = \App\Models\ThemeLayout::where('theme_id', $event->registration_site_theme_id)->get();

            foreach ($layouts as $key => $layout) {
                
                $layoutSections = \App\Models\LayoutSection::where('layout_id', $layout->id)->get();
    
                foreach ($layoutSections as $key => $value) {
                    $exists = \App\Models\EventLayoutSection::where('event_id', $event->id)
                                ->where('layout_id', $value->layout_id)
                                ->where('variation_slug', $value->variation_slug)
                                ->where('module_alias', $value->module_alias)
                                ->first();
                    if(!$exists){
                        \App\Models\EventLayoutSection::create([
                            'event_id' => $event->id,
                            'layout_id' => $value->layout_id, 
                            "variation_slug" => $value->variation_slug, 
                            "module_alias" => $value->module_alias, 
                            "status" => $value->status, 
                            "sort_order" => $value->sort_order
                        ]);
                    }
                }
        
            }

            $event_description = \App\Models\EventDescription::where('event_id', $event->id)->first();

            if($event_description){
                $description =  \App\Models\EventDescriptionInfo::where('description_id', $event_description->id)->where('name', 'description')->where('languages_id',  $event->language_id)->first();
                if($description){
                    $customHtml = \App\Models\EventCustomHtml::where('event_id', $event->id)->first();
                    if($customHtml){
                        \App\Models\EventCustomHtml::where('event_id', $event->id)->update([
                            "custom_html_3" => $description->value
                        ]);
                    }
                    else{
                        \App\Models\EventCustomHtml::create([
                            'event_id'=> $event->id,
                            "custom_html_3" => $description->value
                        ]);
                    }
                }
            }


            \App\Models\Event::where('id', $event->id)->update(['theme_cron_status' => 1 ]);
        }

        $this->info("Themes and variations synced.");
        exit;
    }
}
