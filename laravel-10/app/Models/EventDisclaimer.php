<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventDisclaimer extends Model {
    use SoftDeletes;
    protected $attributes = [
        'disclaimer' => ' '
    ];
    protected $table = 'conf_event_disclaimer';
    protected $fillable = ['event_id','disclaimer','languages_id'];

}