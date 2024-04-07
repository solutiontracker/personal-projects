<?php
namespace App\Models;

use App\Scopes\EventScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSponsor extends Model
{
    protected $table = 'conf_event_sponsors';
    protected $fillable = ['id', 'event_id', 'name', 'email', 'logo', 'booth', 'phone_number', 'website', 'twitter', 'facebook', 'linkedin', 'ribbons', 'allow_reservations', 'status', 'allow_card_reader', 'login_email', 'password', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany(EventSponsorInfo::class, 'sponsor_id');
    }

    public function categories()
    {
        return $this->belongsToMany(EventCategory::class, 'conf_event_sponsor_categories', 'sponsor_id', 'category_id')->wherePivotNull('conf_event_sponsor_categories.deleted_at');
    }

    public function sponsorsAttendee()
    {
        return $this->hasMany(EventSponsorAttendee::class, 'sponsor_id');
    }

    public function attendeeSponsors()
    {
        return $this->hasMany(SponsorAttendee::class, 'sponsor_id');
    }
    
    /**
     * @param $query
     * @param $id
     * @return mixed
     */
    public function scopeOfEvent($query, $id)
    {
        return $query->where('event_id', $id);
    }

    public function contactPersons()
    {
        return $this->belongsToMany(\App\Models\Attendee::class, 'conf_event_sponsor_attendees', 'sponsor_id', 'attendee_id');
    }

}
