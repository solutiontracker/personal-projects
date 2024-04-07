<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingListCampaignLog extends Model
{
    use SoftDeletes;
    protected $table = 'conf_mailing_list_campaign_logs';
    protected $fillable = ['id','user_id', 'user_email', 'campaign_id', 'created_at','updated_at'];

}