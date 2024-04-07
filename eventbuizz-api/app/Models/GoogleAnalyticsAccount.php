<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoogleAnalyticsAccount extends Model
{
    use SoftDeletes;
    
    protected $table = 'conf_google_analytics_accounts';
    protected $fillable = ['ga_gmail_id','ga_account_id', 'ga_account_name', 'ga_properties_count', 'status'];

    public function gmail()
    {
        return $this->belongsTo('\App\Models\GoogleAnalyticsGmailAccount', 'ga_gmail_id', 'id');
    }
        
}