<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HubAutoLogin extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'conf_hub_auto_logins';
    protected $fillable = ['user_id','token','session_expiry_time'];
    protected $dates = ['deleted_at'];
}
