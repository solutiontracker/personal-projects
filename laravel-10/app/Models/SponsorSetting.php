<?php
namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SponsorSetting extends Model
{
    use SoftDeletes;
    use Observable;
    protected $table = 'conf_sponsors_settings';
    protected $fillable = ['id', 'event_id', 'sponsor_list', 'sponsorName', 'sponsorPhone', 'sponsorEmail', 'contact_person_email', 'sponsorContact', 'sponsorTab', 'catTab', 'sortType', 'hide_attendee',
            'mark_favorite', 'poll', 'document','allow_card_reader', 'show_lead_email_button', 'reservation_overview_icone', 'reservations_view', 'reservation_display_filters', 'reservation_time_slots',
        'reservation_available_meeting_rooms', 'reservation_meeting_rooms', 'reservation_display_colleagues', 'reservation_display_company', 'colleague_book_meeting','enable_signature', 'change_category', 'show_on_native_app_dashboard'];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
