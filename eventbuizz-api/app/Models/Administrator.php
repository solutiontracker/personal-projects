<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Administrator extends Model
{

    use SoftDeletes;
    protected $table = 'conf_administrator';
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'status', 'type', 'updated_at', 'created_at'];
    protected $dates = ['deleted_at'];

}
