<?php
namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTurnListSetting extends Model
{
    protected $table = 'conf_event_turn_list_settings';

    // 'show_awaiting_turnlist',
    use Observable;
    protected $fillable = ['event_id','status','turnlist_attendee_approval','enable_speech_time', 'display_time', 'show_image_turnlist',
        'show_company_turnlist', 'show_title_turnlist', 'show_delegate_turnlist', 'show_network_group_turnlist', 'show_department_turnlist',
        'speak_time', 'turn_project_refresh_time', 'delegate_label', 'network_label', 'department_label', 'time_between_attendees',
        'background_image', 'background_color', 'headings_color', 'description_color', 'program_section_color', 'font_size',
        'show_program_section', 'enable_speech_time_for_moderator', 'ask_to_apeak','av_output_all_template','av_output_active_template',
        'av_output_sub_active_template', 'av_output_next_template', 'active_bg_color','all_bg_color','ask_to_apeak_notes', 'use_group_to_control_request_to_speak'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}

