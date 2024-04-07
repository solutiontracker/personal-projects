<?php

namespace App\Http\Controllers\Wizard;

use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\OrganizerRepository;
use App\Http\Controllers\Wizard\Requests\Organizer\OrganizerRequest;
use App\Models\Organizer;


class OrganizerController extends Controller
{
    protected $organizerRepository;

    public $successStatus = 200;

    public function __construct(OrganizerRepository $organizerRepository)
    {
        $this->organizerRepository = $organizerRepository;
    }

    public function profile(OrganizerRequest $request)
    {
        if ($request->isMethod('PUT')) {
            $organizer = organizer_info();
            if ($organizer) {
                $this->organizerRepository->edit($request->all(), $organizer);

                return response()->json([
                    'success' => true,
                    'message' => __('messages.update'),
                ], $this->successStatus);
            }
        } else {
            $organizer = $this->organizerRepository->getOrganizer();

            return response()->json([
                'success' => true,
                'data' => array(
                    'organizer' => $organizer
                ),
            ], $this->successStatus);
        }
    }

    public function change_password(OrganizerRequest $request)
    {
        if ($request->isMethod('PUT')) {
            $organizer = organizer_info();
            if ($organizer) {
                $this->organizerRepository->change_password($request->all(), $organizer);

                return response()->json([
                    'success' => true,
                    'message' => __('messages.update'),
                ], $this->successStatus);
            }
        } else {
            $organizer = organizer_info();
            return response()->json([
                'success' => true,
                'data' => array(
                    'organizer' => $organizer
                ),
            ], $this->successStatus);
        }
    }
}
