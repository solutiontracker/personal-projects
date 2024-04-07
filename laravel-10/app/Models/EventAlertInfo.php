<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAlertInfo extends Model
{
    protected $table = 'conf_event_alert_info';
    protected $fillable = ['alert_id','name','value','languages_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
