<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Eventbuizz\Repositories\AttendeeRepository;

class SettingController extends Controller
{
    public $successStatus = 200;

    protected $attendeeRepository;

    /**
     * @param AttendeeRepository $attendeeRepository
     */
    public function __construct(AttendeeRepository $attendeeRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
    }

    /**
     * @param Request $request
     * @param mixed $action
     * 
     * @return [type]
     */
    public function update_gdpr(Request $request, $event_url, $action)
    {
        $this->attendeeRepository->update_gdpr($request->all(), $action);

        return response()->json([
            'success' => true,
        ], $this->successStatus);
    }
}
