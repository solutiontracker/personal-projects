<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCheckInTicketItem extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_checkin_ticket_items';

    protected $fillable = ['event_id', 'organizer_id', 'price', 'item_number', 'item_name', 'status', 'total_tickets'];

    protected $appends = ['remaining_items', 'sold_items'];
    
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function ticket_validity()
    {
        return $this->hasMany('\App\Models\EventTicketItemValidity', 'ticket_item_id', 'id');
    }

    public function ticket_config()
    {
        return $this->hasOne('\App\Models\EventTicketItemConfig', 'ticket_item_id', 'id');
    }

    public function order()
    {
        return $this->hasManyThrough('\App\Models\EventCheckinTicketOrder', '\App\Models\EventCheckInTicketOrderAddon', 'ticket_item_id', 'order_id');
    }

    public function addons()
    {
        return $this->hasMany('\App\Models\EventCheckInTicketOrderAddon', 'addon_id');
    }

    public function billing_order_addons()
    {
        return $this->hasManyThrough('\App\Models\BillingOrderAddon', '\App\Models\BillingItem', 'ticket_item_id', 'addon_id');
    }

    public function getRemainingItemsAttribute()
    {
        $used_count = $this->addons()->whereHas('order', function ($q) {
            $q->where('is_archive', '0')->where('status', '<>', 'cancelled');
        })->sum('qty');

        return $this->total_tickets - $used_count;
    }

    public function getSoldItemsAttribute()
    {
        $billing_Orders_qty = 0;

        $billing_orders_addons = $this->billing_order_addons()->whereHas('order', function ($q) {
            $q->where('is_archive', '0')->where('status', '<>', 'cancelled'); //to get the current order
        })->with('order')->get()->toArray();

        if (count($billing_orders_addons) > 0) {

            $orders = [];

            foreach ($billing_orders_addons as $addons) {
                $orders[] = $addons['order']['id'];
            }

            $orders = array_unique($orders);

            $order_ids = \App\Models\BillingOrder::whereIn('id', $orders)->currentOrder()->pluck('id')->toArray();

            $billing_Orders_qty = $this->billing_order_addons()->whereHas('order', function ($q) use ($order_ids) {
                $q->whereIn('id', $order_ids);
            })->sum('conf_billing_order_addons.qty');
            
        }

        $chekin_orders_qty = $this->addons()->whereHas('order', function ($q) {
            $q->where('is_archive', '=', '0')->where('status', '<>', 'cancelled');
        })->sum('qty');

        return $chekin_orders_qty + $billing_Orders_qty;
    }

    public function info()
    {
        return $this->hasMany('\App\Models\EventCheckInTicketItemInfo', 'ticket_item_id', 'id');
    }
}
