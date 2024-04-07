<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderRequestLog extends Model
{
    protected $connection = 'mysql_request_logs';

    protected $table = 'conf_order_request_logs';

    protected $fillable = ['request', 'url', 'event_id', 'order_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'request' => 'array',
    ];
}
