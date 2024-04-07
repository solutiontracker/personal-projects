<?php
namespace App\Http\Controllers\Api;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Requests\order\CreateOrder;
use App\Eventbuizz\Repositories\HotelRepository;
use App\Eventbuizz\Repositories\EventsiteBillingVoucherRepository;
use App\Eventbuizz\Repositories\EventsiteBillingItemRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;

class EventsiteBillingOrderController extends Controller
{
    public $successStatus = 200;

    protected $attendeeRepository;

    /**
     * @param AttendeeRepository $attendeeRepository
     * @param HotelRepository $HotelRepository
     *  @param EventsiteBillingItemRepository $eventsiteBillingItemRepository
     */
    public function __construct(AttendeeRepository $attendeeRepository, HotelRepository $hotelRepository, EventsiteBillingItemRepository $eventsiteBillingItemRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->hotelRepository = $hotelRepository;
        $this->eventsiteBillingItemRepository = $eventsiteBillingItemRepository;
        $this->hotelRepository = $hotelRepository;
    }

    /**
     * @param CreateOrder $request
     *
     * @return [type]
     */
    public function createOrder(CreateOrder $request)
    {
        $request_data = json_decode($request->getContent(), true);

        $request_data = $this->makeRequestData($request_data, $request);

        $setting = $request->event['eventsite_setting'];

        request()->merge([ "panel" => "attendee", 'is_new_flow' => 0]); 

        //create order
        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder($request_data);

        $attendee_billing = $EBOrder->getMainAttendee()->getBillingModel();

        if($setting->payment_type == 1 && in_array($attendee_billing->billing_company_type, ['card', 'private']) && $EBOrder->getOrderGrandTotal() > 0) {
            request()->merge([ "draft" => true]); 
        }
        
        $EBOrder->updateModelAttribute('order_number', $EBOrder->_generateOrderNumber());

        $EBOrder->save();

        return response()->json([
            'success' => true,
            'data' => array(
                "order" => array(
                    'code' => $EBOrder->getModel()->code,
                    'coupon_id' => $EBOrder->getModel()->coupon_id,
                    'is_voucher' => $EBOrder->getModel()->is_voucher,
                    'order_number' => $EBOrder->getModel()->order_number,
                    'event_id' => $EBOrder->getModel()->event_id,
                    'language_id' => $EBOrder->getModel()->language_id,
                    'sale_agent_id' => $EBOrder->getModel()->sale_agent_id,
                    'sale_type' => $EBOrder->getModel()->sale_type,
                    'vat' => $EBOrder->getModel()->vat,
                    'vat_amount' => $EBOrder->getModel()->vat_amount,
                    'grand_total' => $EBOrder->getModel()->grand_total,
                    'summary_sub_total' => $EBOrder->getModel()->summary_sub_total,
                    'total_attendee' => $EBOrder->getModel()->total_attendee,
                    'discount_type' => $EBOrder->getModel()->discount_type,
                    'discount_amount' => $EBOrder->getModel()->discount_amount,
                    'quantity_discount' => $EBOrder->getModel()->quantity_discount,
                    'order_date' => $EBOrder->getModel()->order_date,
                    'eventsite_currency' => $EBOrder->getModel()->eventsite_currency,
                    'billing_quantity' => $EBOrder->getModel()->billing_quantity,
                    'order_type' => $EBOrder->getModel()->order_type,
                    'is_free' => $EBOrder->getModel()->is_free,
                    'is_waitinglist' => $EBOrder->getModel()->is_waitinglist,
                    'id' => $EBOrder->getModel()->id,
                    'invoice_reference_no' => $EBOrder->getModel()->invoice_reference_no,
                    'attendee_id' => $EBOrder->getModel()->attendee_id,
                    'is_payment_received' => $EBOrder->getModel()->is_payment_received,
                    'payment_received_date' => $EBOrder->getModel()->payment_received_date,
                    'comments' => $EBOrder->getModel()->comments,
                    'status' => $EBOrder->getModel()->status,
                    'registration_type' => $EBOrder->getModel()->registration_type,
                    'registration_type_id' => $EBOrder->getModel()->registration_type_id,
                ),
                "paymentUrl" => ($setting->payment_type == 1 ? config('app.eventcenter_url') . '/event/'.$request->event['url'].'/detail/complete-api-order/'.$EBOrder->getModel()->id : false)
            ),
        ], $this->successStatus);
    }
    
    /**
     * makeRequestData
     *
     * @param  mixed $input
     * @param  mixed $request
     * @return void
     */
    public function makeRequestData($input, $request)
    {
        //payment setting
        $payment_setting = request()->event['payment_setting'];

        $sections = EventSiteSettingRepository::getAllSections(["event_id" => $request->event_id, "language_id" => $request->language_id, "status" => 1]);

        //skip extra request data for main attendee
        $input['mainAttendee'] = $this->skipExtraRequestData($sections, $input['mainAttendee'], $request);

        //skip extra request data for additional attendee
        foreach($input['additional_attendees'] as $key => $additional_attendee) {
            $input['additional_attendees'][$key] = $this->skipExtraRequestData($sections, $additional_attendee, $request);
        }
        
        //skip extra request data for payment information
        $input['payment_info'] = $this->skipExtraRequestData($sections, $input['payment_info'], $request);

        //set hotel data
        if(isset($input['hotel']['selected_hotel_rooms'])) {
            foreach($input['hotel']['selected_hotel_rooms'] as $key=> $hotel) {
                $hotelData = $this->hotelRepository->searchHotels(["event_id" => request()->event_id, "language_id" => request()->language_id, "checkin" => $hotel['checkin'], "checkout" => $hotel['checkout'], "room" => $hotel['rooms'], "hotel_ids" => [$hotel['id']]]);
                if(count($hotelData) > 0) {
                    $input['hotel']['selected_hotel_rooms'][$key]['reserved_dates'] = $hotelData[0]['available_dates'];
                    if(count($input['hotel']['selected_hotel_rooms'][$key]['reserved_dates']) > 0) {
                        foreach($input['hotel']['selected_hotel_rooms'][$key]['reserved_dates'] as $i => $room) {
                            $input['hotel']['selected_hotel_rooms'][$key]['reserved_dates'][$i]['date_reserved'] = $room['available_date'];
                            $input['hotel']['selected_hotel_rooms'][$key]['reserved_dates'][$i]['room_id'] = $room['available_hotel_id'];

                            unset($input['hotel']['selected_hotel_rooms'][$key]['reserved_dates'][$i]['available_hotel_id']);
                            unset($input['hotel']['selected_hotel_rooms'][$key]['reserved_dates'][$i]['available_date']);
                            unset($input['hotel']['selected_hotel_rooms'][$key]['reserved_dates'][$i]['rooms']);
                        }
                    }
                }
            }
        }

        //payment info
        if(isset($input['payment_info'])) {
            $input['mainAttendee'] = array_merge($input['mainAttendee'], $input['payment_info']);
        }

        //Voucher
        if(isset($input['voucher_code']) && $input['voucher_code'] && $payment_setting['is_voucher']) {
            $voucherData = EventsiteBillingVoucherRepository::getVoucherByCode(["event_id" => request()->event_id, "voucher_code" => $input['voucher_code']]);
            $input['coupon'] = $voucherData->toArray();
        }

        //Waitinglist order
        $waitingListSetting = request()->event['waiting_list_setting'];

        if($waitingListSetting && $waitingListSetting->status == 1) {
            $input['is_waiting'] = 1;
        }

        return $input;
    }
    
    /**
     * skipExtraRequestData
     *
     * @param  mixed $sections
     * @param  mixed $input
     * @param  mixed $request
     * @return void
     */
    public function skipExtraRequestData($sections, $input, $request)
    {
        foreach ((array) $sections as $section) {
            foreach ($section['fields'] as $field) {
                if ($field['status'] == 0 && isset($input[$field['field_alias']])) {
                    unset($input[$field['field_alias']]);
                }
            }
        }

        //Phone
        if(isset($input['phone']) && $input['phone'] && isset($input['calling_code_phone']) && $input['calling_code_phone']) {
            $input['phone'] = $input['calling_code_phone'].'-'.$input['phone'];
        }

        return $input;
    }
    
    /**
     * cancelOrder
     *
     * @param  mixed $request
     * @param  mixed $order_id
     * @return void
     */
    public function cancelOrder(Request $request, $order_id)
    {
        $request->merge(['order_id' =>  $order_id, 'cancelOption' => 'whole_order']); 

        $data = \App\Eventbuizz\Repositories\EventsiteBillingOrderRepository::cancelOrder($request->all());

        return json_encode(['success' => $data['success']]);
    }

}
