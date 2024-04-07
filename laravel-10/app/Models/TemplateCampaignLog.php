<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateCampaignLog extends Model
{
    protected $table = 'conf_template_campaign_logs';
    protected $fillable = ['id','attendee_id', 'attendee_email', 'campaign_id', 'created_at','updated_at'];

}