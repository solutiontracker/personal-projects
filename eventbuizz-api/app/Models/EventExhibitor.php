<?php

namespace App\Models;

use App\Scopes\EventScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventExhibitor extends Model
{
    protected $table = 'conf_event_exhibitors';
    protected $fillable = ['id', 'event_id', 'name', 'email', 'logo', 'booth', 'phone_number', 'website', 'twitter', 'facebook', 'linkedin', 'allow_reservations', 'status', 'allow_card_reader', 'created_at', 'updated_at', 'deleted_at','url'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];


    public function categories()
    {
        return $this->belongsToMany(EventCategory::class, 'conf_event_exhibitor_categories', 'exhibitor_id', 'category_id')->wherePivotNull('conf_event_exhibitor_categories.deleted_at');
    }


    public function contactPersons()
    {
        return $this->belongsToMany(\App\Models\Attendee::class, 'conf_event_exhibitor_attendees', 'exhibitor_id', 'attendee_id');
    }

    public function info()
    {
        return $this->hasMany(ExhibitorInfo::class, 'exhibitor_id');
    }

    public function exhibitorsAttendee()
    {
        return $this->hasMany(EventExhibitorAttendee::class, 'exhibitor_id');
    }

    public function attendeeExhibitors()
    {
        return $this->hasMany(ExhibitorAttendee::class, 'exhibitor_id');
    }
    
}
