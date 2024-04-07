<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class Theme extends Model
{
    use SoftDeletes;
    protected $table = 'conf_themes';
    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'status', 'thumbnail', 'demo_url', 'is_paid', 'price', 'is_default', 'slug'];

    public function modules()
    {
        return $this->hasMany(\App\Models\ThemeModule::class, 'theme_id', 'id');
    }

    public function eventThemeModules()
    {
        return $this->hasMany(\App\Models\ThemeModule::class, 'theme_id', 'id')->has('currentEventThemeModules');
    }

    public function currentEventTheme(){
        return $this->hasMany(EventTheme::class, 'theme_id')->where('event_id', request()->event_id);
    }
}
