<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialWallSetting extends Model
{
    use SoftDeletes;
    protected $table = 'conf_social_wall_settings';
    protected $fillable = ['event_id', 'hash_label', 'background_color', 'background_image', 'organizer_info'];
    protected $dates = ['deleted_at'];
}