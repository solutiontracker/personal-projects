<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model {

    protected $table = 'conf_modules';
    protected $fillable = ['name', 'alias', 'class_name', 'version', 'type'];

    public function adds()
    {
        return $this->hasOne('Addons', 'module_id');
    }
}