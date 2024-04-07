<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Http\Resources\EventSettingCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventSettingController extends Controller
{

    public $successStatus = 200;

    protected $eventSettingRepository;

    public function __construct(EventSettingRepository $eventSettingRepository)
    {
        $this->eventSettingRepository = $eventSettingRepository;
    }

    public function getSettings(Request $request, $slug)
    {
        $settings = $this->eventSettingRepository->getSettings($request->get('event_id'));

        return new EventSettingCollection($settings);
    }
}
