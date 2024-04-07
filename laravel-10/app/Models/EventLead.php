<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventLead extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_leads';
    protected $fillable = ['device_id','contact_person_id','type_id','contact_person_type','email','first_name','last_name',
        'notes','event_id','rating', 'image_file', 'initial','term_text'];
    protected $dates = ['deleted_at'];
    public function info()
    {
        return $this->hasMany('\App\Models\LeadInfo', 'lead_id');
    }
   
    public function products()
    {
        return $this->belongsToMany('\App\Models\CatalogueProduct', 'conf_event_lead_product', 'lead_id', 'product_id');
    }
    
    public function signups()
    {
        return $this->belongsToMany('\App\Models\ConsentManagement', 'conf_event_lead_consent', 'lead_id', 'consent_id');
    }

    public function surveyResults() {
        return $this->hasMany('\App\Models\EventAttendeeSurveyResult', 'lead_id');
    }
}
