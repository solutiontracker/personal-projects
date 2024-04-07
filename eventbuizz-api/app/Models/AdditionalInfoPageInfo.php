<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdditionalInfoPageInfo extends Model
{
    protected $table = 'conf_additional_info_pages_info';
    protected $fillable = ['page_id','name','value','languages_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}