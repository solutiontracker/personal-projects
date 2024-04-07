<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DirectorySetting extends Model
{

    protected $table = 'conf_directory_settings';
    protected $fillable = ['id','name','value','event_id','alies','languages_id','created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}