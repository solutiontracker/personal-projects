<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakerRequest extends Model
{
    use SoftDeletes;

    protected $table = 'conf_speaker_requests';
    protected $fillable = ['event_id', 'attendee_id', 'agenda_id'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
