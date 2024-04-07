<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventCheckInTicketOrder extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_checkin_ticket_orders';
    protected $fillable = ['event_id', 'organizer_id', 'user_id', 'order_date', 'is_archive', 'status', 'user_type'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $appends = ['order_total', 'type_of_user', 'all_tickets_cancelled'];

    public function addons()
    {
        return $this->hasMany('\App\Models\EventCheckInTicketOrderAddon', 'order_id', 'id');
    }

    public function getOrderTotalAttribute()
    {
        $total = 0;
        $addons = $this->addons()->get()->toArray();
        foreach ($addons as $addon) {
            $total = $total + ($addon['price'] * $addon['qty']);
        }
        return $total;
    }

    public function tickets()
    {
        return $this->hasManyThrough('\App\Models\EventTicket', '\App\Models\EventCheckInTicketOrderAddon', 'order_id', 'addon_id');
    }

    public function user()
    {
        return $this->morphTo();
    }

    public function getTypeOfUserAttribute()
    {
        $type = $this->user_type;
        if ($type) {
            $type = str_replace('\App\Models\\', '', $type);
            $type = rtrim($type, 's');
            return $type;
        } else {
            return '';
        }
    }

    public function getAllTicketsCancelledAttribute()
    {
        $tickets = $this->tickets;
        foreach ($tickets as $ticket) {
            if ($ticket->status == '1') {return 0;}
        }
        return 1; //all tickets are cancelled
    }
}
