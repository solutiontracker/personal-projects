<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoogleAnalyticsJobsLog extends Model {
    use SoftDeletes;
    protected $connection = "mysql-logs";
    protected $table = 'conf_google_analytics_jobs_logs';
    protected $fillable = ['job_type', 'code_hint', 'res_code', 'response'];
    protected $dates = ['deleted_at'];
}