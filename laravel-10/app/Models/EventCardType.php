<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCardType extends Model {
    protected $attributes = [
        'purchase_policy_inline_text' => '',
    ];
    use SoftDeletes;
    protected $table = 'conf_event_card_type';
    protected $fillable = ['event_id','organizer_id', 'card_type', 'purchase_policy'];
    protected $dates = ['deleted_at'];

}