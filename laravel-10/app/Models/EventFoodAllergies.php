<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventFoodAllergies extends Model {

    protected $table = 'conf_event_food_allergies';
    protected $fillable = ['event_id','subject','inline_text','description'];

}