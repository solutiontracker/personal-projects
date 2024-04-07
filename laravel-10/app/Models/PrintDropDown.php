<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintDropDown extends Model
{
    protected $table = 'conf_print_dropdown';
    protected $fillable = ['event_id', 'name', 'value', 'type', 'sort_order'];

}
