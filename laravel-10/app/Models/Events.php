<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Events extends Model {

    use SoftDeletes;
    protected $table = 'conf_events';
    protected $fillable = ['name','organizer_name','url','start_date','end_date','start_time','end_time','tickets_left',
        'cancellation_date','registration_end_date','organizer_id','status','language_id','timezone_id','country_id','office_country_id','latitude','longitude','ean_number',
        'owner_id', 'show_native_app_link', 'organizer_site','native_app_timer', 'native_app_acessed_date', 'is_template', 'is_advance_template', 'is_wizard_template', 'is_registration', 'is_app',
        'allow_all_qualities','enable_cloud_proxy','enable_storage', 'parent_event_id', 'registration_type'];
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany(EventInfo::class,'event_id');
    }

    public function owner()
    {
        return $this->belongsTo('Organizer','owner_id','id');
    }

    public function themes(){
        return $this->belongsToMany(Theme::class, 'conf_event_themes', 'event_id', 'theme_id')->withPivot('status');
    }

    public function themeModules(){
        return $this->hasMany(EventThemeModule::class, 'event_id', 'id');
    }

    public function languages()
    {
        return $this->belongsToMany('Language','conf_event_languages','event_id','language_id');
    }

    public function attendees()
    {
        return $this->belongsToMany('Attendees','conf_event_attendees','event_id','attendee_id')->withPivot('is_active')->whereNull('conf_event_attendees.deleted_at')->groupby('conf_event_attendees.attendee_id');
    }

    public function sponsors()
    {
        return $this->hasMany('\Models\Sponsors','event_id');
    }
    public function analytics_requests()
    {
        return $this->hasOne('\Models\AnalyticsRequests','event_code');
    }

    public function exhibitors()
    {
        return $this->hasMany('\Models\Exhibitors','event_id');
    }

    public function attendee_info()
    {
        return $this->hasMany('AttendeeInfo','attendee_id');
    }

	public function usedPackage()
	{
		return $this->hasMany('AssignPackageUsed','event_id');
	}

	public function assignPackage()
	{
		return $this->belongsTo('AssignPackages', 'organizer_id', 'organizer_id');
	}


	public function extendEvent()
	{
		return $this->belongsTo('ExtendEvent', 'event_id', 'id');
	}

    public function organizer_info()
    {
        return $this->belongsTo('Organizer', 'organizer_id');
    }

    public function floorPlan()
    {
        return $this->hasOne('FloorPlan', 'event_id');
    }

    public function orders()
    {
        return $this->hasMany('EventsiteBillingOrders','event_id');
    }

    public function ordersCount()
    {

        return $this->orders()
            ->selectRaw('event_id, count(*) as aggregate')->where('order_type','=','invoice')->where('e_invoice','=','1')->groupBy('event_id');


    }

    public function ordersAttendee()
    {
        //return $this->hasMany('Attendees','attendee_id');
    }

    public function settings()
    {
        return $this->hasMany('EventSettings','event_id');
    }

    public function eventsitesettings()
    {
        return $this->hasMany('EventsiteSettings','event_id');
    }

    public function eventsitePaymentsettings()
    {
        return $this->hasMany('EventsitePaymentSettings','event_id');
    }

    public function eventsiteNewsSubscriberSettings()
    {
        return $this->hasMany('NewsSubscriberSettings','event_id');
    }

    public function modules() {
        return $this->hasMany('ModuleOrder','event_id');
    }

    public function badgesDesign()
    {
        return $this->hasMany('\Models\BadgesPrinterDesign', 'event_id');
    }

    /**
     * @return mixed
     */
    public function timeZone()
    {
        return $this->belongsTo(Timezone::class, 'timezone_id', 'id');
    }


    public function assign_attendees()
    {
        return $this->hasMany('\Models\EventAttendees', 'event_id');
    }

    public function checkinUsers()
    {
        return $this->hasMany('CheckinUser', 'event_id');
    }

    public function localSms()
    {
        return $this->hasMany('SmsHistory', 'event_id', 'id')->where('phone','Like','45%');
    }

    public function globalSms()
    {
        return $this->hasMany('SmsHistory', 'event_id', 'id')->where('phone','Not Like','45%');
    }
    public function loginHistory()
    {
        return $this->hasMany('LoginHistory', 'event_id', 'id');
    }


    public function attendee_settings()
    {
        return $this->hasOne('AttendeeSettings','event_id');
    }
    public function hub_admins()
    {
        return $this->hasMany('\Models\EventAttachHubAdmin','event_id');
    }
    public function module_tabs()
    {
        return $this->hasMany('\Models\EventModuleTabSettings','event_id');
    }

    public function eventDateFormat()
    {
        return $this->hasMany('EventDateFormat','event_id');
    }

    public function nativeAppSetting()
    {
        return $this->hasMany('NativeAppSettings','event_id');
    }

}