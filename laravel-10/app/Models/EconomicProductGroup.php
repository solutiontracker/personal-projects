<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EconomicProductGroup extends Model
{
    use SoftDeletes;
    protected $table = 'conf_economic_product_groups';
    protected $fillable = ['productGroupNumber', 'name', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];
}
