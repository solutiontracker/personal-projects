<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleOrderInfo extends Model
{

    protected $table = 'conf_modules_order_info';
    protected $fillable = ['name', 'value', 'languages_id','status','module_order_id'];
}