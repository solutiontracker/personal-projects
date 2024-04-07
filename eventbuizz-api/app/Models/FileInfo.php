<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileInfo extends Model {

    protected $table = 'conf_file_info';
    protected $fillable = ['id','name','value','file_id','languages_id','status','created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
}