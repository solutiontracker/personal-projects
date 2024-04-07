<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutologinTokens extends Model
{
    protected $table = 'autologin_tokens';
    protected $fillable = ['id', 'user_id', 'token', 'path', 'type'];

    public $timestamps = true;
}
