<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventOrderKeyword extends Model
{

    protected $table = 'conf_event_order_keyword';

    protected $fillable = ['id', 'order_id', 'keyword_id', 'attendee_id'];

	protected $dates = ['deleted_at'];

}
