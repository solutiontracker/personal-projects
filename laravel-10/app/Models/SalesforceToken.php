<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesforceToken extends Model
{
    use SoftDeletes;
    protected $table = 'salesforce_tokens';
    protected $fillable = ['access_token', 'refresh_token', 'instance_base_url', 'user_id', 'expires', 'issued_at', 'token_body'];
    protected $dates = ['deleted_at'];
}
