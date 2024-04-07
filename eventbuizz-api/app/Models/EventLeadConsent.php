<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventLeadConsent extends Model {
    use SoftDeletes;
    protected $table = 'conf_event_lead_consent';
    protected $fillable = ['consent_id', 'lead_id'];
}