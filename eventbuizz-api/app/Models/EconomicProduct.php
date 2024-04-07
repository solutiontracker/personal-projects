<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EconomicProduct extends Model
{
    use SoftDeletes;
    protected $table = 'conf_economic_products';
    protected $fillable = ['productNumber', 'name', 'description', 'recommendedPrice', 'salesPrice', 'lastUpdated', 'productGroupNumber', 'barred', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];
}
