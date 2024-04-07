<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventLeadProduct extends Model {
    use SoftDeletes;
    protected $table = 'conf_event_lead_product';
    protected $fillable = ['product_id', 'lead_id'];

}