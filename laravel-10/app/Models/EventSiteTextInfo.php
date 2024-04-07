<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSiteTextInfo extends Model
{
    protected $table = 'conf_event_site_text_info';
    protected $fillable = ['id', 'name', 'value', 'text_id', 'languages_id', 'status', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function labels()
    {
        return $this->belongsTo('\App\Models\EventSiteText', 'text_id', 'id');
    }
}
