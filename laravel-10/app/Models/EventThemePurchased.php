<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventThemePurchased extends Model
{
    use SoftDeletes;
    protected $table = 'conf_theme_purchased';
    protected $dates = ['deleted_at'];
    protected $fillable = ['organizer_id', 'event_id', 'theme_id', 'status'];
}
