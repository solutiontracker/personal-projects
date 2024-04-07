<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlugnplayModulesProgress extends Model
{
    protected $table = 'conf_plugnplay_modules_progress';
    protected $fillable = ['module', 'status', 'event_id'];
}
