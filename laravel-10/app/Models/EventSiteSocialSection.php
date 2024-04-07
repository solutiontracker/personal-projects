<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSiteSocialSection extends Model {
    protected $attributes = [
        'icon' => '',
    ];
    protected $table = 'conf_eventsite_social_sections';
    protected $fillable = ['sort_order','event_id','status','alias','version','created_at','updated_at','icon','is_purchased'];

    public function labels()
    {
        return $this->belongsTo('\App\Models\EventSiteSocialSectionInfo', 'section_id', 'id');
    }
}