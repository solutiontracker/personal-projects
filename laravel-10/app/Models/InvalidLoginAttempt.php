<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvalidLoginAttempt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'conf_event_invalid_login_attempt';
    protected $fillable = ['event_id', 'email','password', 'message', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];
}
