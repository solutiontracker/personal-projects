<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpDeskInfo extends Model
{
    protected $table = 'conf_help_desk_info';
    protected $fillable = ['name','value','help_desk_id','languages_id','status'];

    use SoftDeletes;
}
