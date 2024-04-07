<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\EventsiteModuleRepository;
use App\Eventbuizz\Repositories\ModuleRepository;
use App\Http\Resources\EventsiteModuleCollection;
use App\Models\EventSiteModuleOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventsiteModuleController extends Controller
{
    public $successStatus = 200;

    protected $eventsiteModuleRepository;

    public function __construct(EventsiteModuleRepository $eventsiteModuleRepository)
    {
        $this->eventsiteModuleRepository = $eventsiteModuleRepository;
    }

    public function getModules(Request $request, $slug)
    {
        $modules = $this->eventsiteModuleRepository->getModules($request->all());

        return new EventsiteModuleCollection($modules);
    }

    public function topSideMenu(Request $request){
        $header_menu = getMenuInfo($request['event_id']);
        return response()->json([
            'data' => $header_menu,
        ]);
    }
}
