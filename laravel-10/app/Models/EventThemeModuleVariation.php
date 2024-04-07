<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventThemeModuleVariation extends Model {

    protected $table = 'conf_event_theme_module_variations';
    protected $fillable = ['event_id', 'theme_id', "alias", "module_name", "variation_name", "variation_slug", "background_image", "text_align"];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}