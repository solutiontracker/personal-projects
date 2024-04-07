<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class WebServiceRequestLog extends Model
{
    use SoftDeletes;
    protected $table = 'conf_webservice_request_log';
    protected $fillable = ['data', 'endpoint', 'date'];
    public $timestamps = false;
}
