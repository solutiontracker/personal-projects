<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMediaSetting extends Model
{
    protected $table = 'conf_social_media_settings';
    use Observable;
    protected $fillable = ['event_id', 'display_social_media_in_dashboard'];

    use SoftDeletes;
}
