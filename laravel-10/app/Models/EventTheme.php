<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTheme extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_themes';
    protected $dates = ['deleted_at'];
    protected $fillable = ['event_id', 'theme_id', 'status'];


    public function modules()
    {
        return $this->hasMany(\App\Models\EventThemeModule::class, 'theme_id', 'theme_id');
    }

    public function theme_info()
    {
        return $this->belongsTo(\App\Models\Theme::class, 'theme_id', 'id');
    }
}
