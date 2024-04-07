<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DirectoryInfo extends Model {
    protected $table = 'conf_directory_info';
    protected $fillable = ['id','name','value','directory_id','languages_id','status','created_at', 'updated_at', 'deleted_at'];
    use SoftDeletes;
}