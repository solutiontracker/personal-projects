<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTicket extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_tickets';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['serial', 'event_id', 'addon_id', 'ticket_item_id', 'addon_type', 'status', 'qr_string'];
    
    protected $appends = ['type', 'vat'];

    public function addon()
    {
        return $this->morphTo();
    }

    public function validity()
    {
        return $this->hasMany(EventTicketValidity::class, 'ticket_id', 'id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function ticket_item()
    {
        return $this->belongsTo(EventCheckInTicketItem::class, 'ticket_item_id', 'id')->withTrashed();
    }

    public function getTypeAttribute()
    {
        return ($this->addon_type == '\App\Models\BillingOrderAddon' ? 'billing' : 'checkin');
    }

    public function getVatAttribute()
    {
        if ($this->type != 'billing') { return 0; } else {
            $eps = \App\Models\EventsitePaymentSetting::where('event_id', '=', $this->event_id)->where('registration_form_id', 0)->first();
            return (float) $eps->eventsite_vat;
        }
    }

    public function usage_history()
    {
        return $this->hasMany(EventTicketUsageHistory::class, 'ticket_id', 'id');
    }
}
