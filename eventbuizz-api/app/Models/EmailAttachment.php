<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailAttachment extends Model
{
    use SoftDeletes;

    protected $table = 'conf_email_attachments';

    protected $fillable = ['id','email_log_id','filename'];
    
    protected $dates = ['deleted_at'];
}
