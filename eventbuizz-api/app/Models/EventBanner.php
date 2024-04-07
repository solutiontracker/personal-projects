<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventBanner extends Model
{
    protected $table = 'conf_event_banners';
    protected $fillable = ['event_id','sponsor_id','exhibitor_id','other_link_url'];

    use SoftDeletes;
}
