<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSponsorAlert extends Model
{
    protected $table = 'conf_event_sponsor_alert';
    protected $fillable = ['sponsor_id','alert_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
