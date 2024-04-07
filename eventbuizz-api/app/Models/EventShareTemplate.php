<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventShareTemplate extends Model {

    use SoftDeletes;
    protected $table = 'conf_event_share_template';
    protected $fillable = ['event_id'];
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\EventShareTemplateInfo', 'template_id');
    }
}
