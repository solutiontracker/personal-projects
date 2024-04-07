<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CronPushNotification extends Model
{

	use SoftDeletes;
	protected $table = 'conf_cron_push_notification';
    protected $fillable = ['id','organizer_id','event_id','deviceType','deviceToken', 'alertTtile','alertDescription','status','alert_id','alert_date','alert_time', 'responce'];
	protected $dates = ['deleted_at'];

    public function alert(){
        return $this->belongsTo(EventAlert::class, 'alert_id');
    }
}