<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpDeskGroup extends Model
{
    protected $table = 'conf_help_desk_groups';
    protected $fillable = ['event_id','status'];

    use SoftDeletes;
}
