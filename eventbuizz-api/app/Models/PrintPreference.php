<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintPreference extends Model
{
    protected $table = 'conf_print_preferences';
    protected $fillable = ['event_id', 'terminal_id', 'category_id'];

}
