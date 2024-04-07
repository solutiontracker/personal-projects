<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadSetting extends Model
{
    use SoftDeletes;
    protected $table = 'conf_leads_settings';
    protected $fillable = ['id', 'event_id','recieve_lead_email_on_save','allow_card_reader', 'show_lead_email_button', 'enable_signature','bcc_emails','access_code','lead_user_without_contact_person','login_with_auth_code','enable_organizer_approval','enable_auto_capture','end_date','end_time','attendees_surveys'];
    protected $dates = ['deleted_at'];
    
}
