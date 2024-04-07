<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpDeskBCCEmail extends Model
{
    protected $table = 'conf_help_desk_bcc_emails';
    protected $fillable = ['event_id', 'group_id', 'bcc_email'];

    use SoftDeletes;
}
