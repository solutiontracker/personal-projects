<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadHubTemplate extends Model
{
    use SoftDeletes;
    protected $table = 'conf_lead_hub_template';
    protected $fillable = ['event_id', 'template_id', 'type_id', 'type', 'title', 'subject', 'template', 'status', 'content','template_type', 'alias'];
    protected $dates = ['deleted_at'];
    
}
