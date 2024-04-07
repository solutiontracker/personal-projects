<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventsiteSetting extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $eventsiteSettings = [];

        if($this->resource) {
            $eventsiteSettings = [
                'event_id' => $this->event_id,
                'ticket_left' => $this->ticket_left,
                'registration_end_date' => $this->registration_end_date,
                'registration_end_time' => $this->registration_end_time,
                'cancellation_date' => $this->cancellation_date,
                'cancellation_end_time' => $this->cancellation_end_time,
                'cancellation_policy' => $this->cancellation_policy,
                'registration_code' => $this->registration_code,
                'mobile_phone' => $this->mobile_phone,
                'eventsite_public' => $this->eventsite_public,
                'eventsite_signup_linkedin' => $this->eventsite_signup_linkedin,
                'eventsite_signup_fb' => $this->eventsite_signup_fb,
                'eventsite_tickets_left' => $this->eventsite_tickets_left,
                'eventsite_time_left' => $this->eventsite_time_left,
                'eventsite_language_menu' => $this->eventsite_language_menu,
                'eventsite_menu' => $this->eventsite_menu,
                'eventsite_banners' => $this->eventsite_banners,
                'eventsite_location' => $this->eventsite_location,
                'eventsite_date' => $this->eventsite_date,
                'eventsite_footer' => $this->eventsite_footer,
                'pass_changeable' => $this->pass_changeable,
                'phone_mandatory' => $this->phone_mandatory,
                'attendee_registration_invite_email' => $this->attendee_registration_invite_email,
                'attach_attendee_ticket' => $this->attach_attendee_ticket,
                'attendee_my_profile' => $this->attendee_my_profile,
                'attendee_my_program' => $this->attendee_my_program,
                'attendee_my_billing' => $this->attendee_my_billing,
                'attendee_my_billing_history' => $this->attendee_my_billing_history,
                'attendee_my_reg_cancel' => $this->attendee_my_reg_cancel,
                'newtwork_interest' => $this->newtwork_interest,
                'show_survey' => $this->show_survey,
                'show_subscriber' => $this->show_subscriber,
                'payment_type' => $this->payment_type,
                'use_waitinglist' => $this->use_waitinglist,
                'goto_eventsite' => $this->goto_eventsite,
                'attendee_go_to_mbl_app' => $this->attendee_go_to_mbl_app,
                'eventsite_add_calender' => $this->eventsite_add_calender,
                'registration_after_login' => $this->registration_after_login,
                'send_invoice_email' => $this->send_invoice_email,
                'attach_invoice_email' => $this->attach_invoice_email,
                'attach_calendar_to_email' => $this->attach_calendar_to_email,
                'auto_complete' => $this->auto_complete,
                'new_message_temp' => $this->new_message_temp,
                'go_to_account' => $this->go_to_account,
                'go_to_home_page' => $this->go_to_home_page,
                'attendee_my_sub_registration' => $this->attendee_my_sub_registration,
                'third_party_redirect' => $this->third_party_redirect,
                'agenda_search_filter' => $this->agenda_search_filter,
                'third_party_redirect_url' => $this->third_party_redirect_url,
                'attach_my_program' => $this->attach_my_program,
                'quick_register' => $this->quick_register,
                'prefill_reg_form' => $this->prefill_reg_form,
                'search_engine_visibility' => $this->search_engine_visibility,
                'attach_invoice_email_online_payment' => $this->attach_invoice_email_online_payment,
                'register_action' => $this->register_action,
                'reg_site_footer_image' => $this->reg_site_footer_image,
                'use_reg_form_footer' => $this->use_reg_form_footer,
                'show_eventsite_breadcrumbs' => $this->show_eventsite_breadcrumbs,
                'manage_package' => $this->manage_package,
                'calender_show' => $this->calender_show,
            ];
        }
        return $eventsiteSettings;
    }
}
