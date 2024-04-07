<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class HubAdminEvent extends Model
{

    use SoftDeletes;
    protected $table = 'conf_hub_admin_events';
    protected $fillable = ['hub_admin_id', 'event_id'];
    protected $dates = ['deleted_at'];
}
