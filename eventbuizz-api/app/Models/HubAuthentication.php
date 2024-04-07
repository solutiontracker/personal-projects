<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HubAuthentication extends Model
{
    use SoftDeletes;
    protected $table = 'conf_hub_authentications';
    protected $fillable = ['email', 'token', 'type', 'expire_at', 'to', 'refrer', 'organizer_id','hub_administrator_id'];
    protected $dates = ['deleted_at'];
}
