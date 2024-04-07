<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventsiteDocument extends Model
{
    use SoftDeletes;

    protected $table = 'conf_eventsite_documents';
    protected $fillable = ['id', 'file_size', 'file_name', 'event_id', 'registration_form_id', 'sort_order'];
    protected $dates = ['deleted_at'];
}

