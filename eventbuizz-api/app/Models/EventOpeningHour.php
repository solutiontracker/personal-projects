<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventOpeningHour extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_opening_hours';
    protected $fillable = ['event_id', 'date', 'start_time', 'end_time'];
}