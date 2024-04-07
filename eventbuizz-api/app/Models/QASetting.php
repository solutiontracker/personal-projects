<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QASetting extends Model
{
    protected $table = 'conf_qa_settings';
    protected $fillable = ['countdown_time', 'allow_qa_all','parallel_session_projector', 'project_list_time', 'max_project_list_time', 'event_id', 'qa_answers_view', 'send_attendee_email', 'show_attendee_popup', 'moderator', 'projector_program', 'organizer_info', 'archive', 'up_vote', 'order_by_likes', 'background_color', 'headings_color', 'description_color', 'program_section_color', 'font_size','show_profile_images'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
