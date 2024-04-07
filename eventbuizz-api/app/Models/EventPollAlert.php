<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPollAlert extends Model
{
    protected $table = 'conf_event_poll_alert';
    protected $fillable = ['poll_id','alert_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
