<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingOrderAddon extends Model
{

    use SoftDeletes;
    protected $table = 'conf_billing_order_addons';
    protected $fillable = ['order_id', 'attendee_id', 'addon_id', 'name', 'price', 'vat', 'qty', 'discount', 'discount_qty', 'discount_type', 'ticket_item_id', 'parent', 'link_to', 'link_to_id', 'group_id'];
    protected $dates = ['deleted_at'];

    public function addon_detail()
    {
        return $this->belongsTo('\App\Models\BillingItem', 'addon_id', 'id');
    }

    public function attendee()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    public function tickets()
    {
        return $this->morphMany('\App\Models\EventTicket', 'addon');
    }

    public function order()
    {
        return $this->belongsTo('\App\Models\BillingOrder', 'order_id', 'id');
    }

    public function ticket_item()
    {
        return $this->hasOne('\App\Models\EventCheckInTicketItem', 'id', 'ticket_item_id');
    }

    public function addon_group_detail()
    {
        return $this->belongsTo('\App\Models\BillingItem', 'group_id', 'id');
    }

    public function info()
    {
        return $this->hasMany('\App\Models\BillingItemInfo', 'item_id');
    }
}
