<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventbuizzApp extends Model
{
    use SoftDeletes;
    protected $table = 'conf_eventbuizz_apps';
    protected $fillable = ['name','logo'];
}



