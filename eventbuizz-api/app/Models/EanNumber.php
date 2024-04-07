<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EanNumber extends Model
{
    use SoftDeletes;

    protected $table = 'conf_ean_numbers';

    protected $fillable = ['ean', 'company_name'];
    
    protected $dates = ['deleted_at'];
}
