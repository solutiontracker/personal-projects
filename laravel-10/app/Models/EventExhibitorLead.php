<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventExhibitorLead extends Model
{
    protected $table = 'conf_event_exhibitor_leads';
    protected $fillable = ['organizer_id','event_id','exhibitor_id','contact_person_id','attendee_id','notes', 'rating_star', 'date_time','permission_allowed','image_file','term_text','initial'];

    use SoftDeletes;
}
