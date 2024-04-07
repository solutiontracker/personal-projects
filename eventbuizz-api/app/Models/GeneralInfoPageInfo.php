<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralInfoPageInfo extends Model
{
    protected $table = 'conf_general_info_pages_info';
    protected $fillable = ['page_id','name','value','languages_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
