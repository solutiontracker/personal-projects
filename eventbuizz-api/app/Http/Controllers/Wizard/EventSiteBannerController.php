<?php

namespace App\Http\Controllers\Wizard;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Eventbuizz\Repositories\EventSiteBannerRepository;

class EventSiteBannerController extends Controller
{
    protected $eventSiteBannerRepository;

    public $successStatus = 200;

    public function __construct(EventSiteBannerRepository $eventSiteBannerRepository)
    {
        $this->eventSiteBannerRepository = $eventSiteBannerRepository;
    }

    public function destroy(Request $request, $id)
    {
        $this->eventSiteBannerRepository->destroy($id);

        return response()->json([
            'success' => true,
        ], $this->successStatus);
    }
}
