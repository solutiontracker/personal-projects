<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSpeakerFieldDisplaySorting extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_speaker_field_display_sorting';
    protected $fillable = ['event_id', 'fields_name', 'sort_order'];
}
