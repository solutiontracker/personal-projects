<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingListCampaignRSSLog extends Model
{
    protected $table = 'conf_mailing_list_campaign_rss_logs';
    protected $fillable = ['id','mailing_list_campaign_id','title', 'link', 'guid', 'pubDate','author','description','created_at'];

}