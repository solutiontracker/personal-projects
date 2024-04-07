<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadInfo extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_lead_info';
    protected $fillable = ['name', 'value', 'languages_id', 'lead_id', 'device_id'];
    
    public function lead()
    {
        return $this->belongsTo('\App\Models\EventLead', 'lead_id', 'id');
    }
 
}