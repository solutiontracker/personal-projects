<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\EventInfoRepository;
use App\Http\Resources\EventInfoCollection;
use App\Http\Resources\EventInfoMenuCollection;
use App\Models\GeneralInfoMenu;
use App\Models\GeneralInfoPage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GeneralInformationController extends Controller
{

    public $successStatus = 200;

    protected $eventInfoRepository;

    public function __construct(EventInfoRepository $eventInfoRepository)
    {
        $this->eventInfoRepository = $eventInfoRepository;
    }

    public function getInformation(Request $request, $slug, $id = 0){
        $menus = $this->eventInfoRepository->getFrontMenus( $request->all(),'general-info', $id);

        return new EventInfoMenuCollection($menus);
    }
    
    public function getPage(Request $request, $slug, $id = 0){
        $page = $this->eventInfoRepository->getPageData( $request->all(),'general-info', $id);

        return response()->json([
            'data'=> $page
        ]);
    }
}
