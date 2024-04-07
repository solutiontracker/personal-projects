<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomizeSetting extends Model
{
    protected $table = 'conf_customize_settings';
    protected $fillable = ['name','value','event_id'];
}
