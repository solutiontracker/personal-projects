<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventNewsSubscriberSetting extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_news_subscriber_settings';
    protected $fillable = ['event_id', 'status', 'subscriber_ids'];
    protected $dates = ['deleted_at'];
}
