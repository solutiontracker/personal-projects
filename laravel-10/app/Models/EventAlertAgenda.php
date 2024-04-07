<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAlertAgenda extends Model
{
    protected $table = 'conf_event_alert_agendas';
    protected $fillable = ['agenda_id','alert_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
