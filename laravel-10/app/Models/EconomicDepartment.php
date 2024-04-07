<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EconomicDepartment extends Model
{
    use SoftDeletes;
    protected $table = 'conf_economic_departments';
    protected $fillable = ['departmentNumber', 'name'];
    protected $dates = ['deleted_at'];
}
