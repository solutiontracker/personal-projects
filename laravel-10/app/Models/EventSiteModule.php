<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSiteModule extends Model {

    protected $table = 'conf_eventsite_modules';
    protected $fillable = ['name', "alias"];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}