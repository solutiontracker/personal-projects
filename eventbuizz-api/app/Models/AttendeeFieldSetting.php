<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeFieldSetting extends Model
{
    use Observable;
    protected $table = 'conf_attendee_field_settings';
    protected $fillable = ['event_id', 'initial', 'first_name', 'last_name', 'email', 'password', 'phone_number', 'age', 'gender', 'first_name_passport', 'last_name_passport', 'birth_date', 'spoken_languages', 'profile_picture', 'website', 'linkedin', 'facebook', 'twitter', 'company_name', 'title', 'department', 'organization', 'employment_date', 'custom_field', 'country', 'industry', 'job_tasks', 'interests', 'about', 'network_group', 'delegate_number', 'table_number', 'event_language'];
}
