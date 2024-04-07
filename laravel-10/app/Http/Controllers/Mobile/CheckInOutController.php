<?php
namespace App\Http\Controllers\Mobile;

use App\Eventbuizz\Repositories\CheckInOutRepository;
use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Eventbuizz\Repositories\OrganizerRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;

class CheckInOutController extends Controller
{
    public $successStatus = 200;

    protected $checkInOutRepository;

    protected $eventSettingRepository;

    /**
     * @param CheckInOutRepository $checkInOutRepository
     * @param EventSettingRepository $eventSettingRepository
     * @param organizerRepository $organizerRepository
     */
    public function __construct(CheckInOutRepository $checkInOutRepository, EventSettingRepository $eventSettingRepository, OrganizerRepository $organizerRepository)
    {
        $this->checkInOutRepository = $checkInOutRepository;
        $this->eventSettingRepository = $eventSettingRepository;
        $this->organizerRepository = $organizerRepository;
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function index(Request $request)
    {
        $event = $request->event;

        $labels = $event['labels'];

        $setting = $this->checkInOutRepository->getSetting($request->all());

        $history = $this->checkInOutRepository->getHistory($request->all());

        $perDayCheckInResults = $this->checkInOutRepository->perDayCheckInResults($request->all());

        $perDayCheckOutResults = $this->checkInOutRepository->perDayCheckOutResults($request->all());

        $enableEvent = false;

        $currentDate = date("Y-m-d");

        if ($currentDate >= $event['start_date']) {
            if ($currentDate > $event['end_date']) {
                $enableEvent = false;
                $eventStatusMsg = $labels['CHECKIN_EVENT_ENDED_MSG'];
            } else {
                $enableEvent = true;
            }
        } else {
            $eventStatusMsg = $labels['CHECKIN_STATUS_MESSAGE'] . ' ' . \Carbon\Carbon::parse($event['start_date'])->format('d.m.Y') . "";
            $enableEvent = false;
        }

        //check settings and current status
        $enableCheckinWithoutLocatiom = true;
        
        if (count($history) == 0) {
            $status = "start";
            $enableCheckinWithoutLocatiom = true;
        } else if ($setting['type'] == "single") {
            if ($setting['single_type'] == "per_event") {
                if ($history[0]['checkin'] == "" || $history[0]['checkout'] == '0000-00-00 00:00:00') {
                    if ($history[0]['checkin'] == "") {
                        $status = "check-in";
                        $enableCheckinWithoutLocatiom = true;
                    } else {
                        $enableCheckinWithoutLocatiom = false;
                        $status = "check-out";
                    }
                } else {
                    $enableCheckinWithoutLocatiom = false;
                    $status = "attended";
                }
            } else if ($setting['single_type'] == "per_day") {
                // In case of #per_day attendee cna attend event at one time in one day and next time on next day of event
                if ($perDayCheckInResults->checkin == "") {
                    $enableCheckinWithoutLocatiom = true;
                    $status = "check-in";
                } else if ($perDayCheckOutResults->checkout == '0000-00-00 00:00:00') {
                    $enableCheckinWithoutLocatiom = false;
                    $status = "check-out";
                } else {
                    $enableCheckinWithoutLocatiom = false;
                    $status = "attended";
                }
            }
        } else {
            if ($perDayCheckOutResults->checkout != '0000-00-00 00:00:00') {
                $status = "check-in";
            } else {
                $status = "check-out";
            }
        }

        $request->merge(["alias" => "checkin"]);
        
        $checkin = $this->eventSettingRepository->getEventModule($request->all());

        $checkInOutSetting = $this->checkInOutRepository->getSetting($request->all());

        return response()->json([
            'success' => true,
            'data' => array(
                "attendee" => $request->attendee_detail,
                "setting" => $setting,
                "history" => $history,
                "enableEvent" => $enableEvent,
                "enableCheckinWithoutLocatiom" => $enableCheckinWithoutLocatiom,
                "status" => $status,
                "eventStatusMsg" => $eventStatusMsg,
                "checkin" => $checkin,
                "checkInOutSetting" => $checkInOutSetting,
            ),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function save(Request $request)
    {
        if(isset($request->action) && $request->action == "program-checkin") {

            $response = $this->checkInOutRepository->checkInOutProgram($request->all());
            
            //call thirdpart call to update socket for event center
            $orgainzer = $this->organizerRepository->fetchOrganizer($request->all());

            $client = new \GuzzleHttp\Client(['base_uri' => config('app.eventcenter_url')]);

            $client->request('POST', '/_admin/checkinout/push_check_in_out_program_history_to_socket/'.$request->event['id'].'/'.$request->program_id, [
                "headers" => [
                    "Authorization" => $orgainzer->api_key
                ]
            ]);
            
            //End

            return response()->json($response, $this->successStatus);

        } else {

            $data = $this->checkInOutRepository->save($request->all());

            //call thirdpart call to update socket for event center
            $orgainzer = $this->organizerRepository->fetchOrganizer($request->all());

            $client = new \GuzzleHttp\Client(['base_uri' => config('app.eventcenter_url')]);

            $response = $client->request('POST', '/_admin/checkinout/push_check_in_out_history_to_socket/'.$request->event['id'], [
                "headers" => [
                    "Authorization" => $orgainzer->api_key
                ]
            ]);
            
            $response = json_decode($response->getBody());
            //End

            $data['history'] = $this->checkInOutRepository->getHistory($request->all());

            return response()->json([
                'success' => true,
                'data' => $data,
            ], $this->successStatus);
        
        }
        
        
    }
}
