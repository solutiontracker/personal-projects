<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventBadge extends Model
{
    use SoftDeletes;
    protected $attributes = [
        'heading_color' => '',
    ];
    protected $table = 'conf_event_badges';
    protected $fillable = ['id', 'event_id', 'template_type', '	heading_color', 'company_color', 'tracks_color','delegate_Color','table_Color','logo','logoType','footer_bg_color','footer_text_color'];

    public $timestamps = false;
}
