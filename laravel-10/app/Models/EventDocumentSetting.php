<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventDocumentSetting extends Model
{
    protected $table = 'conf_event_document_settings';
    protected $fillable = ['event_id','show_documents_notes'];
}
