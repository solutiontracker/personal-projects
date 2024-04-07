<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventCheckInTicketOrderAddon extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_checkin_ticket_order_addons';

    protected $fillable = ['order_id', 'user_id', 'addon_id', 'price', 'qty'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function order()
    {
        return $this->belongsTo('\App\Models\EventCheckInTicketOrder', 'order_id', 'id');
    }

    public function ticket_item()
    {
        return $this->belongsTo('\App\Models\EventCheckInTicketItem', 'addon_id', 'id');
    }

    public function tickets()
    {
        return $this->morphMany('\App\Models\EventTicket', 'addon');
    }
}
