<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventReportingAgent extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_reporting_agents';

    protected $fillable = ['id', 'reporting_agent_id', 'event_id'];
    protected $dates = ['deleted_at'];

}
