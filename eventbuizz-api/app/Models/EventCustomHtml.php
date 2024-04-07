<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCustomHtml extends Model {

    use SoftDeletes;
    protected $table = 'conf_event_customhtml';
    protected $fillable = ['event_id','custom_html_1','custom_html_2', 'custom_html_3'];
    protected $dates = ['deleted_at'];
}
