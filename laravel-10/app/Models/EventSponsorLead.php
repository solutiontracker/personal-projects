<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSponsorLead extends Model
{
    protected $table = 'conf_event_sponsor_leads';
    protected $fillable = ['organizer_id','event_id','sponsor_id','contact_person_id','attendee_id','notes', 'rating_star', 'date_time','image_file','permission_allowed','term_text','initial'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
