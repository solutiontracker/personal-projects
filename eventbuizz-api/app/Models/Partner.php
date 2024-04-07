<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use SoftDeletes;
    protected $table = 'conf_partners';
    protected $fillable = ['id', 'organizer_id', 'p_name', 'p_key', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

}