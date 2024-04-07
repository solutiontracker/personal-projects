<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventSiteBusinessDatingSetting extends Model
{
    use SoftDeletes;
    protected $table = 'conf_eventsite_business_dating_settings';
    protected $fillable = ['event_id', 'required'];
    protected $dates = ['deleted_at'];
}
