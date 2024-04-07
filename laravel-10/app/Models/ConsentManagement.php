<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsentManagement extends Model {
    use SoftDeletes;
    protected $table = 'conf_event_consent_management';
    protected $fillable = ['event_id', 'type', 'type_id', 'consent_name','status','sort_order'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['pivot'];
}