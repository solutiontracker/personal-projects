<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakerSetting extends Model
{
    protected $attributes = [
        'tab' => '0',
    ];
    use Observable;
    protected $table = 'conf_speaker_settings';
    protected $fillable = ['event_id', 'phone', 'email', 'title', 'department', 'company_name', 'show_country', 'contact_vcf',
        'program', 'category_group', 'show_group', 'show_document', 'initial', 'chat', 'hide_attendee', 'tab', 'default_display', 'order_by',
        'registration_site_limit', 'poll', 'document', 'delegate_number', 'network_group', 'table_number', 'organization', 'interest', 'bio_info', 'show_custom_field', 'show_industry', 'show_job_tasks'];
}

