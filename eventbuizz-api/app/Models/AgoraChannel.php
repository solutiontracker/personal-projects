<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgoraChannel extends Model
{
    protected $table = 'agora_channels';
    protected $fillable = ['channel'];
    use SoftDeletes;
}
