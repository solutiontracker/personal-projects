<?php
namespace App\Eventbuizz\Repositories;

use App\Models\RegistrationFormTheme;
use App\Models\RegistrationThemeSetting;
use Illuminate\Http\Request;

class EventThemeRepository extends AbstractRepository
{
    /**
     * Event installation / Event cloning
     *
     * @param array
     */
    public function install($request)
    {
        $event_id = $request['to_event_id'];

        $from_event_id = $request['from_event_id'];

        $fromEvent = \App\Models\Event::where('id', $from_event_id)->first();

        // event theme module variations
        $themeModuleVariations = \App\Models\EventThemeModuleVariation::where('event_id', $from_event_id)->where('theme_id', $fromEvent->registration_site_theme_id)->get();
       
        // event theme layout
        foreach ($themeModuleVariations as $key => $value) {
            \App\Models\EventThemeModuleVariation::create([
                'event_id' => $event_id, 
                'theme_id' => $value->theme_id, 
                "alias" => $value->alias, 
                "module_name" => $value->module_name, 
                "variation_name" => $value->variation_name, 
                "variation_slug" => $value->variation_slug, 
                "text_align" => $value->text_align, 
                "background_image" => $value->background_image,
            ]);
        }

        $layoutSections = \App\Models\EventLayoutSection::where('event_id', $from_event_id)->where('layout_id', $fromEvent->registration_site_layout_id)->get();

        foreach ($layoutSections as $key => $value) {
            \App\Models\EventLayoutSection::create([
                'event_id' => $event_id,
                'layout_id' => $value->layout_id, 
                "variation_slug" => $value->variation_slug, 
                "module_alias" => $value->module_alias, 
                "status" => $value->status, 
                "sort_order" => $value->sort_order
            ]);
        }

        //Registration flow theme settings
        $theme_settings = \App\Models\RegistrationThemeSetting::where('event_id', $from_event_id)->get();
        foreach($theme_settings as $theme_setting) {
            $new_theme_setting = $theme_setting->replicate();
            $new_theme_setting->event_id = $request['to_event_id'];
            $new_theme_setting->save();
        }

        if ($request["content"]) {

            // event Contact Persons
            $event_contact_person  = \App\Models\EventContactPerson::where('event_id', $from_event_id)->get();
            foreach ($event_contact_person as $key => $value) {
                \App\Models\EventContactPerson::create([
                    'event_id' => $event_id,
                    'first_name' => $value->first_name, 
                    "last_name" => $value->last_name, 
                    "email" => $value->email, 
                    "phone" => $value->phone, 
                ]);
            }

            // Event opening hours 
            $event_opening_hours = \App\Models\EventOpeningHour::where('event_id', $from_event_id)->get();
            foreach ($event_opening_hours as $key => $value) {
                \App\Models\EventOpeningHour::create([
                    'event_id' => $event_id,
                    'date' => $value->date, 
                    "start_time" => $value->start_time, 
                    "end_time" => $value->end_time, 
                ]);
            }


        }
        
    }
    
}
