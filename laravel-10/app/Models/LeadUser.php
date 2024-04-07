<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class LeadUser extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;
    protected $table = 'conf_leads_user';
    protected $fillable = ['event_id', 'name', 'email', 'password', 'status','phone','verified','approved'];
    protected $hidden =["password"];
}