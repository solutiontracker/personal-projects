<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventBadgeCustom extends Model
{
    protected $table = 'conf_event_custom_badges';
    protected $fillable = ['id', 'event_id', 'organizer_id', 'name', 'size', 'body','logo','logo_2','height','width','background','badgefor','badgeTypeId','created_at','updated_at','deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
