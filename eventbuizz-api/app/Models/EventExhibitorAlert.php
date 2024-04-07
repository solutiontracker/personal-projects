<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventExhibitorAlert extends Model
{
    protected $table = 'conf_event_exhibitor_alert';
    protected $fillable = ['exhibitor_id','alert_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
