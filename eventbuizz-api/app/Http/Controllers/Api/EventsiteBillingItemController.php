<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Eventbuizz\Repositories\EventsiteBillingItemRepository;

class EventsiteBillingItemController extends Controller
{
    public $successStatus = 200;

    protected $attendeeRepository;

    /**
     *  @param EventsiteBillingItemRepository $eventsiteBillingItemRepository
     */
    public function __construct(EventsiteBillingItemRepository $eventsiteBillingItemRepository)
    {
        $this->eventsiteBillingItemRepository = $eventsiteBillingItemRepository;
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function index(Request $request)
    {
        $setting = $request->event['eventsite_setting'];

        $request->merge(["is_free" => ($setting->payment_type == 0 ? 1 : 0), "rule" => true]);

        $items = $this->eventsiteBillingItemRepository->getRegistrationItems($request->all());

        return response()->json([
            'success' => true,
            'data' => array(
                "registrationItems" => $items['registrationItems']
            ),
        ], $this->successStatus);
    }
}
