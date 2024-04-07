<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportingAgentSetting extends Model
{
    use Observable;
    protected $table = 'conf_reporting_agents_settings';
    protected $fillable = ['organizer_id','order_number','order_date', 'name_email', 'name_email', 'company', 'amount', 'sales_agent', 'order_status'];
    use SoftDeletes;
    protected  $dates = ['deleted_at'];
}
