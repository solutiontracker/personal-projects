<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebHookRequestLog extends Model
{

    use SoftDeletes;
    protected $connection= 'mysql_email_logs';

    protected $table = 'conf_webhook_request_log';

    protected $fillable = ['data', 'endpoint', 'date'];

}
