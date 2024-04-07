<?php

    namespace App\Http\Resources;

    use App\Eventbuizz\Repositories\EventSiteSettingRepository;
    use Illuminate\Http\Resources\Json\JsonResource;
    use App\Http\Resources\EventDescription as EventDescriptionResource;
    use App\Http\Resources\EventsiteSetting as EventsiteSettingResource;
    class Event extends JsonResource
    {
        /**
         * Transform the resource into an array.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function toArray($request)
        {
            $event = [];

            if ($this->resource) {
                $event = [
                    'id' => $this->id,
                    'name' => $this->name,
                    'url' => $this->url,
                    'organizer_name' => $this->organizer_name,
                    'tickets_left' => $this->tickets_left,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'status' => $this->status,
                    'language_id' => $this->language_id,
                    'timezone_id' => $this->timezone_id,
                    'country_id' => $this->country_id,
                    'country' => $this->country,
                    'office_country_id' => $this->office_country_id,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'owner_id' => $this->owner_id,
                    'export_setting' => $this->export_setting,
                    'show_native_app_link' => $this->show_native_app_link,
                    'organizer_site' => $this->organizer_site,
                    'native_app_acessed_date' => $this->native_app_acessed_date,
                    'native_app_timer' => $this->native_app_timer,
                    'white_label_sender_name' => $this->white_label_sender_name,
                    'white_label_sender_email' => $this->white_label_sender_email,
                    'is_template' => $this->is_template,
                    'is_advance_template' => $this->is_advance_template,
                    'is_wizard_template' => $this->is_wizard_template,
                    'type' => $this->type,
                    'is_registration' => $this->is_registration,
                    'registration_form_id' => $this->registration_form_id,
                    'is_app' => $this->is_app,
                    'info' => $this->when($this->info, $this->info),
                    'registration_end_date_passed'=> $this->registration_end_date_passed,
                    'registration_form_info' => $this->registration_form_info,
                    'description' => $this->when($this->description,new EventDescriptionResource($this->description)),
                    'settings' => $this->when($this->settings, $this->settings),
                    'eventsiteSettings' => $this->when($this->eventsiteSettings, new EventsiteSettingResource($this->eventsiteSettings)),
                    'eventsiteModules' => $this->when($this->eventsiteModules, new EventsiteModuleCollection($this->eventsiteModules)),
                    'eventsiteSections' => $this->when($this->eventsiteSections, EventsiteSection::collection($this->eventsiteSections)),
                    'speaker_settings' => $this->when($this->speaker_settings, $this->speaker_settings),
                    'sponsor_settings' => $this->when($this->sponsor_settings, $this->sponsor_settings),
                    'exhibitor_settings' => $this->when($this->exhibitor_settings,$this->exhibitor_settings),
                    'attendee_settings' => $this->when($this->attendee_settings, $this->attendee_settings),
                    'agenda_settings' => $this->when($this->agenda_settings, $this->agenda_settings),
                    'news_settings' => $this->when($this->news_settings, $this->news_settings),
                    'paymentSettings' => $this->when($this->paymentSettings, $this->paymentSettings),
                    'newsletter_subcription_form_settings' => $this->when($this->newsletter_subcription_form_settings, $this->newsletter_subcription_form_settings),
                    'theme' => $this->when($this->registration_site_theme, new Theme($this->registration_site_theme)),
                    'header_data'=> $this->when($this->header_data, $this->header_data),
                    'labels'=> $this->when($this->labels, $this->labels),
                    'layoutSections'=> $this->when($this->layoutSections, $this->layoutSections),
                    'moduleVariations'=> $this->when($this->moduleVariations, $this->moduleVariations),
                    'socialMediaShare'=> $this->when($this->socialMediaShare, $this->socialMediaShare),
                    'totalAttendees'=> $this->totalAttendees,
                    'waitinglistSettings'=> $this->when($this->waitinglistSettings, $this->waitinglistSettings),
                    'customSection1'=> $this->when($this->customSection1, $this->customSection1),
                    'customSection2'=> $this->when($this->customSection2, $this->customSection2),
                    'customSection3'=> $this->when($this->customSection3, $this->customSection3),
                    'eventContactPersons'=> $this->when($this->eventContactPersons, $this->eventContactPersons),
                    'eventOpeningHours'=> $this->when($this->eventOpeningHours, $this->eventOpeningHours),
                    'disclaimer'=> $this->when($this->disclaimer, $this->disclaimer),
                    'interface_labels'=> $this->when($this->interface_labels, $this->interface_labels),
                    'timezone'=> $this->when($this->timezone, $this->timezone),
                ];
            }
            return $event;
        }
    }
