<?php
namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendee extends Model
{
    use SoftDeletes;
    protected $attributes = [
        'device_token' => '',
        'allow_vote' => '0',
        'allow_gallery' => '0',
        'ask_to_apeak' => '0',
        'type_resource' => '0',
        'attendee_type' => '0',
        'allow_my_document' => '0',
    ];
    protected $table = 'conf_event_attendees';
    protected $fillable = ['id','email_sent','sms_sent','login_yet','status','attendee_id','default_language_id','event_id','device_token', 'is_active', 'verification_id', 'gdpr', 'speaker','sponser','exhibitor','accept_foods_allergies', 'attendee_type', 'allow_vote', 'allow_gallery', 'type_resource', 'allow_my_document', 'ask_to_apeak', 'camera'];

    public function attendees()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    public function attendee()
    {
        return $this->belongsTo('\App\Models\Attendee','attendee_id','id');
    }

    public function event()
    {
        return $this->belongsTo('\App\Models\Event', 'event_id', 'id');
    }

    public function regForm()
    {
        return $this->belongsTo('\App\Models\RegistrationForm', 'attendee_type', 'type_id');
    }

    public static function boot()
    {
        parent::boot();
        EventAttendee::created(function($model)
        {
            static::logChange( $model, 'CREATED' );
        });
        EventAttendee::updated(function (Model $model) {
            foreach ($model->getDirty() as $attribute=>$value){
                if($attribute != 'updated_at') {
                    $column = $attribute;
                    $data = $value;
                    $original = $model->getOriginal($attribute);
                    if ($original != $data) {
                        static::logChange($model, 'UPDATED', $column, $data, $original);
                    }
                }
            }
        });
        EventAttendee::deleted(function (Model $model) {
            static::logChange( $model, 'DELETED' );
        });
    }

    public static function logChange( Model $model, string $action , $column = null, $new = null, $original = null) {
        $data=getLogData($model->event_id);
        if($action === 'DELETED' || $action === 'CREATED'){
            $event_id = isset($model->event_id)?$model->event_id:$data['event_id'];
            AttendeeRegistrationLog::create([
                'organizer_id' => $data['organizer_id'],
                'event_id' => $event_id,
                'attendee_id' => $model->attendee_id,
                'model' => 'EventAttendee',
                'reg_date' => \Carbon\Carbon::now(),
                'app_type' => $data['app_type'],
                'action' => $action,
                'register_by' => $data['logged_by_user_type'],
                'register_by_id' => $data['logged_by_id'],
                'ip' => getIPAddress(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT']??null
            ]);
        }else{
            AttendeeChangeLog::create([
                'organizer_id' => $data['organizer_id'],
                'logged_by_id' =>  $data['logged_by_id'],
                'logged_by_user_type' =>  $data['logged_by_user_type'],
                'attendee_id' =>  $model->attendee_id,
                'event_id' => isset($model->event_id)?$model->event_id:$data['event_id'],
                'action_model'   => static::class,
                'action'  => $action,
                'model_id'  => $model->id,
                'attribute_name'  => $column,
                'old_value'  => $original,
                'new_value'  => $original==1&&$new==''?0:$new,
                'app_type'  => $data['app_type']
            ]);
        }
    }
    
}
