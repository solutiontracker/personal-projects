<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PollTemplate extends Model
{
    protected $table = 'conf_poll_templates';
    protected $fillable = ['event_id', 'name', 'position', 'preview_image', 'status', 'sort_order'];
}