<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventDeletionLog extends Model {

	protected $table = 'conf_events_deletion_log';

	protected $fillable = [
        'event_id',
        'name',
        'url',
        'soft_deleted_at',
        'hard_deleted_at'
        ];

}
