<?php

namespace App\Http\Controllers\Lead;

use App\Eventbuizz\Repositories\LeadRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeadController extends Controller
{
    public $successStatus = 200;

    protected $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }
    
    /**
     * getEventDetails
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeadSettings($event_id)
    {
        $leadSettings = $this->leadRepository->getLeadSettings($event_id);
        return response()->json($leadSettings, $this->successStatus);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContactPersonSponsorsExhibitors(Request $request)
    {
        $contactPersonSponsorExhibitors = $this->leadRepository->getContactPersonSponsorsExhibitors($request);
        return response()->json($contactPersonSponsorExhibitors, $this->successStatus);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getScannedLeadAttendeeInfo(Request $request)
    {
       $attendeeInfo = $this->leadRepository->getScannedLeadAttendeeInfo($request);
        return response()->json($attendeeInfo, $this->successStatus);
    }
    
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContactPersonProfileData(Request $request)
    {
        $contactPersonProfileData = $this->leadRepository->getContactPersonProfileData($request);
        return response()->json($contactPersonProfileData, $this->successStatus);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncLeadsFromDevice(Request $request)
    {
        $syncLeadsFromDevice = $this->leadRepository->syncLeadsFromDevice($request);
        return response()->json($syncLeadsFromDevice, $this->successStatus);
    }

    public function getLeads(Request $request)
    {
        $getLeads = $this->leadRepository->getLeads($request);
        return response()->json($getLeads, $this->successStatus);
    }
    
    public function getProfileLeaderBoard(Request $request)
    {
        $getProfileLeaderBoard = $this->leadRepository->getProfileLeaderBoard($request);
        return response()->json($getProfileLeaderBoard, $this->successStatus);
    }
    
}
