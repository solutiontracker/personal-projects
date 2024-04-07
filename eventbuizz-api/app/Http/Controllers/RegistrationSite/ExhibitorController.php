<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\ExhibitorRepository;
use App\Http\Resources\Exhibitor;
use App\Models\EventExhibitor;
use App\Models\EventExhibitorCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
class ExhibitorController extends Controller
{
    public $successStatus = 200;

    protected $exhibitorRepository;

    public function __construct(ExhibitorRepository $exhibitorRepository)
    {
        $this->exhibitorRepository = $exhibitorRepository;
    }

    public function getExhibitors(Request $request, $slug){
        $exhibitors = $this->exhibitorRepository->getEventSiteExhibitors($request->all());
        return Response::json([
            "data"=> [
              'exhibitors' => $exhibitors,
            ] 
        ]);
    }
    
    public function getExhibitorsListing(Request $request, $slug){
        
        $exhibitors = $this->exhibitorRepository->getEventSiteExhibitorsListing($request->all());
        $exhibitorCategories = $this->exhibitorRepository->getExhibitorCategories($request->all());
        return Response::json([
            "data"=> [
                "exhibitors" => $exhibitors,
                "exhibitorCategories" => $exhibitorCategories,
            ]
        ]);
    }

    public function getExhibitorDetail(Request $request, $slug, $exhibitor_id)
    {
        $exhibitor = $this->exhibitorRepository->getEventSiteExhibitorDetail($request->all(), $exhibitor_id);
        $documents = $this->exhibitorRepository->getExhibitorDocument($request->all(), $exhibitor_id);
        return Response::json([
            "data"=> [
                "exhibitor" => $exhibitor,
                "documents" => $documents,
            ]
        ]);
    }

}
