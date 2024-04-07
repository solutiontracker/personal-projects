<?php
namespace App\Eventbuizz\EBObject;

class EBOrderHotel
{
    // \App\Models\EventOrderHotel
    protected $_model;
    // \App\Models\EventHotel
    protected $_hotel_object;
    // EBOrder
    protected $_order;
    // Create a separate hotel persons class and attach here
    protected $_hotel_persons;
    protected $_rooms = 0;
    protected $_price_type;
    protected $_price_per_day = 0;
    protected $_vat;
    protected $_check_in;
    protected $_check_out;
    // Hotel sub total without vat
    protected $_hotel_subtotal = 0;
    // Create a separate hotel rooms class and attach here
    protected $_hotel_rooms;

    public function updateOrderObjectReference(EBOrder $order)
    {
        $this->_order = $order;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function __construct(EBOrder $order, $data = null)
    {
        $this->_order = $order;

        if ($order->isOrderPlaced()) {
            $this->_model = $data;
            $this->_hotel_object = \App\Models\EventHotel::find($this->_model->hotel_id);
            $this->_hotel_persons = $this->_model->hotel_persons()->get();
            $this->_hotel_rooms = $this->_model->room->toArray();
            $this->_check_in = \Carbon\Carbon::parse($this->_model->checkin)->format('Y-m-d');
            $this->_check_out = \Carbon\Carbon::parse($this->_model->checkout)->format('Y-m-d');
            $this->_check_out = $this->_check_in == $this->_check_out ? \Carbon\Carbon::parse($this->_check_out)->addDay() : $this->_check_out;
            $this->_rooms = $this->_model->rooms;
            $this->_price_per_day = $this->_model->price;
            $this->_vat = $this->_model->vat;
            $this->_price_type = $this->_model->price_type;
            $this->_hotel_subtotal = $this->_calcSubTotal();

            //Registration form (Attendee type)
            $this->registration_form_id = $this->_model->registration_form_id;
            $this->attendee_id = $this->_model->attendee_id;

        } else if ($order->isEdit()) {
            $checkin = \Carbon\Carbon::parse($data['checkin'])->format('Y-m-d');
            $checkout = \Carbon\Carbon::parse($data['checkout'])->format('Y-m-d');
            $rooms = $data['rooms'];
            $this->_model = new \App\Models\EventOrderHotel();
            $this->_hotel_object = \App\Models\EventHotel::findOrFail($data['id']);
            $this->_hotel_persons = $data['hotel_person_id'];
            $this->_hotel_rooms = $this->_setHotelRooms($data);
            $this->_check_in = $checkin;
            $this->_check_out = $checkout;
            $this->_check_out = $this->_check_in == $this->_check_out ? \Carbon\Carbon::parse($this->_check_out)->addDay() : $this->_check_out;
            $this->_rooms = $rooms;
            
            $this->_vat = $this->_hotel_object->vat;

            //Only add prices if enable price is on
            if($this->_order->getPaymentSettingAttribute('show_hotel_prices') == 1) 
            {
                $this->_price_per_day = $this->_hotel_object->price;
                $this->_price_type = $this->_hotel_object->price_type;
                $this->_hotel_subtotal = $this->_calcSubTotal();
            }

            //Registration form (Attendee type)
            $this->registration_form_id = $data['registration_form_id'];
            $this->attendee_id = $data['attendee_id'];

        } else {
            $this->_model = new \App\Models\EventOrderHotel();
            $this->_hotel_object = \App\Models\EventHotel::findOrFail($data['id']);
            $this->_hotel_persons = explode(',', $data['room_persons']);
            $this->_hotel_rooms = $data['reserved_dates'];
            $this->_check_in = \Carbon\Carbon::parse($data['checkin'])->format('Y-m-d');
            $this->_check_out = \Carbon\Carbon::parse($data['checkout'])->format('Y-m-d');
            $this->_check_out = $this->_check_in == $this->_check_out ? \Carbon\Carbon::parse($this->_check_out)->addDay() : $this->_check_out;
            $this->_rooms = $data['rooms'];
            
            $this->_vat = $this->_hotel_object->vat;

            //Only add prices if enable price is on
            if($this->_order->getPaymentSettingAttribute('show_hotel_prices') == 1) {
                $this->_price_per_day = $this->_hotel_object->price;
                $this->_price_type = $this->_hotel_object->price_type;
                $this->_hotel_subtotal = $this->_calcSubTotal();
            }

            //Registration form (Attendee type)
            $this->registration_form_id = $data['registration_form_id'];
            $this->attendee_id = $data['attendee_id'];
        }
    }

    private function _setHotelRooms($data)
    {
        $rooms = [];
        
        if ($data['link_type'] == 'new') {
            $available_dates = $data['available_dates'];
            if (count($available_dates) > 0) {
                foreach ($available_dates as $room) {
                    $rooms[] = ['date_reserved' => $room['available_date'], 'room_id' => $room['available_hotel_id']];
                }
            }

        } elseif ($data['link_type'] == 'old') {
            $room_reverse_dates = json_decode($data['room_reverse_dates'], true);
            if (count($room_reverse_dates) > 0) {
                foreach ($room_reverse_dates as $room) {
                    $rooms[] = ['date_reserved' => $room['reserve_date'], 'room_id' => $room['room_id']];
                }
            }
        }

        return $rooms;
    }

    private function _isVatApplied()
    {
        return ($this->_order->getHotelVAT() instanceof EBOrderVAT);
    }

    public function getApplicableVATRate()
    {
        if ($this->_isVATApplied()) {
            if ($this->_order->isFree()) {
                return $this->_order->getHotelVAT()->getApplicableVATRate();
            } else {
                return $this->getVAT();
            }
        }
        return 0;
    }

    public function getHotelSubtotal()
    {
        return $this->_hotel_subtotal;
    }

    public function getHotelVATAmount()
    {
        if ($this->_isVatApplied()) {
            if ($this->_order->isFree()) {
                return $this->_order->getHotelVAT()->getVatAmount();
            } else {
                return ($this->getHotelSubtotal() * $this->getVAT()) / 100;
            }
        }
        return 0;
    }

    public function getHotelGrandTotal()
    {
        return $this->getHotelSubtotal() + $this->getHotelVATAmount();
    }

    public function getPersons()
    {
        return $this->_hotel_persons;
    }

    public function updateRoomPersons($_hotel_persons) {
        $this->_hotel_persons = $_hotel_persons;
    }
    
    public function save()
    {
        $this->_model->hotel_id = $this->_hotel_object->id;
        $this->_model->order_id = $this->_order->getModelAttribute('id');
        $this->_model->name = $this->_hotel_object->name;
        $this->_model->price = round($this->_price_per_day, 2); // $this->_hotel_subtotal;
        $this->_model->price_type = $this->_hotel_object->price_type;
        $this->_model->vat = $this->getApplicableVATRate();
        $this->_model->vat_price = round($this->getHotelVATAmount(), 2);
        $this->_model->rooms = $this->_rooms;
        $this->_model->checkin = $this->_check_in;
        $this->_model->checkout = $this->_check_out;
        $this->_model->registration_form_id = $this->registration_form_id;
        $this->_model->attendee_id = $this->attendee_id;
        $this->_model->save();

        if (count($this->_hotel_persons) > 0) {
            foreach ($this->_hotel_persons as $index => $person) {
                if($person instanceof \App\Models\EventHotelPerson) {
                    $hotel_person = \App\Models\EventHotelPerson::where('id', $person->id)->first();
                    if(!$hotel_person) {
                        $hotel_person = new \App\Models\EventHotelPerson(); 
                    }
                } else {
                    $hotel_person = new \App\Models\EventHotelPerson();
                }
                
                $hotel_person->room_no = ++$index;
                if ($this->_order->isEdit()) {
                    if (isset($person['attendee_id'])) {
                        $hotel_person->attendee_id = $person['attendee_id'];
                    } else {
                        $hotel_person->attendee_id = $person;
                    }
                } else {
                    if ($person == 'main') {
                        $hotel_person->attendee_id = $this->_order->getMainAttendee()->getModel()->id;
                    } else if (validateEmail($person)) {
                        $attendee_id = \App\Models\Attendee::join('conf_billing_order_attendees', function ($join) {
                            $join->on('conf_billing_order_attendees.attendee_id', '=', 'conf_attendees.id');
                        })->where('conf_attendees.email', $person)->where('conf_attendees.organizer_id', $this->_order->getUtility()->getOrganizerID())->value('conf_attendees.id');
                        $hotel_person->attendee_id = ($attendee_id ? $attendee_id : $this->getAttendeeIdByIndex($person));
                    } elseif ($person != '') {
                        $hotel_person->attendee_id = $this->getAttendeeIdByIndex($person);
                    } else {
                        $hotel_person->attendee_id = 0;
                    }
                }

                $hotel_person->hotel_id = $this->_model->hotel_id;
                $hotel_person->order_hotel_id = $this->_model->id;
                $hotel_person->order_id = $this->_order->getModelAttribute('id');
                $hotel_person->save();
            }
        }

        if (count($this->_hotel_rooms) > 0) {
            foreach ($this->_hotel_rooms as $room) {
                if(isset($room['id'])) {
                    $hotel_room = \App\Models\EventOrderHotelRoom::where('id', $room['id'])->first();
                    if(!$hotel_room) {
                        $hotel_room = new \App\Models\EventOrderHotelRoom();
                    }
                } else {
                    $hotel_room = new \App\Models\EventOrderHotelRoom();
                }
                $hotel_room->order_hotel_id = $this->_model->id;
                $hotel_room->room_id = $room['room_id'];
                $hotel_room->hotel_id = $this->_model->hotel_id;
                $hotel_room->order_id = $this->_order->getModelAttribute('id');
                $hotel_room->event_id = $this->_order->getOrderEventId();
                $hotel_room->reserve_date = \Carbon\Carbon::parse($room['date_reserved'])->format('Y-m-d');
                $hotel_room->rooms = $this->_model->rooms;
                $hotel_room->save();
            }
        }
    }

    public function replicateModel()
    {
        $this->_model = $this->_model->replicate();
    }

    public function getCheckin()
    {
        return $this->_check_in;
    }

    public function getCheckout()
    {
        return $this->_check_out;
    }

    public function getRooms()
    {
        return $this->_rooms;
    }

    public function updateRooms($rooms)
    {
        $this->_rooms = $rooms;
    }

    private function _calcSubTotal()
    {
        //No need to account for number of days between checkin and checkout
        if ($this->_price_type == 'fixed') {
            return $this->_price_per_day * $this->_rooms;
        } else {
            // Account for number of days between checkin and checkout
            $subTotal = $this->_price_per_day * $this->_rooms;
            
            $num_of_days = \Carbon\Carbon::parse($this->_check_in)->diffInDays(\Carbon\Carbon::parse($this->_check_out));

            if ($num_of_days < 2) // 0 or 1
            {
                $num_of_days = 1;
            }

            return ($num_of_days > 0) ? $subTotal * $num_of_days : $subTotal;
        }
    }

    public function getHotelObject()
    {
        return $this->_hotel_object;
    }

    public function getHotelRooms()
    {
        return $this->_hotel_rooms;
    }
    
    public function getAttendeeIdByIndex($index)
    {
        // Only to find "id" for additional Attendee
        $attendee_id = 0;
        $attendees = $this->_order->getAllAttendees();
        if (isset($attendees[$index])) {
            $attendee = $attendees[$index];
            if ($attendee instanceof EBOrderAttendee) {
                $attendee_id = $attendee->getModel()->id;
            }
        }
        return $attendee_id;
    }

    public function getVAT()
    {
        return $this->_vat;
    }
}
