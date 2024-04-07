<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\SurveyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SurveyController extends Controller
{
    public $successStatus = 200;

    protected $surveyRepository;

    public function __construct(SurveyRepository $surveyRepository)
    {
        $this->surveyRepository = $surveyRepository;
    }

    public function getSurveyListing(Request $request, $slug) {
        
        $surveyListing = $this->surveyRepository->getSurveyListing($request->all(), $request->user()->id);
        return response()->json([
            'success' => true,
            'data'=> $surveyListing
        ]);
    }
    
    public function getSurveyDetail(Request $request, $slug, $id) {
        
        $surveyListing = $this->surveyRepository->getSurveyDetail($request->all(), $id, $request->user()->id);
        return response()->json([
            'success' => true,
            'data'=> $surveyListing
        ]);
    }
    public function saveSurveyDetail(Request $request, $slug, $id) {
        
        $surveyListing = $this->surveyRepository->saveSurveyDetail($request->all(), $id, $request->user()->id);
        return response()->json([
            'success' => true,
            'data'=> $surveyListing
        ]);
    }

}
