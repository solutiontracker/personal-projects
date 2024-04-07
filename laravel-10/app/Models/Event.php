<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $table = 'conf_events';

    protected $attributes = [
        'organizer_site' => '0',
        'status' => '1',
        'latitude' => '0',
        'longitude' => '0',
        'show_native_app_link' => '0',
        'tickets_left' => '',
        'cancellation_date' => '0000-00-00 00:00:00',
        'registration_end_date' => '0000-00-00 00:00:00',
        'type' => '0',
    ];

    protected $fillable = [
        'name', 'organizer_name', 'url', 'start_date', 'end_date', 'start_time', 'end_time', 'tickets_left',
        'cancellation_date', 'registration_end_date', 'organizer_id', 'status', 'language_id', 'timezone_id', 'country_id', 'office_country_id', 'latitude', 'longitude', 'owner_id', 'show_native_app_link', 'organizer_site', 'native_app_timer', 'native_app_acessed_date', 'type', 'is_registration', 'is_app', 'is_map', 'template_id', 'end_event_total_attendee_count','tags','contact_person_name','phone','email', 'registration_flow_theme_id', 'registration_form_id'
    ];

    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\EventInfo', 'event_id');
    }

    public function languages()
    {
        return $this->belongsToMany('\App\Models\Language', 'conf_event_languages', 'event_id', 'language_id');
    }

    public function attendees()
    {
        return $this->belongsToMany('\App\Models\Attendee', 'conf_event_attendees', 'event_id', 'attendee_id')
            ->withPivot('is_active', 'created_at')->whereNull('conf_event_attendees.deleted_at')->groupby('conf_event_attendees.attendee_id');
    }

    public function orderAttendees()
    {
        return $this->belongsToMany('\App\Models\Attendee', 'conf_event_attendees', 'event_id', 'attendee_id')
            ->withPivot('is_active', 'created_at')->groupby('conf_event_attendees.attendee_id');
    }

    public function settings()
    {
        return $this->hasMany('\App\Models\EventSetting', 'event_id');
    }

    public function attendee_settings()
    {
        return $this->hasOne('\App\Models\AttendeeSetting', 'event_id');
    }
    
    public function speaker_settings()
    {
        return $this->hasOne('\App\Models\SpeakerSetting', 'event_id');
    }
    
    public function sponsor_settings()
    {
        return $this->hasOne('\App\Models\SponsorSetting', 'event_id');
    }
    
    public function exhibitor_settings()
    {
        return $this->hasOne('\App\Models\ExhibitorSetting', 'event_id');
    }

    public function agenda_settings()
    {
        return $this->hasOne('\App\Models\AgendaSetting', 'event_id');
    }
    
    
    public function news_settings()
    {
        return $this->hasOne('\App\Models\EventNewsSetting', 'event_id');
    }

    public function sub_admins()
    {
        return $this->belongsToMany(Organizer::class, 'conf_subadmin_events', 'event_id', 'admin_id');
    }

    public function eventsiteNewsSubscriberSettings()
    {
        return $this->hasMany('\App\Models\EventNewsSubscriberSetting', 'event_id');
    }

    public function eventsitePaymentsettings()
    {
        return $this->hasMany('\App\Models\EventsitePaymentSetting', 'event_id');
    }

    public function eventsiteModules()
    {
        return $this->hasMany(EventSiteModuleOrder::class, 'event_id')->where('status', 1)->orderBy('sort_order')->withoutGlobalScopes();
    }

    public function eventsiteSettings(){
        return $this->hasOne(EventsiteSetting::class, 'event_id')->withoutGlobalScopes();
    }

    public function theme()
    {
        return $this->belongsToMany(Theme::class, 'conf_event_themes', 'event_id', 'theme_id')->wherePivot('status', 1);
    }

    public function description()
    {
        return $this->hasOne(EventDescription::class, 'event_id');
    }
    
    public function gdprSettings()
    {
        return $this->hasOne(EventGdprSetting::class, 'event_id');
    }

    public function eventsiteSections(){
        return $this->hasMany(EventsiteSection::class, 'event_id')->where('status', 1)->orderBy('sort_order')->orderBy('id');
    }

    public function organizer()
    {
        return $this->hasOne('\App\Models\Organizer', 'id', 'organizer_id');
    }

    public function registration_flow_theme()
    {
        return $this->hasOne(EventRegistrationFlowTheme::class, 'id', 'registration_flow_theme_id');
    }
    
    public function registration_site_theme()
    {
        return $this->hasOne(Theme::class, 'id', 'registration_site_theme_id');
    }

    public function timezone()
    {
        return $this->hasOne(Timezone::class, 'id', 'timezone_id');
    }

}
