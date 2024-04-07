<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeePrivateDocument extends Model
{
    protected $table = 'conf_event_attendee_private_documents';
    protected $fillable = ['id','file_caption','uploaded_filename','stored_filename', 'filesize', 'event_id', 'attendee_id', 'created_at','updated_at','deleted_at' ];

    use SoftDeletes;
}

