<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailStatsLogInfo extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection= 'mysql_email_logs';

    protected $table = "conf_email_stats_log_infos";

    protected $fillable =[ 'from', 'headers', 'body', 'response', 'email_stats_log_id' , 'template'];

}
