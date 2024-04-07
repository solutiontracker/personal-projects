<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempAttendee extends Model
{

    use SoftDeletes;
    protected $table = 'conf_temp_attendees';
    protected $fillable = ['verification_id', 'organizer_id', 'event_id', 'first_name', 'last_name', 'email', 'delegate_number', 'table_number', 'password', 'age', 'gender', 'image', 'company_name', 'title', 'industry', 'about', 'phone', 'facebook', 'twitter', 'linkedin', 'linkedin_profile_id', 'fbprofile_id', 'fb_token', 'fb_url', 'registration_type', 'country', 'organization', 'jobs', 'interests', 'allow_vote', 'initial', 'department', 'site_area_id', 'network_group', 'billing_ref_attendee', 'billing_password', 'isActivated'];
    protected $dates = ['deleted_at'];

}

