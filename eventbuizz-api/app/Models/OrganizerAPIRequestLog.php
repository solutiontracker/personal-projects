<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerAPIRequestLog extends Model {

    use SoftDeletes;

    protected $connection = "mysql-logs";

    protected $table = 'conf_organizer_api_request_log';

    protected $fillable = ['organizer_id', 'api_key', 'request_type', 'request_responce', 'request'];

    protected $dates = ['deleted_at'];

}