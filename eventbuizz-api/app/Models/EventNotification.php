<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventNotification extends Model
{
    protected $table = 'conf_event_notifications';
    protected $fillable = ['event_id', 'attendee_id', 'type', 'link', 'status', 'date'];


}
