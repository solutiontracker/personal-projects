<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogueProduct extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_catalogue_products';
    protected $fillable = ['event_id', 'type', 'type_id', 'product_name','document', 'status','sort_order'];
    protected $dates = ['deleted_at'];  
    protected $hidden = ['pivot'];
}
