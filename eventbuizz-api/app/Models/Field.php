<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model {
    use SoftDeletes;
    protected $table = 'conf_fields';
    protected $fillable = ['sort_order','status','mandatory','field_alias','type','section_alias'];

    public function info()
    {
        return $this->hasMany('\App\Models\FieldInfo', 'field_id');
    }
}