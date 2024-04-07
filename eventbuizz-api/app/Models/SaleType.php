<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SaleType extends Model
{

	use SoftDeletes;
	
	protected $table = 'conf_sales_type';

    protected $fillable = ['id', 'organizer_id','name','code'];

	protected $dates = ['deleted_at'];

}