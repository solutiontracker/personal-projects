<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateCampaign extends Model
{
    use SoftDeletes;
    protected $table = 'conf_template_campaign';
    protected $fillable = ['id','parent_id','event_id', 'organizer_id', 'subject', 'list_type','template_id','l_t_id','l_t_type','template','status','schedule_date','schedule_time','sent_datetime','utc_datetime','schedule_repeat','repeat_every_qty','repeat_every_type','repeat_every_on','end_type','end_on','end_after','in_progress','created_at','updated_at'];
    protected $dates = ['deleted_at'];
}