<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;
use App\Models\EventHotel;

class HotelRepository extends AbstractRepository
{
    protected $infoFields = array('description');

    public function __construct(Request $request, EventHotel $model)
    {
        $this->formInput = $request;
        $this->model = $model;
    }

    /**
     *Billing hotels clone/default
     *
     * @param array
     */
    public function install($request)
    {
        if ($request["content"]) {
            //Billing hotels
            $from_event_billing_hotels = \App\Models\EventHotel::where("event_id", $request['from_event_id'])->get();

            if ($from_event_billing_hotels) {

                foreach ($from_event_billing_hotels as $from_event_billing_hotel) {

                    $to_event_billing_hotel = $from_event_billing_hotel->replicate();

                    $to_event_billing_hotel->event_id = $request['to_event_id'];

                    if (session()->has('clone.event.event_registration_form.' . $from_event_billing_hotel->registration_form_id) && $from_event_billing_hotel->registration_form_id > 0) {
                        $to_event_billing_hotel->registration_form_id = session()->get('clone.event.event_registration_form.' . $from_event_billing_hotel->registration_form_id);
                    }

                    $to_event_billing_hotel->save();

                    //hotel info 
                    $from_event_billing_hotel_info = \App\Models\EventHotelInfo::where("hotel_id", $from_event_billing_hotel->id)->get();
                    
                    foreach ($from_event_billing_hotel_info as $from_info) {

                        $info = $from_info->replicate();

                        $info->hotel_id = $to_event_billing_hotel->id;

                        $info->languages_id = $request["languages"][0];

                        $info->save();

                    }

                    //hotel rooms
                    $from_hotel_rooms = \App\Models\EventHotelRoom::where("hotel_id", $from_event_billing_hotel->id)->get();

                    if ($from_hotel_rooms) {

                        foreach ($from_hotel_rooms as $from_hotel_room) {

                            $to_hotel_room = $from_hotel_room->replicate();

                            $to_hotel_room->hotel_id = $to_event_billing_hotel->id;

                            $to_hotel_room->save();

                        }
                    }
                }
            }
        }
    }

    public function listing($formInput)
    {
        $currencies = getCurrencyArray();

        $currency = \App\Models\EventsitePaymentSetting::where('event_id', $formInput['event_id'])
        ->where('registration_form_id', isset($formInput['registration_form_id']) ? $formInput['registration_form_id'] : 0)
        ->value('eventsite_currency');
        $currency = (isset($currencies[$currency]) && $currencies[$currency] ? $currencies[$currency] : 'USD');

        $query = \App\Models\EventHotel::where('event_id', '=', $formInput['event_id'])
            ->where('is_archive', 0)
            ->with(['info' => function ($query) use ($formInput) {
                $query->where('languages_id', '=', $formInput['language_id']);
            }]);
        $query->where('registration_form_id', isset($formInput['registration_form_id']) ? $formInput['registration_form_id'] : 0);
        if(isset($formInput['status'])) {
            $query->where('status', $formInput['status']);
        }
        
        $result = $query->orderBy('sort_order', 'asc')->get()->toArray();
        
        foreach ($result as $key => $row) {
            $rowData = array();
            $infoData = readArrayKey($row, $rowData, 'info');
            $rowData['id'] = $row['id'];
            $rowData['name'] = $row['name'];
            $rowData['status'] = $row['status'];
            $rowData['price'] = $row['price'];
            $rowData['currency'] = $currency;
            $rowData['from_date'] = \Carbon\Carbon::parse($row['hotel_from_date'])->format('m/d/Y');
            $rowData['to_date'] = \Carbon\Carbon::parse($row['hotel_to_date'])->format('m/d/Y');
            $rowData['description'] = isset($infoData['description']) ? $infoData['description'] : '';
            $hotelRooms = [];
            $rooms =  \App\Models\EventHotelRoom::where('hotel_id', $row['id'])->get();
            foreach ($rooms as $r) {
                $hotelRooms[] = ['no_of_rooms' => $r->total_rooms, 'room_date' => \Carbon\Carbon::parse($r->available_date)->format('d/m/Y')];
            }
            $rowData['room_range'] = $hotelRooms;
            $result[$key] = $rowData;
        }
        return $result;
    }

    public function setCustomFormInput($formInput)
    {
        $formInput['status'] = 1;

        $formInput['from_date'] = \Carbon\Carbon::parse($formInput['from_date'])->toDateString();

        $formInput['to_date'] = \Carbon\Carbon::parse($formInput['to_date'])->toDateString();

        $this->setFormInput($formInput);

        return $this;
    }

    public function createHotel($formInput)
    {
        $formInput = get_trim_all_data($formInput);

        $this->setCustomFormInput($formInput)
            ->create()
            ->insertInfoData()
            ->insertRoomsData();
    }

    public function insertInfoData()
    {
        $formInput = $this->getFormInput();

        $languages = get_event_languages($formInput['event_id']);

        $info = array();

        foreach ($languages as $key) {
            foreach ($this->infoFields as $field) {
                $info[] = new \App\Models\EventHotelInfo(array('name' => $field, 'value' => trim($formInput[$field]), 'languages_id' => $key, 'status' => 1));
            }
        }

        $object = $this->getObject();

        $object->info()->saveMany($info);

        return $this;
    }

    public function insertRoomsData()
    {
        $formInput = $this->getFormInput();
        $roomRange = json_decode($formInput['room_range'], true);
        $rooms_array = [];
        foreach ($roomRange as $index => $room) {
            $rooms_array[] = new \App\Models\EventHotelRoom(
                array(
                    'available_date' => \Carbon\Carbon::parse($room['room_date'])->toDateString(),
                    'total_rooms' => $room['no_of_rooms']
                )
            );
        }

        $object = $this->getObject();
        $object->room()->saveMany($rooms_array);
        return $this;
    }

    public function updateHotel($formInput, $object)
    {
        $formInput = get_trim_all_data($formInput);
        $this->setCustomFormInput($formInput)
            ->update($object)
            ->updateInfoData($object->id)
            ->updateRoomsData($object);
    }

    public function updateInfoData($id)
    {
        $formInput = $this->getFormInput();
        $languages = get_event_languages($formInput['event_id']);
        foreach ($languages as $key) {
            foreach ($this->infoFields as $field) {
                if (isset($formInput[$field])) {
                    $info = \App\Models\EventHotelInfo::where('hotel_id', '=', $id)->where('languages_id', '=', $key)->where('name', '=', $field)->first();
                    if (!$info) {
                        \App\Models\EventHotelInfo::create([
                            "name" => $field,
                            "hotel_id" => $id,
                            "languages_id" => $key,
                            "status" => 1,
                            "value" => trim($formInput[$field])
                        ]);
                    } else {
                        $info->value = trim($formInput[$field]);
                        $info->save();
                    }
                }
            }
        }

        return $this;
    }

    public function updateRoomsData($object)
    {
        $roomIds = $object->room()->pluck('id')->toArray();
        $formInput = $this->getFormInput();
        $roomRange = json_decode($formInput['room_range'], true);
        foreach ($roomRange as $index => $room) {
            $roomIsExist = \App\Models\EventHotelRoom::where('hotel_id', $object->id)->where('available_date', \Carbon\Carbon::parse($room['room_date'])->toDateString())->first();
            if ($roomIsExist) {
                $roomIsExist->total_rooms =  $room['no_of_rooms'];
                $roomIsExist->save();
                unset($roomIds[array_search($roomIsExist->id, $roomIds)]);
            } else {
                \App\Models\EventHotelRoom::create(array(
                    'hotel_id' => $object->id,
                    'available_date' => \Carbon\Carbon::parse($room['room_date'])->toDateString(),
                    'total_rooms' => $room['no_of_rooms']
                ));
            }
        }

        if (count($roomIds) > 0) {
            \App\Models\EventHotelRoom::whereIn('id', $roomIds)->delete();
        }

        return $this;
    }

    public function deleteHotel($id)
    {
        \App\Models\EventHotel::where('id', '=', $id)->delete();
        \App\Models\EventHotelInfo::where('hotel_id', '=', $id)->delete();
        \App\Models\EventHotelRoom::where('hotel_id', '=', $id)->delete();
    }

    public function sorting($formInput)
    {
        if (isset($formInput['items'])) {

            foreach ($formInput['items'] as $key => $item) {

                \App\Models\EventHotel::where('event_id', $formInput['event_id'])->where('id', $item['id'])->update([
                    "sort_order" => $key
                ]);

            }

        }

    }

    /**
     * hotel export setting
     */
    static public function getExportSettings()
    {
        $settings = array(
            'fields' => array(
                'name' => array(
                    'field' => 'name',
                    'label' => 'Hotel item',
                    'required' => false
                ),
                'rooms' => array(
                    'field' => 'rooms',
                    'label' => 'Rooms',
                    'required' => true
                ),
                'checkin' => array(
                    'field' => 'checkin',
                    'label' => 'Check in date',
                    'required' => true
                ),
                'checkout' => array(
                    'field' => 'checkout',
                    'label' => 'Check out date',
                    'required' => true
                ),
                'nights' => array(
                    'field' => 'nights',
                    'label' => 'Nights',
                    'required' => true
                ),
                'price' => array(
                    'field' => 'price',
                    'label' => 'Price',
                    'required' => false
                ),
                'price_type' => array(
                    'field' => 'price_type',
                    'label' => 'Price Type',
                    'required' => false
                ),
                'sub_total' => array(
                    'field' => 'sub_total',
                    'label' => 'Subtotal',
                    'required' => false
                ),
                'vat' => array(
                    'field' => 'vat',
                    'label' => 'Vat%',
                    'required' => false
                ),
                'vat_price' => array(
                    'field' => 'vat_price',
                    'label' => 'Vat Price',
                    'required' => false
                ),
                'total' => array(
                    'field' => 'total',
                    'label' => 'Total',
                    'required' => false
                ),
                'email' => array(
                    'field' => 'email',
                    'label' => 'Booked Person email',
                    'required' => false
                ),
                'first_name' => array(
                    'field' => 'first_name',
                    'label' => 'Booked person First name',
                    'required' => false
                ),
                'last_name' => array(
                    'field' => 'last_name',
                    'label' => 'Booked person Last name',
                    'required' => false
                ),
                'order_number' => array(
                    'field' => 'order_number',
                    'label' => 'order number',
                    'required' => false
                )

            )
        );

        return $settings;
    }

    /**
     * export hotel data
     * @param array
     *
     */
    public function export($formInput)
    {
        $payment_setting = \App\Models\EventsitePaymentSetting::where('event_id', $formInput['event_id'])->where('registration_form_id', (int)$formInput['registration_form_id'])->first();

        $active_order_ids = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->where('is_archive', '0')->currentOrder()->pluck('id');

        $result = \App\Models\EventOrderHotel::join("conf_billing_orders", "conf_billing_orders.id", "=", "conf_event_order_hotels.order_id")->whereIn("conf_event_order_hotels.order_id", $active_order_ids)->where("conf_event_order_hotels.registration_form_id", (int)$formInput['registration_form_id'])->select("conf_event_order_hotels.*", "conf_billing_orders.order_number", "conf_billing_orders.vat as order_vat", "conf_billing_orders.is_free")->get();
        
        $hotels = array();
        
        foreach ($result as $i => $row) {

            $nights = days($row->checkin, $row->checkout);

            $vat = ($payment_setting->eventsite_apply_multi_vat == 1 || $row->is_free == 1 ? $row->vat : $row->order_vat);

            $attendees = \App\Models\EventHotelPerson::leftJoin("conf_attendees", "conf_attendees.id", "=", "conf_event_hotels_persons.attendee_id")->where("conf_event_hotels_persons.order_hotel_id", $row->id)->where("conf_event_hotels_persons.order_id", $row->order_id)->select("conf_attendees.*")->get();
            
            if (count($attendees) > 0) {

                foreach ($attendees as $attendee) {

                    $room = 1;

                    $sub_total = ($row->price_type == "fixed" ? ($row->price * $room) : ($nights * $row->price * $room));

                    $vat_price = ($vat / 100) * $sub_total;

                    $total = ($sub_total + $vat_price);

                    $hotels[] = array('name' => $row->name, 'rooms' => $room, 'checkin' => $row->checkin, 'checkout' => $row->checkout, 'nights' => $nights, 'price' => $row->price, 'price_type' => $row->price_type, 'sub_total' => $sub_total, 'vat' => $vat, 'vat_price' => $vat_price, 'total' => $total, 'email' => ($attendee ? $attendee->email : ""), 'first_name' => ($attendee ? $attendee->first_name : ""), 'last_name' => ($attendee ? $attendee->last_name : ""), 'order_number' => $row->order_number);
                
                }

            }

        }

        return $hotels;
    }

    /**
     * Hotel bookings
     * @param array
     *
     */
    public function bookings($formInput)
    {
        $payment_setting = \App\Models\EventsitePaymentSetting::where('event_id', $formInput['event_id'])->where('registration_form_id', (int)$formInput['registration_form_id'])->first();

        $active_order_ids = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->where('is_archive', '0')->currentOrder()->pluck('id');

        $query = \App\Models\EventOrderHotel::join("conf_billing_orders", "conf_billing_orders.id", "=", "conf_event_order_hotels.order_id")->whereIn("conf_event_order_hotels.order_id", $active_order_ids)->where("conf_event_order_hotels.registration_form_id", (int)$formInput['registration_form_id'])->select("conf_event_order_hotels.*", "conf_billing_orders.order_number", "conf_billing_orders.vat as order_vat", "conf_billing_orders.is_free");

        if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && in_array($formInput['sort_by'], ["name", "rooms", "checkin", "checkout", "order_id"]))) {
            $query->orderBy('conf_event_order_hotels.' . $formInput['sort_by'], $formInput['order_by']);
        }

        //search
        if (isset($formInput['query']) && $formInput['query']) {
            $query->where('conf_event_order_hotels.name', 'LIKE', '%' . trim($formInput['query']) . '%');
        }

        $hotels = $query->paginate($formInput['limit'])->toArray();

        foreach ($hotels['data'] as $i => $row) {

            $nights = days($row['checkin'], $row['checkout']);

            $vat = ($payment_setting->eventsite_apply_multi_vat == 1 || $row['is_free'] == 1 ? $row['vat'] : $row['order_vat']);

            $attendees = \App\Models\EventHotelPerson::leftJoin("conf_attendees", "conf_attendees.id", "=", "conf_event_hotels_persons.attendee_id")->where("conf_event_hotels_persons.order_hotel_id", $row['id'])->where("conf_event_hotels_persons.order_id", $row['order_id'])->select("conf_attendees.*")->get();

            if (count($attendees) > 0) {

                foreach ($attendees as $attendee) {

                    $room = 1;

                    $sub_total = ($row['price_type'] == "fixed" ? ($row['price'] * $room) : ($nights * $row['price'] * $room));

                    $vat_price = ($vat / 100) * $sub_total;

                    $total = ($sub_total + $vat_price);

                    $hotels['data']['hotels'][] = array('name' => $row['name'], 'rooms' => $room, 'checkin' => $row['checkin'], 'checkout' => $row['checkout'], 'nights' => $nights, 'price' => $row['price'], 'price_type' => $row['price_type'], 'sub_total' => $sub_total, 'vat' => $vat, 'vat_price' => $vat_price, 'total' => $total, 'email' => ($attendee ? $attendee->email : ""), 'first_name' => ($attendee ? $attendee->first_name : ""), 'last_name' => ($attendee ? $attendee->last_name : ""), 'order_number' => $row['order_number']);
               
                }
            }

        }

        if (isset($hotels['data']['hotels'])) $hotels['data'] = $hotels['data']['hotels'];

        return $hotels;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function hotelDescription($formInput) {
        return \App\Models\EventSiteDescription::where('event_id', $formInput['event_id'])->with(['info'=>function($query) use($formInput) {
            return $query->where('languages_id', $formInput['language_id']);
        }])->first();
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function hotelMinDate($formInput) {
        $hotel = \App\Models\EventHotel::where('event_id', $formInput['event_id'])->where('status', 1)->where('is_archive','=',0)->where('registration_form_id','=',$formInput['registration_form_id'])->orderBy('hotel_from_date', 'asc')->first();
        return $hotel ? (\Carbon\Carbon::now()->gt($hotel->hotel_from_date) ? \Carbon\Carbon::now()->toDateString() : $hotel->hotel_from_date) : \Carbon\Carbon::now()->toDateString();
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function hotelMaxDate($formInput) {
        $hotel = \App\Models\EventHotel::where('event_id', $formInput['event_id'])->where('status', 1)->where('is_archive','=',0)->where('registration_form_id','=',$formInput['registration_form_id'])->orderBy('hotel_to_date', 'desc')->first();
        return $hotel ? (\Carbon\Carbon::now()->gt($hotel->hotel_to_date) ? \Carbon\Carbon::now()->toDateString() : $hotel->hotel_to_date) : \Carbon\Carbon::now()->toDateString();
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function searchHotels($formInput, $count = false) {
        
        $currencies = getCurrencyArray();
        $currency = $formInput['event']['payment_setting']['eventsite_currency'];
        $from_date = \Carbon\Carbon::parse($formInput['checkin'])->format('Y-m-d');
        $to_date = \Carbon\Carbon::parse($formInput['checkout'])->format('Y-m-d');
        $checkin = strtotime($from_date);
        $checkout = strtotime($to_date);
        $date_diff = $checkout - $checkin;
        $days = floor($date_diff / (60 * 60 * 24));
        if($days > 0)
        {
            $to_date = date('Y-m-d', strtotime('-1 day', $checkout));
        }
        $period =  date_range($from_date, $to_date, 'Y-m-d');
        $days = count($period);
        $rooms = $formInput['room'];

        $query = \App\Models\EventHotel::where('conf_event_hotels.status', 1)->where('conf_event_hotels.event_id', $formInput['event_id'])
            ->leftJoin('conf_event_hotels_rooms', 'conf_event_hotels.id', '=', 'conf_event_hotels_rooms.hotel_id')
            ->where('conf_event_hotels_rooms.total_rooms', '>=', $rooms)
            ->whereBetween('conf_event_hotels_rooms.available_date', [$from_date, $to_date])
            ->whereDate('conf_event_hotels_rooms.available_date', '>=', \Carbon\Carbon::now()->format('Y-m-d'))
            ->whereNull('conf_event_hotels_rooms.deleted_at')
            ->with(['info' => function ($query) use($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }]);
        
        //Fetch specific hotels
        if(isset($formInput['hotel_ids'])) {
            $query->whereIn('conf_event_hotels.id', $formInput['hotel_ids']);
        }

        $query->where('conf_event_hotels.registration_form_id', isset($formInput['registration_form_id']) ? $formInput['registration_form_id'] : 0);

        $query->orderBy('conf_event_hotels_rooms.available_date', 'asc')
            ->orderBy('conf_event_hotels.sort_order', 'asc')
            ->orderBy('conf_event_hotels.id', 'asc')
            ->select('conf_event_hotels.id', 'conf_event_hotels.name', 'conf_event_hotels.price_type','conf_event_hotels.price','conf_event_hotels.url', 'conf_event_hotels.vat', 'conf_event_hotels_rooms.total_rooms', 'conf_event_hotels_rooms.available_date','conf_event_hotels_rooms.id as available_hotel_id');

        $results = $query->get()->toArray();

        if($count) {
            return count($results);
        }

        $results = returnArrayKeys($results, ['info']);
        $unique_ids = array_unique(array_column($results, 'id'));
        $ids = array_replace(array_flip($unique_ids), array_fill_keys($unique_ids, 0));

        $available_dates_with_hotel = [];
        foreach ($period as $date) {
            $available_dates_with_hotel[$date] = $ids;
        }
        $hotels = array();
        $required_rooms = $rooms;
        $active_order_ids = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->where('is_archive', '0')->currentOrder()->pluck('id');

        foreach($results as $index => $hotel) {
            $total_rooms_acquired = (int) $this->getRoomsAcquiredByRoomId($hotel['id'], $hotel['available_hotel_id'], $active_order_ids);
            $remaining_rooms = $hotel['total_rooms'] - $total_rooms_acquired;
            if($remaining_rooms >= $required_rooms)
            {
                $available_dates_with_hotel[$hotel['available_date']][$hotel['id']] = 1;
            }
        }

        $hotels_with_dates = [];
        
        foreach ($unique_ids as $i => $id)
        {
            $flag = true;
            foreach ($period as $date)
            {
                if($available_dates_with_hotel[$date][$id] == 0) {
                    $flag = false;
                }  
            }
            if($flag)
            {
                $keys = array_keys(array_column($results, 'id'), $id);
                $hotel_with_ids = [];
                foreach ($keys as $k)
                {
                    $hotel_with_ids[] = [
                        'available_date' => $results[$k]['available_date'],
                        'available_hotel_id' => $results[$k]['available_hotel_id'],
                        'rooms' => $rooms,
                    ];
                }
                $hotels_with_dates[$id] = $hotel_with_ids;
                $key = array_search($id, array_column($results, 'id'));
                $results[$key]['priceDisplay'] = getCurrency($results[$key]['price'], $currencies[$currency]) . ' ' . $currencies[$currency];
                $hotels[] = array_merge($results[$key], ['available_dates' => $hotel_with_ids]) ;
            }
        }
       
        $payment_settings = EventSiteSettingRepository::getPaymentSetting($formInput);
        
        foreach ($hotels as $index => $hotel)
        {
            unset($hotels[$index]['available_date']);
            unset($hotels[$index]['available_hotel_id']);
            $hotel_info = \App\Models\EventHotelInfo::where('hotel_id', '=', $hotel['id'])->where('languages_id', $formInput['language_id'])->get()->toArray();
            $total_price = 0;
            if($payment_settings['show_hotel_prices'] == 1) {
                if($hotel['price_type'] == 'fixed')
                {
                    $total_price = $hotel['price'] * $rooms;
                } else {
                    $total_price = $hotel['price'] * $rooms * $days;
                }
            }
            $hotels[$index]['url'] = $hotel['url'];
            $hotels[$index]['checked'] = 0;
            $hotels[$index]['hotel_person_id'] = [];
            $hotels[$index]['rooms'] = $rooms;
            $hotels[$index]['nights'] = $days;
            $hotels[$index]['checkin'] = \Carbon\Carbon::parse($formInput['checkin'])->format('Y-m-d');
            $hotels[$index]['checkout'] = \Carbon\Carbon::parse($formInput['checkout'])->format('Y-m-d');
            $hotels[$index]['description'] = $hotel_info[0]['value'];
            $hotels[$index]['total_price'] = $total_price;
            $hotels[$index]['total_price_display'] = getCurrency($total_price, $currencies[$currency]) . ' ' . $currencies[$currency];
        }

        return $hotels;
    }

    /**
     * @param mixed $hotel_id
     * @param mixed $room_id
     * @param array $active_order_ids
     * @param null $order_id
     * 
     * @return [type]
     */
    public function getRoomsAcquiredByRoomId($hotel_id, $room_id, $active_order_ids = [], $order_id = null)
    {
        if(!is_null($order_id)) {
            $rooms_acquired = \App\Models\EventOrderHotelRoom::whereIn('order_id', $active_order_ids)->where('hotel_id','=',$hotel_id)->where('order_id','<>',$order_id)->where('room_id','=',$room_id)->sum('rooms');
        } else {
            $rooms_acquired = \App\Models\EventOrderHotelRoom::whereIn('order_id', $active_order_ids)->where('hotel_id','=',$hotel_id)->where('room_id','=',$room_id)->sum('rooms');
        }
        
        return $rooms_acquired;
    }
}
