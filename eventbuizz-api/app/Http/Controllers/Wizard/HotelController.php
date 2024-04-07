<?php

namespace App\Http\Controllers\Wizard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Wizard\Requests\Hotel\HotelRequest;
use App\Eventbuizz\Repositories\HotelRepository;
use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\ImportRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;

class HotelController extends Controller
{
    protected $hotelRepository;

    protected $importRepository;

    public $successStatus = 200;

    public function __construct(HotelRepository $hotelRepository, ImportRepository $importRepository)
    {
        $this->hotelRepository = $hotelRepository;
        $this->importRepository = $importRepository;
    }

    public function listing(Request $request, $page = 1)
    {
        $event = $request->event;

        $request->merge(['page' =>  $page, 'registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);
        
        $hotels = $this->hotelRepository->listing($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.fetch'),
            'data' => $hotels,
        ], $this->successStatus);

    }

    public function store(HotelRequest $request)
    {
        $event = $request->event;

        $request->merge(['registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);

        $this->hotelRepository->createHotel($request->all());

        EventRepository::add_module_progress($request->all(), "hotel");

        return response()->json([
            'success' => true,
            'message' => __('messages.create'),
        ], $this->successStatus);

    }

    public function update(HotelRequest $request, $id)
    {
        $hotel = \App\Models\EventHotel::find($id);

        if ($hotel) {

            $this->hotelRepository->updateHotel($request->all(), $hotel);

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ], $this->successStatus);

        }
        
        return response()->json([
            'success' => false,
            'message' => "Record not exist.",
        ], $this->successStatus);
    }

    public function destroy(Request $request, $id)
    {
        $hotel = \App\Models\EventHotel::find($id);

        if ($hotel) {

            $this->hotelRepository->deleteHotel($id);

            return response()->json([
                'success' => true,
                'message' => __('messages.delete'),
            ], $this->successStatus);

        }

        return response()->json([
            'success' => false,
            'message' => "Record not exist.",
        ], $this->successStatus);

    }

    public function updateHotelPriceSetting(Request $request)
    {

        $event = $request->event;

        $registration_form_id = $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0;

        $paymentSettings = \App\Models\EventsitePaymentSetting::where('event_id', $request->get('event_id'))->where('registration_form_id', $registration_form_id)->first();

        if ($paymentSettings) {

            $paymentSettings->show_hotel_prices = $request->get('show_hotel_prices');

            $paymentSettings->save();

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ], $this->successStatus);

        }

        return response()->json([
            'success' => false,
            'message' => "Record not exist.",
        ], $this->successStatus);

    }

    public function getHotelPriceSetting(Request $request)
    {
        $event = $request->event;

        $registration_form_id = $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0;

        $paymentSettings = \App\Models\EventsitePaymentSetting::where('event_id', $request->get('event_id'))->where('registration_form_id', $registration_form_id)->first();

        $data = [];

        if ($paymentSettings) {

            $data['show_hotel_prices'] = $paymentSettings->show_hotel_prices;

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => __('messages.fetch'),
            ], $this->successStatus);

        }

        return response()->json([
            'success' => false,
            'message' => __('messages.not_found'),
        ], $this->successStatus);
        
    }

    public function sorting(Request $request)
    {
        $this->hotelRepository->sorting($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    public function export(Request $request)
    {
        if ($request->isMethod('GET')) {

            $event = $request->event;

            $request->merge(['registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);

            $settings = $this->hotelRepository->getExportSettings($request->all());

            $records = $this->hotelRepository->export($request->all());

            $header_data = array();

            foreach ($settings['fields'] as $headers) {
                $header_data[] = $headers['label'];
            }

            array_unshift($records, $header_data);

            $filename = time() . 'hotel.csv';

            $this->importRepository->export($request->all(), $records, $filename, '', false);

        }
    }

    public function bookings(Request $request, $page = 1)
    {
        $event = $request->event;

        $request->merge(['page' =>  $page, 'registration_form_id' => $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);

        $hotels = $this->hotelRepository->bookings($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.fetch'),
            'data' => $hotels,
        ], $this->successStatus);

    }
    
}
