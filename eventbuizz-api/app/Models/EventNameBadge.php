<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventNameBadge extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_name_badges';
    public $timestamps = true;
}
