<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\EventsiteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventSiteController extends Controller
{
    public $successStatus = 200;

    protected $eventSiteRepository;

    public function __construct(EventsiteRepository $eventSiteRepository)
    {
        $this->eventSiteRepository = $eventSiteRepository;
    }

    public function getManagePackagesListing(Request $request)
    {
        $listing = $this->eventSiteRepository->getManagePackagesListing($request->all());
        $currency = $this->eventSiteRepository->getPackageCurrency($request->all());
        return response()->json([
            'success' => true,
            'data'=> $listing,
            'currency'=> $currency
        ]);
    }
}
