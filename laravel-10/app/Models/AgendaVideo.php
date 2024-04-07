<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaVideo extends Model {
    use SoftDeletes;
	protected $table = 'conf_agenda_video';
    protected $fillable = ['name', 'type', 'size', 'url', 'filename', 'agenda_id', 'plateform', 'status', 'is_live', 'thumbnail', 'is_iframe', 'iframe_data', 'moderator', 'streaming_url', 'streaming_key', 'private', 'sessionId', 'broadcasting_service', 'broadcaster', 'broadcasting_id', 'archiveId'];
}