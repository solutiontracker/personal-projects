<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class HubOrganizerEmailTemplate extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'conf_organizer_hub_email_template';

    protected $fillable = ['organizer_id', 'alias', 'title', 'subject', 'template', 'content', 'status','template_type'];

    protected $dates = ['deleted_at'];
}
