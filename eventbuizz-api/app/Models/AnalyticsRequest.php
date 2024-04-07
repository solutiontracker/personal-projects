<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnalyticsRequest extends Model
{
    use SoftDeletes;
    protected $table = 'conf_analytics_requests';
    protected $fillable = ['event_code','event_name','analytics_email','analytics_code','profile_id','status','organizer_id','organizer_name'];

    /**
     * Relationships
     */
    public function event()
    {
        return $this->belongsTo('\App\Models\Event', 'event_code', 'id');
    }

    public function organizer()
    {
        return $this->belongsTo('\App\Models\Organizer','organizer_id','id');
    }
}
