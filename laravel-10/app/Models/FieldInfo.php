<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldInfo extends Model {

    use SoftDeletes;
    protected $table = 'conf_fields_info';
    protected $fillable = ['name','value','languages_id','field_id'];
}