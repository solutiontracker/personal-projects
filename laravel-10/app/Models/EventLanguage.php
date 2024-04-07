<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventLanguage extends Model {

    protected $table = 'conf_event_languages';
    protected $fillable = ['event_id','language_id','status'];

}