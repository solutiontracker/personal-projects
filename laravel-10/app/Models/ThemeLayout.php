<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThemeLayout extends Model {

    protected $table = 'conf_theme_layouts';
    protected $fillable = ['theme_id', "name", "description", "image"];
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}