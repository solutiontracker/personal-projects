<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Request;

class Attendee extends Authenticatable
{
    use Notifiable, HasApiTokens;
    use SoftDeletes;
    protected $guard = 'attendee';
    protected $table = 'conf_attendees';
    public static function boot()
    {
        parent::boot();
        Attendee::created(function($model)
        {
            static::logChange( $model, 'CREATED' );
        });
        Attendee::updated(function (Model $model) {
            foreach ($model->getDirty() as $attribute=>$value){
                if($attribute != 'updated_at') {

                    if(str_contains(static::class, 'Info') && $attribute == 'value'){
                        $column = $model->getOriginal('name');
                    }else{
                        $column = $attribute;
                    }
                    $data = $value;
                    $original = $model->getOriginal($attribute);
                    if ($original != $data && $original != $data.':00' && $original != $data.' 00:00:00') {
                        static::logChange($model, 'UPDATED', $column, $data, $original);
                    }
                }
            }
        });
        Attendee::deleted(function (Model $model) {
            static::logChange( $model, 'DELETED' );
        });
    }
    public static function logChange( Model $model, string $action , $column = null, $new = null, $original = null) {
        $data=getLogData();
        if($action === 'DELETED' || $action === 'CREATED'){
            AttendeeRegistrationLog::create([
                'organizer_id' => $model->organizer_id,
                'event_id' => $data['event_id'],
                'attendee_id' => $model->id,
                'model' => 'Attendee',
                'reg_date' => \Carbon\Carbon::now(),
                'app_type' => $data['app_type'],
                'action' => $action,
                'register_by' => $data['logged_by_user_type'],
                'register_by_id' => $data['logged_by_id'],
                'ip' => getIPAddress(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT']??null
            ]);
        }else{
            $data=getLogData();
            AttendeeChangeLog::create([
                'organizer_id' => $model->organizer_id,
                'logged_by_id' => $data['logged_by_id'],
                'logged_by_user_type' => $data['logged_by_user_type'],
                'attendee_id' =>$model->id,
                'event_id' => isset($model->event_id) ? $model->event_id : $data['event_id'],
                'action_model' => static::class,
                'action' => $action,
                'attribute_name' => $column,
                'old_value' => $original,
                'new_value' => $original == 1 && $new == '' ? 0 : $new,
                'app_type' => $data['app_type']
            ]);
        }
    }
    protected $attributes = [
        'ss_number' => '',
        'password' => '',
        'status' => '',
        'show_home' => '0',
        'image' => '',
        'first_name' => '',
        'last_name' => '',
        'billing_ref_attendee' => '0',
        'SPOKEN_LANGUAGE' => '',
        'LAST_NAME_PASSPORT' => '',
        'FIRST_NAME_PASSPORT' => '',
        'change_password' => '',
        'phone' => '',
        'is_updated' => '1',
        'billing_password' => '',
    ];

    protected $fillable = ['id', 'email', 'ss_number', 'end_date', 'password', 'first_name', 'last_name', 'organizer_id', 'status', 'show_home', 'image', 'country_id', 'allow_vote', 'billing_password', 'billing_ref_attendee', 'SPOKEN_LANGUAGE', 'EMPLOYMENT_DATE', 'BIRTHDAY_YEAR', 'LAST_NAME_PASSPORT', 'FIRST_NAME_PASSPORT', 'change_password', 'phone', 'is_updated'];
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\AttendeeInfo', 'attendee_id')->where('languages_id', request()->language_id);
    }
    
    public function detail()
    {
        return $this->hasMany('\App\Models\AttendeeInfo', 'attendee_id');
    }

    public function companyName(){
        return $this->hasMany('\App\Models\AttendeeInfo', 'attendee_id')
            ->where('languages_id', request()->language_id)
            ->where("name", "company_name");
    }

    public function categories()
    {
        return $this->belongsToMany('\App\Models\EventAgendaSpeaker', 'conf_event_speaker_categories', 'speaker_id', 'category_id');
    }

    public function image()
    {
        return $this->hasOne('\App\Models\EventAttendeeImage', 'attendee_id');
    }

    public function event()
    {
        return $this->hasOne('App\Models\EventAttendee', 'attendee_id');
    }

    public function groups()
    {
        return $this->belongsToMany('\App\Models\EventGroup', 'conf_event_attendees_groups', 'attendee_id', 'group_id');
    }

    public function adminEventGroups()
    {
        return $this->belongsToMany('\App\Models\EventGroup', 'conf_event_attendees_groups', 'attendee_id', 'group_id');
    }

    public function billing()
    {
        return $this->hasOne(AttendeeBilling::class, 'attendee_id');
    }

    public function agoraMeeting($channel)
    {
        return $this->hasOne('\App\Models\EventMeetingHistory', 'attendee_id')->where("plateform", "agora")->where("channel", $channel)->first();
    }

    public function orderAttendee()
    {
        return $this->hasOne('\App\Models\BillingOrder', 'attendee_id');
    }

    public function eventAttendees()
    {
        return $this->hasMany('\App\Models\EventAttendee', 'attendee_id');
    }

    public function billingFields($order_id, $event_id)
    {
        return $this->hasOne('\App\Models\AttendeeBilling', 'attendee_id')->where('event_id', $event_id)->where('order_id', $order_id);
    }

    public function Events(){
        return $this->belongsToMany(Event::class, 'conf_event_attendees', 'attendee_id', 'event_id')
            ->withPivot(['id,email_sent,sms_sent,login_yet,status,attendee_id,event_id,speaker,sponser,exhibitor,attendee_type,default_language_id,device_token,device_type,app_invite_sent,is_active,verification_id,gdpr,allow_vote,allow_gallery,ask_to_apeak,type_resource,accept_foods_allergies,native_app_forgot_password_code,native_app_forgot_password_code_created_at,created_at,updated_at,deleted_at,allow_my_document']);
    }

    public function attachedSponsor()
    {
        return $this->belongsToMany(\App\Models\EventSponsor::class, 'conf_event_sponsor_attendees', 'attendee_id', 'sponsor_id');
    }
    
    public function attachedExhibitor()
    {
        return $this->belongsToMany(\App\Models\EventExhibitor::class, 'conf_event_exhibitor_attendees', 'attendee_id', 'exhibitor_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @api RegistrationSite
     */
    public function currentEventAttendee(){
        return $this->hasOne(EventAttendee::class, 'attendee_id')->where('event_id', request()->event_id);
    }
}