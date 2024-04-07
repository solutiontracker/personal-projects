<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTicketUsageHistory extends Model
{
    protected $table = 'conf_event_tickets_usage_history';
    protected $fillable = ['ticket_id','used_by','used_on','checked_by','is_organizer'];
    public $timestamps = false;
}