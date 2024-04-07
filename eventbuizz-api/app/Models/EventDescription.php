<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class EventDescription extends Model {

    use SoftDeletes;
    protected $table = 'conf_event_description';
    protected $fillable = ['event_id'];
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\EventDescriptionInfo', 'description_id');
    }
}
