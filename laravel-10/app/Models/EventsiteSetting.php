<?php

namespace App\Models;

use App\Scopes\EventScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventsiteSetting extends Model
{
    use SoftDeletes;
    protected $attributes = [
        'cancellation_date' => '0000-00-00 00:00:00',
        'registration_end_date' => '0000-00-00 00:00:00',
        'ticket_left' => '0',
        'cancellation_policy' => '0',
        'registration_code' => '0',
        'mobile_phone' => '0',
    ];
    protected $table = 'conf_eventsite_settings';
    protected $fillable = ['event_id','ticket_left','registration_end_date', 'cancellation_date', 'cancellation_end_time', 'registration_code', 'mobile_phone', 'eventsite_public', 'eventsite_signup_linkedin', 'eventsite_signup_fb', 'eventsite_tickets_left', 'eventsite_time_left', 'eventsite_language_menu', 'eventsite_menu', 'eventsite_banners', 'eventsite_location', 'eventsite_date', 'eventsite_footer', 'pass_changeable', 'phone_mandatory', 'attendee_registration_invite_email', 'attach_attendee_ticket', 'attendee_my_profile', 'attendee_my_program', 'attendee_my_billing', 'attendee_my_billing_history', 'attendee_my_sub_registration', 'attendee_my_reg_cancel', 'attendee_go_to_mbl_app', 'goto_eventsite','eventsite_add_calender', 'auto_complete','cancellation_policy', 'new_message_temp','third_party_redirect','third_party_redirect_url','agenda_search_filter', 'attach_my_program', 'use_waitinglist', 'payment_type', 'search_engine_visibility', 'prefill_reg_form', 'quick_register', 'network_interest', 'show_subscriber', 'registration_form_id', 'manage_package'];

}
