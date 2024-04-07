<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThemeModuleVariation extends Model {

    protected $table = 'conf_theme_module_variations';
    protected $fillable = ['theme_id', "alias", "module_name", "variation_name", "variation_slug", "variation_image",  "background_allowed", "text_align_allowed"];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}