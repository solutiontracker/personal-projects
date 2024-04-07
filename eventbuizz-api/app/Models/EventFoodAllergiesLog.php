<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventFoodAllergiesLog extends Model {

    protected $table = 'conf_event_food_allergies_log';
    protected $fillable = ['food_id','event_id','subject','inline_text','description'];

}