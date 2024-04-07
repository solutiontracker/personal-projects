<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCompetitionTemplate extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_competition_template';
    protected $fillable = ['event_id','deleted_at','created_at','updated_at','status'];
}
