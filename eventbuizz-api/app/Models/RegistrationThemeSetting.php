<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationThemeSetting extends Model
{
    use SoftDeletes;

    protected $table = 'conf_registration_theme_setting';
    use Observable;
    protected $fillable = ['mode', 'body_color', 'wrapper_color', 'event_id', 'theme_id'];

    protected $dates = ['deleted_at'];
}