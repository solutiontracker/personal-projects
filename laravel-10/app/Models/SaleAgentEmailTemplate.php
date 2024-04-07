<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleAgentEmailTemplate extends Model
{
    use SoftDeletes;
    protected $table = 'conf_sales_agent_email_template';
    protected $fillable = ['id', 'organizer_id', 'template'];
    protected $dates = ['deleted_at'];
}
