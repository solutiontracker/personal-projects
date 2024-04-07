<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSpeakerCategory extends Model
{
    use SoftDeletes;
    protected $attributes = [
        'status' => 0,
    ];
    protected $table = 'conf_event_speaker_categories';
    protected $fillable = ['speaker_id', 'category_id'];
    protected $dates = ['deleted_at'];

}
