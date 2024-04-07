<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailStatsLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection= 'mysql_email_logs';

    protected $table= "conf_email_stats_logs";

    protected $fillable=  [ 'event_id',  'organizer_id',  'transmission_id',  'to',  'cc',  'bcc',  'subject',  'bounce',  'delivery',  'click',  'open' ];

    public function info()
    {
        return $this->hasOne(EmailStatsLogInfo::class, 'email_stats_log_id');
    }

    public function attachments()
    {
        return $this->hasMany(EmailAttachment::class, 'email_log_id');
    }
}
