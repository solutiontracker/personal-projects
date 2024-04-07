<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\SubRegistrationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubRegistrationController extends Controller
{
    public $successStatus = 200;

    protected $subRegistrationRepository;

    public function __construct(SubRegistrationRepository $subRegistrationRepository)
    {
        $this->subRegistrationRepository = $subRegistrationRepository;
    }
    
    public function getSubRegistrationAfterLogin(Request $request, $slug) {
        $subreg = $this->subRegistrationRepository->getSubRegistrationAfterLogin($request->all(), $request->user()->id);
        return response()->json([
            'success' => true,
            'data'=> $subreg,
        ]);
    }
    public function getMySubRegistration(Request $request, $slug) {
        
        $subreg = $this->subRegistrationRepository->getMySubRegistration($request->all(), $request->user()->id);
        return response()->json([
            'success' => true,
            'data'=> $subreg
        ]);
    }
    public function saveSubRegistration(Request $request, $slug) {
        $subreg = $this->subRegistrationRepository->saveSubRegistration($request->all(), $request->user()->id);
        return response()->json([
            'success' => true,
            'data'=> $subreg
        ]);
    }

}
