<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeInfo extends Model
{
    use SoftDeletes;

    protected $attributes = [
        'status' => '0',
    ];

    protected $table = 'conf_attendees_info';

    protected $fillable = ['name', 'value', 'event_id', 'languages_id', 'attendee_id', 'status', 'custom_field_updated'];

    public static function AllAttendeeInfo($language_id)
    {
        return self::where('languages_id', $language_id);
    }

    public function attendee()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    public static function firstOrCopyOrCreateEmpty($find, $insert)
    {
        if (!isset($find['languages_id']) || $find['languages_id'] == '') {
            throw new \Exception('languages_id cannot be null');
        }

        $languages_id = $find['languages_id'];
        $data = self::where($find)->first();
        if ($data instanceof \App\Models\AttendeeInfo) {
            return $data;
        } else {
            unset($find['languages_id']);
            //Search in all languages
            $data = self::where($find)->first();
            //If found simply create a new record against current language by copying existing data.
            if ($data instanceof \App\Models\AttendeeInfo) {
                $model = $data->replicate(['id', 'languages_id', 'created_at', 'updated_at', 'deleted_at']);
                $model->languages_id = $languages_id;
                $model->save();
                return $model;
            } else {
                //create a new empty record
                return self::create($insert);
            }
        }
    }

    public static function firstOrCreate_new($find = [], $insert = [])
    {
        $find_data = [];
        if (empty($find)) {
            return;
        } else {
            $find_data = self::where($find)->first();
        }
        if ($find_data and $find_data instanceof \App\Models\AttendeeInfo) {
            return $find_data;
        } else {
            if (empty($insert)) {
                $id = self::insertGetId($find);
            } else {
                $id = self::insertGetId($insert);
            }
            return self::find($id);
        }
    }

    public function scopeOfLanguage($query, $id)
    {
        return $query->where('languages_id', $id);
    }

    public static function boot()
    {
        parent::boot();
        AttendeeInfo::updated(function (Model $model) {
            foreach ($model->getDirty() as $attribute=>$value){
                if($attribute != 'updated_at') {

                    if($attribute == 'value'){
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
        AttendeeInfo::deleted(function (Model $model) {
            static::logChange( $model, 'DELETED' );
        });
    }
    public static function logChange( Model $model, string $action , $column = null, $new = null, $original = null)
    {
        $data = getLogData();
        if ($action === 'DELETED' || $action === 'UPDATED'){
            AttendeeChangeLog::create([
                'organizer_id' => $data['organizer_id'],
                'logged_by_id' => $data['logged_by_id'],
                'logged_by_user_type' => $data['logged_by_user_type'],
                'attendee_id' => $model->attendee_id,
                'event_id' => isset($model->event_id) ? $model->event_id : $data['event_id'],
                'action_model' => static::class,
                'action' => $action,
                'model_id' => $model->id,
                'attribute_name' => $column,
                'old_value' => $original,
                'new_value' => $original == 1 && $new == '' ? 0 : $new,
                'app_type' => $data['app_type']
            ]);
        }
    }
}