<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class StandSaleRegistrationLink extends Model
{
    protected $table = 'conf_stand_sale_registration_links';

    protected $fillable = ['event_id', 'type', 'link_id', 'token', 'expire_at', 'order_id', 'attendee_id'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
