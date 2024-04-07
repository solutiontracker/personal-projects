<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationFormTemplate extends Model
{
    protected $table = 'conf_registration_form_templates';

    protected $fillable = ['alias', 'type', 'title', 'subject', 'template', 'content', 'template_type', 'event_id', 'registration_form_id'];
}
