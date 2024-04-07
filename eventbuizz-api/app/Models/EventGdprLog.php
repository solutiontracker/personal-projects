<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventGdprLog extends Model {

    protected $table = 'conf_event_gdpr_log';
    protected $fillable = ['gdpr_id','event_id','subject','inline_text','description'];

}