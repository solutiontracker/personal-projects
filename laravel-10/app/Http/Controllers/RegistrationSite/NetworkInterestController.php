<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\NetworkInterestRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NetworkInterestController extends Controller
{
    public $successStatus = 200;

    protected $networkInterestRepository;

    public function __construct(NetworkInterestRepository $networkInterestRepository)
    {
        $this->networkInterestRepository = $networkInterestRepository;
    }

    public function getNetworkInterest(Request $request, $slug) {
        
        $keywords = $this->networkInterestRepository->getNetworkInterestKeywords($request->all(), $request->user()->id);
        return response()->json([
            'data' => $keywords,
        ]);
        
    }

    public function updateNetworkInterest(Request $request, $slug) {
        
        $keywords = $this->networkInterestRepository->updateNetworkInterestKeywords($request->all(), $request->user()->id);
        return response()->json([
            'data' => $keywords,
        ]);

    }

}
