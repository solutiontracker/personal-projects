<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeExportEmailTemplate extends Model
{
    use SoftDeletes;
    protected $table = 'conf_attendee_export_email_template';
    protected $fillable = ['id', 'organizer_id', 'template'];
    protected $dates = ['deleted_at'];
}
