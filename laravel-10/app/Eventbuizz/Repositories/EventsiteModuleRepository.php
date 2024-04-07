<?php


namespace App\Eventbuizz\Repositories;


use App\Models\EventSiteModuleOrder;
use Illuminate\Http\Request;

class EventsiteModuleRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getModules($formInput){
        $lang_id = $formInput['language_id'];
        $event_id = $formInput['event_id'];

        return EventSiteModuleOrder::with([
            'info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); },
        ])->where('status', 1)
        ->where('event_id', $event_id)
        ->orderBy('sort_order')->orderBy('id')->get();
    }


}