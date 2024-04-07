<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventEmailTemplateLog extends Model
{
    protected $table = 'conf_event_email_template_log';

    protected $attributes = [
        'title' => '',
        'subject' => '',
        'template' => ''
    ];

    protected $fillable = ['title', 'subject', 'template', 'template_id', 'languages_id', 'status'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
