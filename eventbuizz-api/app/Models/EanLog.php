<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EanLog extends Model
{
    use SoftDeletes;

    protected $table = 'conf_ean_log';
    
    protected $fillable = ['event_id','organizer_id','order_id'];
}
