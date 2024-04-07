<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActorActivityLog extends Model
{
    use SoftDeletes;
    protected $table = 'conf_actor_activity_log';
    protected $fillable = ['module_alias','actor_id', 'actor_type', 'action', 'activity', 'created_at'];
    public $timestamps = true;
}
