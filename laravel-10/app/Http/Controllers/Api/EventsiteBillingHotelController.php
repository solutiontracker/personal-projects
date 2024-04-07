<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Eventbuizz\Repositories\HotelRepository;

class EventsiteBillingHotelController extends Controller
{
    public $successStatus = 200;

    protected $attendeeRepository;

    /**
     * @param HotelRepository $HotelRepository
     */
    public function __construct(HotelRepository $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function index(Request $request)
    {
        $hotels = $this->hotelRepository->searchHotels($request->all());

        return response()->json([
            'success' => true,
            'data' => array(
                "hotels" => $hotels,
            ),
        ], $this->successStatus);
    }
}
