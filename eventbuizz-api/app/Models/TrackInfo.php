<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackInfo extends Model {
    use SoftDeletes;
    protected $table = 'conf_tracks_info';
    protected $fillable = ['name','value','track_id','languages_id','status'];
    protected $dates = ['deleted_at'];

    public function scopeOfLanguage($query, $id)
    {
        return $query->where('languages_id', $id);
    }
}