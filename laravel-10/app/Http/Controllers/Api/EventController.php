<?php
namespace App\Http\Controllers\Api;

use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\OrganizerRepository;
use App\Http\Controllers\Api\Requests\EventRequest;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public $successStatus = 200;

    protected $eventRepository;

    /**
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param EventRequest $request
     * @param mixed $id
     *
     * @return [type]
     */
    public function create(EventRequest $request)
    {
        if (\Route::is('api-event-create')) {
            $packages = (array) OrganizerRepository::getPackages(["organizer_id" => request()->organizer_id]);
            if (!empty($packages)) {
                $package_id = array_key_first($packages);

                $language = \App\Models\Language::where('lang_code', $request->language_code)->first();

                if ($language) {

                    $country = \App\Models\Country::where('code_2', $request->country_code)->first();

                    $timezone_id = \App\Models\Timezone::whereLike('name', $request->timezone)->value('id');

                    if ($country) {
                        //validate request data
                        $request->merge([
                            'from_event_id' => $request->copy_from_event_id,
                            'timezone_id' => ($timezone_id ? $timezone_id : 1),
                            'type' => 1,
                            'assign_package_id' => $package_id,
                            'language_id' => $language->id,
                            'country_id' => $country->id,
                            'start_date' => ($request->start_date ? \Carbon\Carbon::parse($request->start_date)->toDateString() : ''),
                            'end_date' => ($request->end_date ? \Carbon\Carbon::parse($request->end_date)->toDateString() : ''),
                            'start_time' => ($request->start_time ? \Carbon\Carbon::parse($request->start_time)->toTimeString() : ''),
                            'end_time' => ($request->end_time ? \Carbon\Carbon::parse($request->end_time)->toTimeString() : ''),
                            'third_party_redirect_url' => ($request->third_party_redirect_url ? $request->third_party_redirect_url : ''),
                            'description' => ($request->description ? $request->description : ''),
                            'cancellation_date' => ($request->cancellation_date ? \Carbon\Carbon::parse($request->cancellation_date)->toDateTimeString() : ''),
                            'registration_end_date' => ($request->registration_end_date ? \Carbon\Carbon::parse($request->registration_end_date)->toDateTimeString() : ''),
                        ]);

                        $event = $this->eventRepository->store($request->all());

                        return response()->json([
                            'success' => true,
                            'message' => __('messages.create'),
                            'data' => array(
                                "event" => $event,
                            ),
                        ], $this->successStatus);
                    } else {
                        return response()->json([
                            'success' => 0,
                            'error' => "Invalid country code Request!",
                        ], 422);
                    }
                } else {
                    return response()->json([
                        'success' => 0,
                        'error' => "Invalid language code Request!",
                    ], 422);
                }

            } else {
                return response()->json([
                    'success' => 0,
                    'error' => "Invalid package ID!",
                ], 422);
            }
        }
    }
}
