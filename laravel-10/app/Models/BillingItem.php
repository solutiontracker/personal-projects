<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingItem extends Model
{

    use SoftDeletes;
    
    protected $attributes = [
        'group_id' => 0,
        'link_to_id' => 0,
        'total_tickets' => 0,
        'is_archive' => 0,
    ];

    protected $table = 'conf_billing_items';

    protected $fillable = ['event_id', 'sort_order', 'item_number', 'organizer_id', 'price', 'qty', 'status', 'type', 'link_to', 'link_to_id', 'group_id', 'group_type', 'group_required', 'total_tickets', 'is_free', 'is_default', 'ticket_item_id', 'is_required', 'group_is_expanded', 'vat', 'registration_form_id', 'non_editable'];

    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\BillingItemInfo', 'item_id');
    }

    public function event_items()
    {
        return $this->belongsToMany('\App\Models\Event', 'conf_billing_item_events', 'item_id', 'event_id');
    }
    
    public function subitem()
    {
        return $this->hasMany('\App\Models\BillingItem', 'group_id');
    }

    public function rules()
    {
        return $this->hasMany('\App\Models\BillingItemRule', 'item_id');
    }

    public function used_items()
    {
        return $this->hasMany('\App\Models\BillingOrderAddon', 'addon_id');
    }

    public function scopeValidItem($query)
    {
        return $query->where('status', '1')->where('is_archive', '0');
    }
}
