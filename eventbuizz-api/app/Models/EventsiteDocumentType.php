<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventsiteDocumentType extends Model
{
    use SoftDeletes;
    protected $hidden = ['pivot'];
    protected $table = 'conf_eventsite_document_types';
    protected $fillable = ['id', 'name', 'registration_form_id', 'event_id', 'is_required', 'sort_order'];
    protected $dates = ['deleted_at'];
}

