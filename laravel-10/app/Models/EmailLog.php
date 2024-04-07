<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailLog extends Model
{
    use HasFactory;

    protected $connection= 'mysql_email_logs';
    
    protected $table = 'conf_email_log';

    protected $fillable = ['id', 'date', 'transmission_id', 'to', 'from', 'cc', 'bcc', 'subject', 'body', 'event_id', 'headers', 'template', 'organizer_id', 'response'];

    use SoftDeletes;

    public function attachments()
    {
        return $this->hasMany(\EmailAttachments::class, 'email_log_id');
    }
}
