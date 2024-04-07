<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTicketItemValidity extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_ticket_item_validity';
    protected $fillable = ['ticket_item_id','valid_from','valid_to','usage_limit'];
    protected $dates = ['created_at','updated_at','deleted_at'];
}