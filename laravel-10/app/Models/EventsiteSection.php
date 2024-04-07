<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class EventsiteSection extends Model {
    protected $attributes = [
        'icon' => '',
    ];
    protected $table = 'conf_eventsite_sections';
    protected $fillable = ['sort_order','event_id','status','alias','version','created_at','updated_at','icon','is_purchased'];
}