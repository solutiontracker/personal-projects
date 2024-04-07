<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Http\Resources\EventsiteSetting as EventsiteSettingResource;
use App\Models\EventsiteSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventsiteSettingController extends Controller
{
    public $successStatus = 200;

    protected $eventsiteSettingRepository;

    public function __construct(EventSiteSettingRepository $eventsiteSettingRepository)
    {
        $this->eventsiteSettingRepository = $eventsiteSettingRepository;
    }


    public function getEventsiteSettings(Request $request, $slug)
    {
        $event_id = $request->get('event_id');
       return new EventsiteSettingResource(EventsiteSetting::where('event_id', $event_id)->first());
    }
}
