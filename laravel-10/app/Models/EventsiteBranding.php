<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class EventsiteBranding extends Model {
    protected $attributes = [
        'logo_type' => '0',
    ];
    protected $table = 'conf_eventsite_branding';
    protected $fillable = ['event_id','site_logo','eventsite_register_button','eventsite_other_buttons', 'logo_type'];

}