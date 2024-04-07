<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LayoutSection extends Model {

    protected $table = 'conf_layout_sections';
    protected $fillable = ['layout_id', "variation_slug", "module_alias", "status","sort_order"];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}