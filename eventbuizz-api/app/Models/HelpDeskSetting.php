<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpDeskSetting extends Model
{
    protected $table = 'conf_help_desk_settings';
    protected $fillable = ['countdown_time', 'parallel_session_projector', 'project_list_time', 'max_project_list_time', 'event_id', 'help_desk_answers_view', 'send_attendee_email', 'show_attendee_popup', 'moderator', 'projector_program', 'organizer_info', 'archive', 'up_vote', 'order_by_likes','help_desk_tabs', 'background_color', 'headings_color', 'description_color', 'program_section_color', 'font_size','show_profile_images'];

    use SoftDeletes;
}
