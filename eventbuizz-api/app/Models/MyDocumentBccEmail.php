<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyDocumentBccEmail extends Model
{
    protected $table = 'conf_mydocument_bcc_emails';
    protected $fillable = ['event_id','bcc_email'];
}
