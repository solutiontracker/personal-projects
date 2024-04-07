<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAlertGroup extends Model
{
    protected $table = 'conf_event_alert_groups';
    protected $fillable = ['group_id','alert_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
