<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTicketItemConfig extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_ticket_item_config';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    
    protected $fillable = ['ticket_item_id', 'prefix', 'serial_start'];

    public function item()
    {
        return $this->belongsTo(BillingItem::class, 'id', 'ticket_item_id');
    }
}
