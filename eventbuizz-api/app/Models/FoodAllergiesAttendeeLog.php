<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodAllergiesAttendeeLog extends Model
{
    protected $table = 'conf_food_allergies_attendee_log';
    protected $fillable = ['event_id','attendee_id','food_accept','food_description'];
    protected $dates = ['deleted_at'];

    use SoftDeletes;

    public function info()
    {
        return $this->hasMany('Attendees', 'id', 'attendee_id');
    }

}