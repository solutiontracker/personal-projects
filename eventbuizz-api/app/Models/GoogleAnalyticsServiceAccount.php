<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoogleAnalyticsServiceAccount extends Model
{
    use SoftDeletes;
    
    protected $table = 'conf_google_analytics_service_accounts';
    protected $fillable = ['service_email', 'ga_views_count', 'status'];

}