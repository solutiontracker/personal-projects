<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingListCampaign extends Model
{
    use SoftDeletes;
    protected $table = 'conf_mailing_list_campaign';

    protected $fillable = ['id','parent_id','event_id', 'organizer_id', 'subject','template_id','mailing_list_id','sender_name','template','status','schedule_date','schedule_time','sent_datetime','utc_datetime','schedule_repeat','repeat_every_qty','repeat_every_type','repeat_every_on','end_type','end_on','end_after','rss_link','timezone_id','in_progress','created_at','updated_at'];
    protected $dates = ['deleted_at'];
}