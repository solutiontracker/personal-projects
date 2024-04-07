<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyDocumentSetting extends Model
{
    protected $table = 'conf_mydocument_settings';
    protected $fillable = ['event_id','show_multiple'];
}
