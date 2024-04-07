<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExhibitorSetting extends Model
{
    use SoftDeletes;
    protected $table = 'conf_exhibitors_settings';
    protected $fillable = ['id', 'event_id', 'exhibitor_list', 'exhibitorName', 'exhibitorPhone', 'exhibitorEmail', 'contact_person_email', 'exhibitorContact', 'exhibitorTab', 'catTab',
        'sortType', 'hide_attendee', 'show_booth', 'mark_favorite', 'poll', 'document', 'notes','allow_card_reader', 'show_lead_email_button', 'reservation_overview_icone',
        'reservations_view', 'reservation_display_filters', 'reservation_time_slots', 'reservation_available_meeting_rooms', 'reservation_meeting_rooms',
        'reservation_display_colleagues', 'reservation_display_company', 'colleague_book_meeting','enable_signature', 'change_category', 'change_category', 'show_on_native_app_dashboard'];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
