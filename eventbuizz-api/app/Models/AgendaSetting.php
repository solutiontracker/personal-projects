<?php

namespace App\Models;

use App\Traits\Observable;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaSetting extends Model
{
    protected $attributes = [
        'agendaTimer' => '0',
    ];
    
    use Observable;

    protected $table = 'conf_agenda_settings';
    
    protected $fillable = ['event_id', 'session_ratings', 'agenda_list', 'agenda_tab', 'admin_fav_attendee', 'attach_attendee_mobile', 'qa', 'program_fav', 'show_tracks', 'show_attach_attendee', 'agenda_display_time','show_program_dashboard','show_my_program_dashboard', 'agenda_search_filter', 'enable_notes','enable_program_attendee','program_groups', 'agendaTimer'];
}
