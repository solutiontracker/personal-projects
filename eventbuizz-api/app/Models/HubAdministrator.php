<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HubAdministrator extends Model
{
    use SoftDeletes;
    protected $table = 'conf_hub_administrator';
    protected $fillable = ['organizer_id', 'first_name', 'last_name', 'email', 'password', 'status','company_name'];
    protected $dates = ['deleted_at'];
}
