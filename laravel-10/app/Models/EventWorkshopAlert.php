<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventWorkshopAlert extends Model
{
    protected $table = 'conf_event_workshop_alert';
    protected $fillable = ['workshop_id','alert_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
