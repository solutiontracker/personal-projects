<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventNews extends \Eloquent
{
    use SoftDeletes;

    protected $table = "conf_event_news";
    protected $fillable = ['event_id', 'title', 'body', 'status', 'scheduled_at', 'image'];

    protected $dates = ['deleted_at', 'scheduled_at'];

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULE = 'schedule';
    const STATUS_PUBLISH = 'publish';

    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    public function isDraft(){
        return ($this->status ==  self::STATUS_DRAFT);
    }

    public function isSchedule(){
        return ($this->status == self::STATUS_SCHEDULE);
    }

    public function isPublish(){
        return ($this->status == self::STATUS_PUBLISH);
    }

}