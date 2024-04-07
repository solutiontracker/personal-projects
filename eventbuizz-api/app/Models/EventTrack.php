<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTrack extends Model
{
    protected $table = 'conf_event_tracks';
    protected $fillable = ['parent_id', 'sort_order', 'event_id', 'status'];
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\TrackInfo', 'track_id');
    } 

    public function programs()
    {
        return $this->belongsToMany('\App\Models\EventAgenda', 'conf_event_agenda_tracks', 'track_id', 'agenda_id');
    }

    public function sub_tracks()
    {
        return $this->hasMany('\App\Models\EventTrack', 'parent_id');
    }

}
