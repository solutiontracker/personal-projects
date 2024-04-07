<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAlertIndividual extends Model
{
    protected $table = 'conf_event_alert_individuals';
    protected $fillable = ['attendee_id','alert_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
