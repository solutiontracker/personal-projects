<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\AlertRepository;
use App\Http\Resources\Alert as AlertResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AlertsController extends Controller
{
    public $successStatus = 200;

    protected $alertRepository;

    public function __construct(AlertRepository $alertRepository)
    {
         $this->alertRepository = $alertRepository;
    }

    public function getAlerts(Request $request, $slug)
    {
        $alerts = $this->alertRepository->getAllAlerts($request->all());
        return AlertResource::collection($alerts);
    }

}
