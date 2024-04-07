<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCloneLog extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_clone_logs';

    protected $fillable = ['from_event', 'to_event'];

    public function fromEvent()
    {
        return $this->belongsTo(Event::class, 'from_event');
    }

    public function toEvent()
    {
        return $this->belongsTo(Event::class, 'to_event');
    }
}
