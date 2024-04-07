<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BadgePrintHistory extends Model
{
    protected $table = 'conf_badges_print_history';
    protected $fillable = ['id', 'event_id', 'badge_id', 'badge_for', 'badge_type', 'print_date'];
    public $timestamps = false;
}
