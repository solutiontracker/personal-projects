<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCheckInTicketItemInfo extends Model
{
    protected $table = 'conf_event_checkin_ticket_items_info';
    protected $fillable = ['name', 'value', 'ticket_item_id', 'languages_id'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function item()
    {
        $this->belongsTo('\App\Models\EventCheckInTicketItem', 'ticket_item_id');
    }
}
