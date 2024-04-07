<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckInUser extends Model
{
    protected $table = 'conf_checkin_user';
    protected $fillable = ['event_id', 'name', 'email', 'password', 'status'];
}
