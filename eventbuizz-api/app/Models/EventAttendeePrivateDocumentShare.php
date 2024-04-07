<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeePrivateDocumentShare extends Model
{
    protected $table = 'conf_event_attendee_private_document_shares';
    protected $fillable = ['id', 'event_id', 'shared_by', 'attendee_id', 'private_document_id','entity_id','entity_type', 'enabled', 'created_at', 'updated_at', 'deleted_at' ];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}

