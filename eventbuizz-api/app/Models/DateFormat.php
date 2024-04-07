<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DateFormat extends Model {
    protected $table = 'conf_date_formats';
    protected $fillable = [];
}