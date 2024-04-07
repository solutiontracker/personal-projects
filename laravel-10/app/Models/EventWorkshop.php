<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventWorkshop extends Model
{

    use SoftDeletes;
    protected $table = 'conf_event_workshops';
    protected $fillable = ['event_id', 'date', 'start_time', 'end_time'];
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\EventWorkShopInfo', 'workshop_id');
    }

    public function programs()
    {
        return $this->hasMany('\App\Models\EventAgenda', 'workshop_id');
    }
}
