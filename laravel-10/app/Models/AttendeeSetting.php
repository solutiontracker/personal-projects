<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeSetting extends Model
{
    use Observable;
    
    use SoftDeletes;

    protected $table = 'conf_attendee_settings';

    protected $fillable = ['event_id', 'phone', 'email', 'title', 'organization', 'department', 'company_name', 'contact_vcf', 'linkedin', 'program', 'group', 'tab', 'initial', 'network_group', 'table_number', 'delegate_number', 'voting', 'image_gallery','default_display', 'create_profile', 'facebook_enable', 'default_password', 'hide_password', 'default_password_label', 'forgot_link', 'attendee_reg_verification', 'validate_attendee_invite', 'domain_names', 'interest', 'show_country', 'show_custom_field', 'password_lenght', 'strong_password','enable_foods', 'attendee_my_group', 'authentication', 'display_registration_invoice', 'export_original_cpr_number','send_email_to_organizer', 'validate_attendee_invite_with_domain'];
}
