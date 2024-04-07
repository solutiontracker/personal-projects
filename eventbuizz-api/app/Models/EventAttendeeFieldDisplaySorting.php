<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeFieldDisplaySorting extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_attendee_field_display_sorting';
    
    protected $fillable = ['event_id', 'fields_name', 'sort_order', 'is_visible_to_all', 'is_editable', 'is_private', 'event_attendee_type_id'];
}
