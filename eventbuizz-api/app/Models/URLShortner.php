<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class URLShortner extends Model
{
    protected $table = 'conf_urlshortner';
    protected $fillable = ['id', 'attendee_id', 'event_id', 'organizer_id', 'long_url', 'uuid'];
    public $timestamps = false;
}
