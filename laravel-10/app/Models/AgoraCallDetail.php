<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgoraCallDetail extends Model
{
    use SoftDeletes;
    protected $table = 'conf_agora_call_details';

    protected $guarded = [];

}
