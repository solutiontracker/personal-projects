<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformationPageInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_information_page_info';
    protected $fillable = ['page_id','name','value','language_id','status'];

    protected $dates = ['deleted_at'];
}
