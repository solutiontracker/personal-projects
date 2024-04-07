<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventGroupInfo extends Model {

    protected $table = 'conf_event_group_info';
    protected $fillable = ['name', 'value', 'end_date', 'languages_id', 'group_id'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}

