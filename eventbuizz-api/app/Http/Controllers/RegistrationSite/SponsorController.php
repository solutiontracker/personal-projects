<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\SponsorsRepository;
use App\Http\Resources\EventSponsor as EventSponsorResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;

class SponsorController extends Controller
{
    public $successStatus = 200;

    protected $sponsorsRepository;

    public function __construct(SponsorsRepository $sponsorsRepository)
    {
        $this->sponsorsRepository = $sponsorsRepository;
    }

    
    /**
     * getSponsors
     *
     * @param  mixed $request
     * @param  mixed $slug
     * @return void
     */
    public function getSponsors(Request $request, $slug){
        //Fetch event ID
        $sponsors = $this->sponsorsRepository->getEventSiteSponsors($request->all());
        return Response::json([
            "data" => [
                "sponsors" => $sponsors,
            ]
        ]);
    }
        
    /**
     * getSponsorsListing
     *
     * @param  mixed $request
     * @param  mixed $slug
     * @return void
     */
    public function getSponsorsListing(Request $request, $slug){
        //Fetch event ID
        $sponsors = $this->sponsorsRepository->getEventSiteSponsorsListing($request->all());
        $sponsorCategories = $this->sponsorsRepository->getSponsorCategories($request->all());
        return Response::json([
            "data"=> [
                "sponsors" => $sponsors,
                "sponsorCategories" => $sponsorCategories,
            ]
        ]);
    }

    public function getSponsorDetail(Request $request, $slug, $sponsor_id)
    {
        $sponsor = $this->sponsorsRepository->getEventSiteSponsorDetail($request->all(), $sponsor_id);
        $documents = $this->sponsorsRepository->getSponsorDocument($request->all(), $sponsor_id);
        return Response::json([
            "data"=> [
                "sponsor" => $sponsor,
                "documents" => $documents,
            ]
        ]);

    }

}
