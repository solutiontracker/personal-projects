<?php


namespace App\Eventbuizz\Repositories;


use App\Models\EventEventSitePhoto;
use App\Models\EventVideo;
use Illuminate\Http\Request;

class EventsiteVideoRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getEventsiteVideos($formInput)
    {
        $event_id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];

        return EventVideo::with([ 'info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); },
            ])->where('event_id', $event_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($formInput['limit']);
    }
}