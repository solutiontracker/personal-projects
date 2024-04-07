<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportingAgentEmailTemplate extends Model
{
    use SoftDeletes;
    protected $table = 'conf_reporting_agent_email_template';
    protected $fillable = ['id', 'organizer_id', 'template'];
    protected $dates = ['deleted_at'];
}
