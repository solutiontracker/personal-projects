<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpDeskAnswer extends Model
{
    protected $table = 'conf_help_desk_answers';
    protected $fillable = ['answer','sender_id','help_desk_id','is_admin', 'group_id'];
    use SoftDeletes;
}
