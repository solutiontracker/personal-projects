<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoogleAnalyticsGmailAccount extends Model
{
    use SoftDeletes;
    
    protected $table = 'conf_google_analytics_gmail_accounts';
    protected $fillable = ['email', 'ga_accounts_count', 'refresh_token', 'status'];
        
    public function analyticsAccounts()
    {
        return $this->hasMany('\App\Models\GoogleAnalyticsAccount', "id", "ga_gmail_id");
    }
}