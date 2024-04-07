<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTicketValidity extends Model
{
    protected $table = 'conf_event_ticket_validity';
    protected $fillable = ['ticket_id','valid_from','valid_to','usage_limit'];
}