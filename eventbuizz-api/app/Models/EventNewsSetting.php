<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EventNewsSetting extends Model
{
    protected $table = "conf_event_news_settings";
    protected $fillable = ['event_id', 'subscriber_id', 'status', 'news_view'];
    use SoftDeletes;

    public function subscriber()
    {
        return $this->belongsTo(MailingList::class, 'subscriber_id');
    }
}