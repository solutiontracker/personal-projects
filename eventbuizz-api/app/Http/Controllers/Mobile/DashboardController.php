<?php
namespace App\Http\Controllers\Mobile;

use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Eventbuizz\Repositories\CheckInOutRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public $successStatus = 200;

    protected $eventSettingRepository;

    protected $checkInOutRepository;

    /**
     * @param EventSettingRepository $eventSettingRepository
     * @param CheckInOutRepository $checkInOutRepository
     */
    public function __construct(EventSettingRepository $eventSettingRepository, CheckInOutRepository $checkInOutRepository)
    {
        $this->eventSettingRepository = $eventSettingRepository;
        $this->checkInOutRepository = $checkInOutRepository;
    }

    /**
     * @param Request $request
     * 
     * @return [type]
     */
    public function lobby(Request $request)
    {
        $request->merge(["alias" => "checkin"]);
        
        $checkin = $this->eventSettingRepository->getEventModule($request->all());

        $checkInOutSetting = $this->checkInOutRepository->getSetting($request->all());

        return response()->json([
            'success' => true,
            'data' => array(
                "checkin" => $checkin,
                "checkInOutSetting" => $checkInOutSetting
            ),
        ], $this->successStatus);
    }
}
