<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetitionSetting extends Model {
    protected $table = 'conf_competition_settings';
    protected $fillable = ['event_id','template','languages_id'];
}