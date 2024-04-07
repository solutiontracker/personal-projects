<?php

namespace App\Http\Controllers;

use App\Eventbuizz\Repositories\AttendeeRepository;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use App\Mail\Email;

class UtilitiesController extends Controller
{
    public function __construct(AttendeeRepository $attendeeRepository) {
        $this->attendeeRepository = $attendeeRepository;
    }

    public function assignAttendeesToParent(Request $request, $id)
    {
        $event = \App\Models\Event::where('id', $id)->whereNotNull('parent_event_id')->whereNotNull('parent_event_attendee_type')->first();
        if($event) {
            $attendees = \App\Models\EventAttendee::where('event_id', $event->id)->get();
            foreach($attendees as $attendee) {
                $parent_attendee = \App\Models\EventAttendee::where('attendee_id', $attendee->attendee_id)->where('event_id', $event->parent_event_id)->first();
                if($parent_attendee) {
                    $parent_attendee->attendee_type = $event->parent_event_attendee_type;
                    $parent_attendee->save();
                }
            }
        }
    }

    public function updateActiveOrdersData(Request $request, $event_id)
    {
        //Active order ids
        $order_ids = \App\Models\BillingOrder::where('event_id', $event_id)->where('is_archive', 0)->currentOrder()->pluck('id');

        echo implode(',', $order_ids->toArray());
    }

    public function assignedHotelRooms(Request $request, $event_id)
    {
        $assigned_hotels = \DB::select('SELECT * FROM `conf_event_internal_booking_hotel_assigned` WHERE hotel_id IN (SELECT id FROM `conf_event_internal_booking_hotels` WHERE `event_id` = '.$event_id.' AND deleted_at IS NULL ) AND deleted_at IS NULL AND id NOT IN (SELECT assign_hotel_id FROM `conf_event_internal_booking_hotel_rooms_assigned` WHERE `event_id` = '.$event_id.' AND `deleted_at` IS NULL ORDER BY `conf_event_internal_booking_hotel_rooms_assigned`.`attendee_id` ASC ) ORDER BY `conf_event_internal_booking_hotel_assigned`.`id` DESC');

        foreach($assigned_hotels as $assigned_hotel) {
            $dates = getDatesFromRange($assigned_hotel->checkin, $assigned_hotel->checkout);
            $dates = array_slice($dates,0,count($dates)-1);

            foreach($dates as $date) {
                $modified_date  = \Carbon\Carbon::parse($date)->toDateString();
                $hotel_room = \DB::table('conf_event_internal_booking_hotels_rooms')->where('hotel_id', $assigned_hotel->hotel_id)->where('available_date', $modified_date)->first();
                if($hotel_room) {
                    $record = \DB::table('conf_event_internal_booking_hotel_rooms_assigned')->where('assign_hotel_id', $assigned_hotel->id)->where('hotel_id', $assigned_hotel->hotel_id)->where('room_id', $hotel_room->id)->where('event_id', $event_id)->where('attendee_id', $assigned_hotel->attendee_id)->first();
                    if(!$record) {
                        \DB::table('conf_event_internal_booking_hotel_rooms_assigned')->insert([
                            "assign_hotel_id" => $assigned_hotel->id,
                            "hotel_id" => $assigned_hotel->hotel_id,
                            "room_id" => $hotel_room->id,
                            "event_id" => $event_id,
                            "attendee_id" => $assigned_hotel->attendee_id,
                            "rooms" => 1,
                            "reserve_date" => \Carbon\Carbon::parse($date)->toDateString()
                        ]);
                    }
                }  
            }
        }
    }

    public function updateOrderCompanyDetailInfo(Request $request)
    {
        $billing_fields = array('member_number', 'company_type', 'company_registration_number', 'ean', 'contact_person_name', 'contact_person_email', 'contact_person_mobile_number', 'company_street', 'company_house_number', 'company_post_code', 'company_city', 'company_country','poNumber');

        $orders = \App\Models\BillingOrder::where('created_at', '>=', '2021-10-15 00:00:00')->where('is_archive', '0')->where('sale_agent_id',0)->currentOrder()->get();

        foreach($orders as $order) {
            if($order->session_data) {
                $order_data = unserialize($order->session_data);
                if(isset($order_data['step_2'])) {
                    $fields = [];
                    foreach ($billing_fields as $field)
                    {
                        if(array_key_exists($field, $order_data['step_2']))
                        {
                            $fields['billing_'.$field] = $order_data['step_2'][$field];
                        }
                    }
                    if(!empty($fields)) {
                        //$billing = \App\Models\AttendeeBilling::where('order_id', $order->id)->first();
                        if($billing) {
                            /*\App\Models\AttendeeBilling::where('order_id', $order->id)->update($fields);
                            echo $order->event_id,' - ', $order->id; 
                            echo "<pre>";
                            print_r($fields);*/
                        }
                    }
                }
            } 
        }
    }


    public function agendaSubregistrationVerification(Request $request, $event_id)
    {
        $programs = \App\Models\Agenda::where('event_id', $event_id)->pluck('id')->toArray();
        /*
        $programs = \App\Models\Agenda::where('event_id', $event_id)->get();
        
        foreach($programs as $program)
        {
            $info = \App\Models\AgendaInfo::whereIn('name', ['start_time'])->where('agenda_id', $program->id)->first();
            $info->value = str_replace('.',':', $info->value);
            $info->save();
            
            $info = \App\Models\AgendaInfo::whereIn('name', ['end_time'])->where('agenda_id', $program->id)->first();
            $info->value = str_replace('.',':', $info->value);
            $info->save();
        }
        
        exit;*/
        
        $event_agenda_attendees = \App\Models\EventAgendaAttendeeAttached::whereIn('agenda_id', $programs)->get()->toArray();

        $event_agenda_subregistration_attendees = array();

        $directAtatchAttendees = array();
        $checking_duplicate_programs = array();

        foreach($event_agenda_attendees as $row) {              
            $answer = \App\Models\EventSubRegistrationAnswer::where('link_to', $row['agenda_id'])->first();
            if($answer) {
                $result = \App\Models\EventSubRegistrationResult::where('answer_id', $answer->id)->where('attendee_id', $row['attendee_id'])->first();
                if($result) {
                    array_push($event_agenda_subregistration_attendees, $row['attendee_id']);
                } else {
                    array_push($directAtatchAttendees, array(
                        'attendee_id' => $row['attendee_id'],
                        'agenda_id' => $row['agenda_id']
                    ));
                    array_push($checking_duplicate_programs, $row['agenda_id']);
                }
            } else {
                array_push($directAtatchAttendees, array(
                    'attendee_id' => $row['attendee_id'],
                    'agenda_id' => $row['agenda_id']
                ));
                array_push($checking_duplicate_programs, $row['agenda_id']);
            }
        }

        echo 'Direct attach attendees';

        foreach($directAtatchAttendees as $row) {
            \App\Models\EventAgendaAttendeeAttached::where('agenda_id', $row['agenda_id'])->where('attendee_id', $row['attendee_id'])->delete();
        }
        echo "<pre>";
        print_r($directAtatchAttendees);
        echo "<pre>";

        echo 'Event Agenda SubRegistration Attendees';
        
        echo "<pre>";
        print_r(($event_agenda_subregistration_attendees));
        echo "<pre>";
        print_r(array_unique($checking_duplicate_programs));
        echo "<pre>";
    }

    public function correctionAgendaInfoValue(Request $request)
    {
        $programs = \App\Models\Agenda::where('created_at', '>', \Carbon\Carbon::parse('2021-10-14')->format('Y-m-d H:i:s'))->with(['info'])->get()->toArray();
        $updatedPrograms = array();
        $name = 'location';
        foreach($programs as $program) {

            $info = $program['info'];

            $count = count(array_filter($info, function($row) use ($name) {
                return $row['name'] == $name;
            }));
            
            if($count > 1) {

                $filtered = \Illuminate\Support\Arr::where($info, function ($value, $key) use ($name) {
                    return $value['value'] != "" && $value['name'] == $name;
                });
                
                if(!empty($filtered)) {

                    $result = \Illuminate\Support\Arr::first($filtered);

                    \App\Models\AgendaInfo::where('name', $name)->where('agenda_id', $program['id'])->where('languages_id', $result['languages_id'])->delete();

                    \App\Models\AgendaInfo::create([
                        'name' => $result['name'],
                        'value' => $result['value'],
                        'agenda_id' => $result['agenda_id'],
                        'languages_id' => $result['languages_id'],
                        'status' => $result['status'],
                    ]);
                    
                    array_push($updatedPrograms, $result['agenda_id']);
                } else {

                    $result = \Illuminate\Support\Arr::first(\Illuminate\Support\Arr::where($info, function ($value, $key) use ($name) {
                        return $value['name'] == $name;
                    }));

                    \App\Models\AgendaInfo::where('name', $name)->where('agenda_id', $program['id'])->where('languages_id', $result['languages_id'])->delete();

                    \App\Models\AgendaInfo::create([
                        'name' => $result['name'],
                        'value' => $result['value'],
                        'agenda_id' => $result['agenda_id'],
                        'languages_id' => $result['languages_id'],
                        'status' => $result['status'],
                    ]);
                    
                    array_push($updatedPrograms, $result['agenda_id']);
                }
                
            }
        }
        print_all($updatedPrograms);
    }

    public function orderPhoneConrrections(Request $request)
    {
        $orders = \App\Models\BillingOrder::where('created_at', '>', \Carbon\Carbon::parse('2021-10-19'))->get();
        
        foreach($orders as $order) {
            
            $session_data = unserialize($order->session_data);

            if($order['attendee_id'] && $session_data['step_2']['phone']) {

                $attendee = \App\Models\Attendee::where('id', $order['attendee_id'])
                ->whereNull('deleted_at')
                ->where('phone','!=','')->where('phone', 'not like', '+%')
                ->first();
                
                if($attendee) {

                    $info = \App\Models\AttendeeInfo::where('name', 'phone')->where('attendee_id', $attendee->id)->whereNull('deleted_at')->first();
                    
                    //echo 'Inner '.$order->attendee_id.' ('.$session_data['step_2']['calling_code_phone'].'-'.$session_data['step_2']['phone'].') '.$attendee->phone.' | '.$info->value.'<br/>';
                    
                    if(Str::contains($session_data['step_2']['calling_code_phone'].'-'.$session_data['step_2']['phone'], $attendee->phone)) {
                        $attendee->phone = $session_data['step_2']['calling_code_phone'].'-'.$session_data['step_2']['phone'];
                        $attendee->save();

                        $info->value = $session_data['step_2']['calling_code_phone'].'-'.$session_data['step_2']['phone'];
                        $info->save();
                    }
                }  
            }
        }
    }

    public function assignPersonsToOrderHotelRooms(Request $request, $event_id)
    {
        //Active order ids
        $orders = \App\Models\BillingOrder::where('event_id', $event_id)->where('is_archive', 0)->currentOrder()->get();

        if(count($orders) > 0) {
            foreach($orders as $order) {
                $order_hotels = \App\Models\EventOrderHotel::where('order_id', $order->id)->get();
                if(count($order_hotels) > 0) {
                    foreach($order_hotels as $order_hotel) {
                        $order_hotel_room = \App\Models\EventOrderHotelRoom::where('order_hotel_id', $order_hotel->id)->first();
                        if($order_hotel_room) {
                            for($i = 1; $i <= $order_hotel_room->rooms; $i++) {
                                $person = \App\Models\EventHotelPerson::where('order_hotel_id', $order_hotel->id)->where('order_id', $order->id)->where('room_no', $i)->where('hotel_id', $order_hotel->hotel_id)->first();
                                if(!$person) {
                                    echo $order->id.'<br/>';
                                    \App\Models\EventHotelPerson::create([
                                        'order_hotel_id' => $order_hotel->id,
                                        'order_id' => $order->id,
                                        'room_no' => $i,
                                        'hotel_id' => $order_hotel->hotel_id,
                                        'attendee_id' => $order->attendee_id,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
        echo "done";
    }

    public function importAttendeeSpeakerFields($initial = 1, $end = 500){
        $events = \App\Models\Event::whereBetween('id',[$initial,$end])->pluck('id');
        foreach ($events as $event){
            AttendeeRepository::setAttendeeFieldSorting($event);
            AttendeeRepository::setSpeakerFieldSorting($event);
        }
    }

    public function createEventlayoutSectionsThemeModulevariations($start_event_id, $end_event_id)
    {

        $events = \App\Models\Event::where('id', ">=", $start_event_id)->where('id', "<=", $end_event_id)->whereNull("deleted_at")->get();

        foreach ($events as $key => $event) {

                // event theme module variations
                $themeModuleVariations = \App\Models\ThemeModuleVariation::where('theme_id', $event->registration_site_theme_id)->get();
                // event theme layout
                foreach ($themeModuleVariations as $key => $value) {
                    $exists = \App\Models\EventThemeModuleVariation::where('event_id', $event->id)
                                ->where('theme_id', $value->theme_id)
                                ->where('alias', $value->alias)
                                ->where('module_name', $value->module_name)
                                ->where('variation_name', $value->variation_name)
                                ->where('variation_slug', $value->variation_slug)
                                ->first();
                    if(!$exists){
                        \App\Models\EventThemeModuleVariation::create([
                            'event_id' => $event->id, 
                            'theme_id' => $value->theme_id, 
                            "alias" => $value->alias, 
                            "module_name" => $value->module_name, 
                            "variation_name" => $value->variation_name, 
                            "variation_slug" => $value->variation_slug, 
                            "text_align" => "center", 
                            "background_image" => "",
                        ]);
                    }
                }
        }

        echo "done!";

        exit;
    }

    public function createInvoicesFromExcelOrders(Request $request, $mode = "test", $order_number = 0, $order_prefix = '')
    {
        set_time_limit(0);

        $array = array(
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"","val3"=>"Fysioterapeut","val4"=>"Casper","val5"=>"Tornø","val6"=>"c.tornoe@hotmail.com","val7"=>"  ","val8"=>"Charlottehøj Fysioterapi og Træningscenter","val9"=>"","val10"=>"","val11"=>"","val12"=>"","val13"=>"DKK","val14"=>"5770","val15"=>"5770","val16"=>"0","val17"=>"5770"),
        );

        $orders = array();

        foreach($array as $key => $row) {
            $orders[$key]['order_number'] = $row['val0'];
            $orders[$key]['order_date'] = $row['val1'];
            $orders[$key]['additional_attendee'] = $row['val2'];
            $orders[$key]['title'] = $row['val3'];
            $orders[$key]['first_name'] = $row['val4'];
            $orders[$key]['last_name'] = $row['val5'];
            $orders[$key]['email'] = $row['val6'];
            $orders[$key]['billing_ean'] = $row['val7'];
            $orders[$key]['company_name'] = $row['val8'];
            $orders[$key]['billing_company_registration_number'] = $row['val9'];
            $orders[$key]['billing_company_street'] = $row['val10'];
            $orders[$key]['billing_company_post_code'] = $row['val11'];
            $orders[$key]['billing_company_city'] = $row['val12'];
            $orders[$key]['eventsite_currency'] = $row['val13'];
            $orders[$key]['sub_total'] = $row['val14'];
            $orders[$key]['sub_total_with_discount'] = $row['val15'];
            $orders[$key]['vat'] = $row['val16'];
            $orders[$key]['grand_total'] = $row['val17'];
        }

        $orders_items = array();

        $items = array(
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"Casper","val3"=>"Tornø","val4"=>"c.tornoe@hotmail.com","val5"=>"Early bird - Fysioterapeut, medlem","val6"=>"1","val7"=>"DKK","val8"=>"0.00%","val9"=>"0.00","val10"=>"3,700.00","val11"=>"3,700.00","val12"=>"2","val13"=>""),
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"Casper","val3"=>"Tornø","val4"=>"c.tornoe@hotmail.com","val5"=>"Festmiddag fredag d. 18. marts","val6"=>"1","val7"=>"DKK","val8"=>"25.00%","val9"=>"125.00","val10"=>"500.00","val11"=>"500.00","val12"=>"3","val13"=>""),
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"Casper","val3"=>"Tornø","val4"=>"c.tornoe@hotmail.com","val5"=>"Ja tak, jeg ønsker at benytte Bustransport","val6"=>"1","val7"=>"DKK","val8"=>"25.00%","val9"=>"50.00","val10"=>"200.00","val11"=>"200.00","val12"=>"4","val13"=>""),
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"Casper","val3"=>"Tornø","val4"=>"c.tornoe@hotmail.com","val5"=>"Torsdag d. 17. marts før kongressen - bus fra Odense Banegård til Odense Congress Center","val6"=>"1","val7"=>"DKK","val8"=>"25.00%","val9"=>"0.00","val10"=>"0.00","val11"=>"0.00","val12"=>"6","val13"=>""),
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"Casper","val3"=>"Tornø","val4"=>"c.tornoe@hotmail.com","val5"=>"Torsdag d. 17. marts efter Get-together - bus fra Odense Congress Center til kongreshoteller","val6"=>"1","val7"=>"DKK","val8"=>"25.00%","val9"=>"0.00","val10"=>"0.00","val11"=>"0.00","val12"=>"7","val13"=>""),
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"Casper","val3"=>"Tornø","val4"=>"c.tornoe@hotmail.com","val5"=>"Fredag d. 18. marts morgen - bus fra kongreshoteller til Odense Congress Center","val6"=>"1","val7"=>"DKK","val8"=>"25.00%","val9"=>"0.00","val10"=>"0.00","val11"=>"0.00","val12"=>"8","val13"=>""),
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"Casper","val3"=>"Tornø","val4"=>"c.tornoe@hotmail.com","val5"=>"Fredag d. 18. marts aften efter festen - bus fra Odense Congress Center til kongreshoteller","val6"=>"1","val7"=>"DKK","val8"=>"25.00%","val9"=>"0.00","val10"=>"0.00","val11"=>"0.00","val12"=>"9","val13"=>""),
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"Casper","val3"=>"Tornø","val4"=>"c.tornoe@hotmail.com","val5"=>"Lørdag d. 19. marts morgen - bus fra kongreshoteller til Odense Congress Center","val6"=>"1","val7"=>"DKK","val8"=>"25.00%","val9"=>"0.00","val10"=>"0.00","val11"=>"0.00","val12"=>"10","val13"=>""),
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"Casper","val3"=>"Tornø","val4"=>"c.tornoe@hotmail.com","val5"=>"Lørdag d. 19. marts når kongressen slutter - bus fra Odense Congress Center til Odense Banegård","val6"=>"1","val7"=>"DKK","val8"=>"25.00%","val9"=>"0.00","val10"=>"0.00","val11"=>"0.00","val12"=>"11","val13"=>""),
            array("val0"=>"1","val1"=>"2021-10-06","val2"=>"Casper","val3"=>"Tornø","val4"=>"c.tornoe@hotmail.com","val5"=>"Hotel Cabinn, Commodore enkeltværelse inkl. morgenmad","val6"=>"2","val7"=>"DKK","val8"=>"25.00%","val9"=>"342.50","val10"=>"685.00","val11"=>"1,370.00","val12"=>"12","val13"=>"")
        );
        
        foreach($items as $key => $item) {
            $orders_items[$key]['order_number'] = $item['val0'];
            $orders_items[$key]['item'] = $item['val5'];
            $orders_items[$key]['qty'] = $item['val6'];
            $orders_items[$key]['currency'] = $item['val7'];
            $orders_items[$key]['vat'] = $item['val8'];
            $orders_items[$key]['vat_amount'] = $item['val9'];
            $orders_items[$key]['unit_price'] = $item['val10'];
            $orders_items[$key]['total'] = $item['val11'];
            $orders_items[$key]['sortBy'] = $item['val12'];
        }

        $event = \App\Models\Event::where('id', 9771)->first();

        $labels = eventsite_labels(['eventsite', 'exportlabels'], ["event_id" => 9771, "language_id" => 2]);

        $templateData = $this->attendeeRepository->getInviteTemplate(['invite_type' => 'app_invite', "event_id" => 9771, "language_id" => 2]);

        $subject_template = $template = $alias = "";

		if (isset($templateData['email_template']['info'])) {
			$alias = $templateData['email_template']->alias;
			foreach ($templateData['email_template']['info'] as $row) {
				if ($row['name'] == 'template') {
					$template = $row['value'];
				}
				if ($row['name'] == 'subject') {
					$subject_template = $row['value'];
				}
			}
		}

        $event_setting  = get_event_branding(9771);

		if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
			$src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
		} else {
			$src = cdn('/_admin_assets/images/eventbuizz_logo.png');
		}

		$logo = '<img src="' . $src . '" width="150" />';

		$template = getEmailTemplate($template, 9771);

        $subject_template = str_replace("{event_name}", stripslashes($event->name), $subject_template);

		$template = str_replace("{event_logo}", stripslashes($logo), $template);

		$template = str_replace("{event_name}", stripslashes($event->name), $template);

		$template = str_replace("{event_organizer_name}", "" . $event->organizer_name, $template);

        $export_file_name = $labels['EXPORT_ORDER_INVOICE'];
        
        $send = array();

        foreach($orders as $key => $order) {
            
            //$order['order_number'] > 608 && !in_array($order['order_number'], $send) 
            if($order_number && $order['order_number'] == $order_number) {

                if(!$order['additional_attendee']) {

                    $send[] = $order['order_number'];

                    $items = collect($orders_items)->where('order_number', $order['order_number'])->groupBy('item')->sortBy('sortBy')->all();
                    
                    $xml = $this->sendXml($mode, $event, $order, $items, $order_prefix);

                    $this->sendXmlEmail($event, $order, $xml);

                    /* $invoiceHtml = \View::make('admin.order.order_history.excel-invoice.detail', compact('order','items','labels'))->render();
    
                    $filename = '3_' . $order['order_number'] . '.pdf';
    
                    $file_to_save = config('cdn.cdn_upload_path') . 'assets' . DIRECTORY_SEPARATOR . 'fysio' . DIRECTORY_SEPARATOR . $filename;
    
                    if(!file_exists($file_to_save)) {

                        $snappy = \PDF::loadHTML($invoiceHtml)->setPaper('a4');
                    
                        $snappy->setOption('header-html',\View::make('admin.order.order_history.excel-invoice.header', compact('order','items','labels'))->render());
    
                        $snappy->setOption('print-media-type', true);
    
                        $snappy->setOption('margin-right',0);
    
                        $snappy->setOption('margin-left',0);
    
                        $snappy->save($file_to_save);

                        $attachments = [];

                        $attachments[] = ['path' => $file_to_save, 'name' => $export_file_name.$order['order_number'].'.pdf'];

                        $data = array();

                        $data['event_id'] = $event->id;

                        $data['template'] = $alias;

                        $data['subject'] = $subject_template;

                        $data['content'] = $template;

                        $data['view'] = 'email.plain-text';

                        $data['from_name'] =  $event->organizer_name;

                        $data['attachment'] =  $attachments;
             
                        \Mail::to('mms@eventbuizz.com')->send(new Email($data)); 
                    } */
                    
                }
            }
            
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function sendXml($mode, $event, $order, $items, $order_prefix)
    {
        $language_id = $event['language_id'];

        $event_id = $event['id'];

        $payment_setting = \App\Models\EventsitePaymentSetting::where('event_id', $event_id)->first();

        $eventSetting = \App\Models\EventSetting::where('event_id', $event_id)->first();

        $order_vat = 0;

        $organizer = \App\Models\Organizer::where('id', 1299)->first();

        $attendee = \App\Models\Attendee::where('email', $order['email'])->where('organizer_id', $organizer->id)->first()->toArray();

        $info = readArrayKey($attendee, [], 'info');

        $attendee = array_merge($info, $attendee);

        $order_number = $order['order_number'];

        $order_date = $order['order_date'];
        
        $eventsite_currency = $order['eventsite_currency'];

        $date = date("Y-m-d", strtotime($order_date));

        if ($payment_setting['bank_name'] == "0") {
            $payment_due_date = date('Y-m-d', strtotime($date . ' + 14 days'));
        } elseif ($payment_setting['bank_name'] == "1") {
            $payment_due_date = date('Y-m-d', strtotime($date . ' + 21 days'));
        } elseif ($payment_setting['bank_name'] == "2") {
            $payment_due_date = date('Y-m-d', strtotime($date . ' + 8 days'));

        } elseif ($payment_setting['bank_name'] == "3") {
            $payment_due_date = date('Y-m-d', strtotime($date . ' + 30 days'));
        } else {
            $payment_due_date = date("Y-m-d", strtotime($order_date));
        }

        if ($payment_setting['eventsite_invoice_prefix'] != '' && !$order_prefix) {
            $order_prefix = $payment_setting['eventsite_invoice_prefix'] . '-';
        } else {
            $order_prefix = $order_prefix . '-';
        }

        $currencies = getCurrencyArray();

        foreach ($currencies as $key => $cur) {
            if ($eventsite_currency == $key) {
                $currency = $cur;
            }
        }

        $account_number = $payment_setting['account_number'];

        $sub_total = $this->normalizeDecimal($order['sub_total']);

        $invoice = new \SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><Invoice></Invoice>");
        $invoice->addAttribute('xmlns:xmlns:cbc', "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
        $invoice->addAttribute('xmlns:xmlns:cac', "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");
        $invoice->addAttribute('xmlns:xmlns:ccts', "urn:oasis:names:specification:ubl:schema:xsd:CoreComponentParameters-2");
        $invoice->addAttribute('xmlns:xmlns:udt', "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2");
        $invoice->addAttribute('xmlns:xmlns', "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2");
        $invoice->addChild("xmlns:cbc:UBLVersionID", "2.0");
        $invoice->addChild("xmlns:cbc:CustomizationID", "OIOUBL-2.02");
        $childProfile = $invoice->addChild("xmlns:cbc:ProfileID", "urn:www.nesubl.eu:profiles:profile5:ver2.0");
        $childProfile->addAttribute('xmlns:schemeID', "urn:oioubl:id:profileid-1.2");
        $childProfile->addAttribute('xmlns:schemeAgencyID', "320");
        $invoice->addChild("xmlns:cbc:ID", $order_prefix . $order_number);
        $invoice->addChild("xmlns:cbc:IssueDate", date("Y-m-d", strtotime($order_date)));
        $childInvoiceTypeCode = $invoice->addChild("xmlns:cbc:InvoiceTypeCode", "380");
        $childInvoiceTypeCode->addAttribute('xmlns:listID', "urn:oioubl:codelist:invoicetypecode-1.1");
        $childInvoiceTypeCode->addAttribute('xmlns:listAgencyID', "320");
        $invoice->addChild("xmlns:cbc:DocumentCurrencyCode", "DKK");
        $invoice->addChild("xmlns:cbc:LineCountNumeric", "1");
        $AccountingSupplierParty = $invoice->addChild("xmlns:cac:AccountingSupplierParty");
        $Party = $AccountingSupplierParty->addChild("xmlns:cac:Party");
        $Party->addChild("xmlns:cbc:EndpointID", $mode == "test" ? 'DK12345678' : $organizer['vat_number'])->addAttribute('xmlns:schemeID', "DK:CVR");
        $Party->addChild("xmlns:cac:PartyName")->addChild("xmlns:cbc:Name", $organizer['first_name'] . ' ' . $organizer['last_name']);
        $PostalAddress = $Party->addChild("xmlns:cac:PostalAddress");
        $AddressFormatCode = $PostalAddress->addChild("xmlns:cbc:AddressFormatCode", "StructuredDK");
        $AddressFormatCode->addAttribute('xmlns:listID', "urn:oioubl:codelist:addressformatcode-1.1");
        $AddressFormatCode->addAttribute('xmlns:listAgencyID', "320");
        $PostalAddress->addChild("xmlns:cbc:StreetName", $organizer['address']);
        $PostalAddress->addChild("xmlns:cbc:BuildingNumber", $organizer['house_number']);
        $PostalAddress->addChild("xmlns:cbc:CityName", $organizer['city']);
        $PostalAddress->addChild("xmlns:cbc:PostalZone", $organizer['zip_code']);
        $Country = $PostalAddress->addChild("xmlns:cac:Country");
        $Country->addChild("xmlns:cbc:IdentificationCode", "DK");
        $Party->addChild("xmlns:cac:PartyLegalEntity")->addChild("xmlns:cbc:CompanyID", $mode == "test" ? 'DK12345678' : $organizer['vat_number'])->addAttribute('xmlns:schemeID', "DK:CVR");
        $contactChild = $Party->addChild("xmlns:cac:Contact");
        $contactChild->addChild("xmlns:cbc:Name", $event['name']);
        $contactChild->addChild("xmlns:cbc:ElectronicMail", $eventSetting['support_email']);
        $AccountingCustomerParty = $invoice->addChild("xmlns:cac:AccountingCustomerParty");
        $Party = $AccountingCustomerParty->addChild("xmlns:cac:Party");
        $EAN = $Party->addChild("xmlns:cbc:EndpointID", $mode == "test" ? '5798003842455' : $order['billing_ean']);
        $EAN->addAttribute('xmlns:schemeID', "GLN");
        $EAN->addAttribute('xmlns:schemeAgencyID', "9");
        $Party->addChild("xmlns:cac:PartyName")->addChild("xmlns:cbc:Name", htmlentities($order['company_name'], ENT_XML1));
        $PostalAddress = $Party->addChild("xmlns:cac:PostalAddress");
        $AddressFormatCode = $PostalAddress->addChild("xmlns:cbc:AddressFormatCode", "StructuredDK");
        $AddressFormatCode->addAttribute('xmlns:listID', "urn:oioubl:codelist:addressformatcode-1.1");
        $AddressFormatCode->addAttribute('xmlns:listAgencyID', "320");
        $PostalAddress->addChild("xmlns:cbc:StreetName", $order['billing_company_street']);
        $PostalAddress->addChild("xmlns:cbc:BuildingNumber", 'N/A');
        $PostalAddress->addChild("xmlns:cbc:CityName", $order['billing_company_city']);
        $PostalAddress->addChild("xmlns:cbc:PostalZone", $order['billing_company_post_code']);
        $Country = $PostalAddress->addChild("xmlns:cac:Country");
        $Country->addChild("xmlns:cbc:IdentificationCode", "DK");
        $billing_company_registration_number_local = str_replace(' ', '', $order['billing_company_registration_number']);
        $billing_company_registration_number_local = trim(str_replace('DK', '', $billing_company_registration_number_local));
        $Party->addChild("xmlns:cac:PartyLegalEntity")->addChild("xmlns:cbc:CompanyID", "DK" . $billing_company_registration_number_local)->addAttribute('xmlns:schemeID', "DK:CVR");
        $contactChild = $Party->addChild("xmlns:cac:Contact");
        $contactChild->addChild("xmlns:cbc:ID", "n/a");
        
        $contactChild->addChild("xmlns:cbc:Name", $attendee['first_name'] . ' ' . $attendee['last_name']);
        $contactChild->addChild("xmlns:cbc:Telephone", $attendee['phone']);
        $contactChild->addChild("xmlns:cbc:ElectronicMail", $attendee['email']);
        $PaymentMeans = $invoice->addChild("xmlns:cac:PaymentMeans");
        $PaymentMeans->addChild("xmlns:cbc:PaymentMeansCode", "42");
        $PaymentMeans->addChild("xmlns:cbc:PaymentDueDate", date("Y-m-d", strtotime($payment_due_date)));
        $PaymentChannelCode = $PaymentMeans->addChild("xmlns:cbc:PaymentChannelCode", "DK:BANK");
        $PaymentChannelCode->addAttribute('xmlns:listID', "urn:oioubl:codelist:paymentchannelcode-1.1");
        $PaymentChannelCode->addAttribute('xmlns:listAgencyID', "320");
        $PayeeFinancialAccount = $PaymentMeans->addChild("xmlns:cac:PayeeFinancialAccount");
        $PayeeFinancialAccount->addChild("xmlns:cbc:ID", substr($account_number, 4));
        $childBranch = $PayeeFinancialAccount->addChild("xmlns:cac:FinancialInstitutionBranch");
        $childBranch->addChild("xmlns:cbc:ID", substr($account_number, 0, 4));
        $childBranch->addChild("xmlns:cbc:Name", $payment_setting['bank_name']);
        $childBranch->addChild("xmlns:cac:Address"); 
        
        $total_vat_amount = 0;

        foreach($items as $addon) {
            $total_vat_amount += count($addon) * $this->normalizeDecimal($addon[0]['vat_amount']);
        }

        $TaxTotal = $invoice->addChild("xmlns:cac:TaxTotal");
        $TaxTotal->addChild("xmlns:cbc:TaxAmount", number_format((float) $total_vat_amount, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        
        $TaxSubtotal = $TaxTotal->addChild("xmlns:cac:TaxSubtotal");
        $TaxSubtotal->addChild("xmlns:cbc:TaxableAmount", number_format((float) $sub_total, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxSubtotal->addChild("xmlns:cbc:TaxAmount", number_format((float) $total_vat_amount, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxCategory = $TaxSubtotal->addChild("xmlns:cac:TaxCategory");
        $ID = $TaxCategory->addChild("xmlns:cbc:ID", "StandardRated");
        $ID->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxcategoryid-1.1");
        $ID->addAttribute('xmlns:schemeAgencyID', "320");
        $TaxCategory->addChild("xmlns:cbc:Percent", number_format((float) 25.00, 2, '.', ''));
        $TaxScheme = $TaxCategory->addChild("xmlns:cac:TaxScheme");
        $TaxScheme->addChild("xmlns:cbc:ID", "63")->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxschemeid-1.1");
        $TaxScheme->addChild("xmlns:cbc:Name", "Moms");
        $TaxScheme->addChild("xmlns:cbc:CurrencyCode", "DKK");
        
        //Extra Elements
        $LegalMonetaryTotal = $invoice->addChild("xmlns:cac:LegalMonetaryTotal");

        //Items
        $i = 0;

        foreach($items as $addon) {
            $this->sproomInvoiceItem($order, $invoice, $addon, $i, $order_vat);
            $i++;
        }

        $grand_total = $this->normalizeDecimal($order['grand_total']);

        $LegalMonetaryTotal->addChild("xmlns:cbc:LineExtensionAmount", number_format((float) $sub_total, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $LegalMonetaryTotal->addChild("xmlns:cbc:TaxExclusiveAmount", number_format((float) $total_vat_amount, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $LegalMonetaryTotal->addChild("xmlns:cbc:TaxInclusiveAmount", number_format((float) $grand_total, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $LegalMonetaryTotal->addChild("xmlns:cbc:PayableAmount", number_format((float) $grand_total, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");

        return $invoice->asXML();
    }

    public function normalizeDecimal($val, int $precision = 2): string
    {
        $input = str_replace(' ', '', $val);
        $number = str_replace(',', '.', $input);
        if (strpos($number, '.')) {
            $groups = explode('.', str_replace(',', '.', $number));
            $lastGroup = array_pop($groups);
            $number = implode('', $groups) . '.' . $lastGroup;
        }
        return bcadd($number, 0, $precision);
    }

    /**
     * sproomInvoiceItem
     *
     * @param  mixed $invoice
     * @param  mixed $addon
     * @param  mixed $i
     * @return void
     */
    public function sproomInvoiceItem($order_modal, $invoice, $addon, $i, $order_level_vat) {

        $qty = $addon[0]['qty'] * count($addon);
        $unit_price = $this->normalizeDecimal($addon[0]['unit_price']);
        $vat_amount = $this->normalizeDecimal($addon[0]['vat_amount']) * count($addon);
        $total = $this->normalizeDecimal($addon[0]['total']);
        $unit_price = $this->normalizeDecimal($addon[0]['unit_price']);
        $sub_total = $unit_price * $qty;
        $vat_percentage = $vat_amount > 0 ?  ($vat_amount / ($this->normalizeDecimal($total) * count($addon))) * 100 : 0;

        $invoiceLine = $invoice->addChild("xmlns:cac:InvoiceLine");
        $invoiceLine->addChild("xmlns:cbc:ID", $i);
        $invoiceLine->addChild("xmlns:cbc:InvoicedQuantity", number_format((float) $qty, 2, '.', ''))->addAttribute('xmlns:unitCode', "EA");
        $invoiceLine->addChild("xmlns:cbc:LineExtensionAmount", number_format((float) $sub_total, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        
        $TaxTotal = $invoiceLine->addChild("xmlns:cac:TaxTotal");
        $TaxTotal->addChild("xmlns:cbc:TaxAmount", number_format((float) ($vat_amount) , 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxSubtotal = $TaxTotal->addChild("xmlns:cac:TaxSubtotal");
        $TaxSubtotal->addChild("xmlns:cbc:TaxableAmount", number_format((float) $sub_total, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxSubtotal->addChild("xmlns:cbc:TaxAmount", number_format((float) $vat_amount , 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxCategory = $TaxSubtotal->addChild("xmlns:cac:TaxCategory");
        $ID = $TaxCategory->addChild("xmlns:cbc:ID", $vat_percentage > 0 ? "StandardRated" : "ZeroRated");
        $ID->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxcategoryid-1.1");
        $ID->addAttribute('xmlns:schemeAgencyID', "320");
        $TaxCategory->addChild("xmlns:cbc:Percent", number_format($vat_percentage, 2, '.', ''));
        $TaxScheme = $TaxCategory->addChild("xmlns:cac:TaxScheme");
        $TaxScheme->addChild("xmlns:cbc:ID", "63")->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxschemeid-1.1");
        $TaxScheme->addChild("xmlns:cbc:Name", "Moms");
        $TaxScheme->addChild("xmlns:cbc:CurrencyCode", "DKK");

        $Item = $invoiceLine->addChild("xmlns:cac:Item");
        $addon_name = $addon[0]['item'];
        $Item->addChild("xmlns:cbc:Description", stripslashes(strip_tags($addon_name)));
        $Item->addChild("xmlns:cbc:PackQuantity", ($qty));
        $Item->addChild("xmlns:cbc:PackSizeNumeric", number_format((float) $total, 2, '.', ''));
        $Item->addChild("xmlns:cbc:Name", stripslashes(strip_tags($addon_name)));
        $sellerID = $Item->addChild("xmlns:cac:SellersItemIdentification");
        $sellerID->addChild("xmlns:cbc:ID", $i);
        $Price = $invoiceLine->addChild("xmlns:cac:Price");
        $Price->addChild("xmlns:cbc:PriceAmount", number_format((float) ($sub_total / $qty), 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");

        return $invoice;
    }

    public function sendXmlEmail($event, $order, $xml)
    {
        $errors = '';
        $words_url = preg_replace('/[0-9]+/', '', $event['url']);
        $file_path = config('cdn.cdn_upload_path') . 'assets'.DIRECTORY_SEPARATOR.'eInvoice'. DIRECTORY_SEPARATOR . $words_url . '-' . $order['order_number'] . '.xml';
        $result = \File::put($file_path, $xml);
        $options = [
            'file_path' => $file_path,
        ];
        $result = \App\Libraries\Sproom\SproomApi::sproomAPI($options);
        if(isset($result->errors)) {
            foreach($result->errors as $error)
            {
                $errors .= $error->text.'<br/>';
            }
        }
        //unlink($file_path);
    }


    public function updateLayoutSection($type, $start_event_id = null, $end_event_id = null)
    {
        $layout_sections = [
            [
            "header" => 7,
            "top_banner" => 3,
            "register_now" => 2,
            "event_info" => 2,
            "custom_html3" => 2,
            "agenda" => 1,
            "time_table" => 1,
            "speaker" => 1,
            "attendee" => 1,
            "banner_sort" => 1,
            "social_media_share" => 3,
            "video" => 1,
            "gallery" => 1,
            "news" => 1,
            "exhibitor" => 2,
            "sponsor" => 7,
            "custom_html1" => 1,
            "custom_html2" => 1,
            "page_header" => 1,
            "newsletter_subscription" => 2,
            "map" => 1,
            "footer" => 1,
            ],

            [
            "header" => 1,
            "top_banner" => 5,
            "register_now" => 1,
            "event_info" => 1,
            "newsletter_subscription" => 2,
            "agenda" => 1,
            "time_table" => 1,
            "speaker" => 3,
            "attendee" => 3,
            "banner_sort" => 1,
            "social_media_share" => 4,
            "video" => 1,
            "gallery" => 1,
            "news" => 2,
            "exhibitor" => 4,
            "sponsor" => 1,
            "custom_html1" => 1,
            "custom_html2" => 1,
            "page_header" => 1,
            "custom_html3" => 1,
            "map" => 1,
            "footer" => 1,
            ],

            [
            "header" => 7,
            "top_banner" => 5,
            "register_now" => 2,
            "event_info" => 2,
            "newsletter_subscription" => 1,
            "agenda" => 1,
            "time_table" => 1,
            "banner_sort" => 1,
            "speaker" => 2,
            "attendee" => 2,
            "social_media_share" => 5,
            "video" => 5,
            "gallery" => 5,
            "news" => 3,
            "exhibitor" => 3,
            "sponsor" => 8,
            "custom_html1" => 1,
            "custom_html2" => 1,
            "page_header" => 1,
            "custom_html3" => 2,
            "map" => 1,
            "footer" => 1,
        ],

            [
            "header" => 7,
            "top_banner" => 2,
            "register_now" => 1,
            "event_info" => 2,
            "newsletter_subscription" => 4,
            "agenda" => 1,
            "time_table" => 1,
            "speaker" => 4,
            "banner_sort" => 1,
            "attendee" => 4,
            "social_media_share" => 6,
            "video" => 2,
            "gallery" => 2,
            "news" => 1,
            "exhibitor" => 5,
            "sponsor" => 7,
            "custom_html1" => 1,
            "custom_html2" => 1,
            "page_header" => 1,
            "custom_html3" => 2,
            "map" => 1,
            "footer" => 1,
            ],

        [
            "header" => 2,
            "top_banner" => 1,
            "register_now" => 3,
            "event_info" => 2,
            "newsletter_subscription" => 1,
            "agenda" => 1,
            "time_table" => 1,
            "speaker" => 9,
            "banner_sort" => 1,
            "attendee" => 9,
            "social_media_share" => 3,
            "video" => 8,
            "gallery" => 8,
            "news" => 2,
            "exhibitor" => 8,
            "sponsor" => 2,
            "custom_html1" => 1,
            "custom_html2" => 1,
            "page_header" => 1,
            "custom_html3" => 2,
            "map" => 1,
            "footer" => 1,
        ],

        [
            "header" => 6,
            "top_banner" => 5,
            "register_now" => 1,
            "event_info" => 1,
            "newsletter_subscription" => 2,
            "agenda" => 1,
            "time_table" => 1,
            "speaker" => 3,
            "attendee" => 3,
            "banner_sort" => 1,
            "social_media_share" => 5,
            "video" => 4,
            "gallery" => 4,
            "news" => 3,
            "exhibitor" => 6,
            "sponsor" => 2,
            "custom_html1" => 1,
            "custom_html2" => 1,
            "page_header" => 1,
            "custom_html3" => 1,
            "map" => 1,
            "footer" => 1,
        ],

        [
            "header" => 3,
            "top_banner" => 4,
            "register_now" => 4,
            "event_info" => 1,
            "newsletter_subscription" => 3,
            "agenda" => 1,
            "time_table" => 1,
            "speaker" => 8,
            "attendee" => 8,
            "banner_sort" => 1,
            "social_media_share" => 2,
            "video" => 3,
            "gallery" => 3,
            "news" => 1,
            "exhibitor" => 1,
            "sponsor" => 3,
            "custom_html1" => 1,
            "custom_html2" => 1,
            "page_header" => 1,
            "custom_html3" => 1,
            "map" => 1,
            "footer" => 1,
        ],

        ];
        if($type == "master"){
            $i = 1;
            foreach($layout_sections as $layout){
                $j=0;
                foreach ($layout as $key => $module) {
                    $exists = \App\Models\LayoutSection::where('layout_id', $i)->where('module_alias', $key)->first();
                    if($exists){
    
                        \App\Models\LayoutSection::where('layout_id', $i)
                        ->where('module_alias', $key)
                        ->update([
                            "variation_slug" => "Variation$module",
                            "status" => 1,
                            "sort_order" => $j
                        ]);
                    }
                    else{
                        
                        \App\Models\LayoutSection::create([
                            "layout_id" => $i,
                            "module_alias" => $key,
                            "variation_slug" => "Variation$module",
                             "status" => 1,
                             "sort_order" => $j
                        ]);
                    }
                    $j++;
                }
                $i++;
            }
        }

        else if($type == "event" && $start_event_id !== null && $end_event_id !== null){

            $events = \App\Models\Event::where('id', ">=", $start_event_id)->where('id', "<=", $end_event_id)->whereNull("deleted_at")->get();
            foreach ($events as $key => $event) {
                
                $i = 1;
                foreach($layout_sections as $layout){
                    $j=0;
                    foreach ($layout as $key => $module) {
                        $exists = \App\Models\EventLayoutSection::where('event_id', $event->id)->where('layout_id', $i)->where('module_alias', $key)->first();
                        if($exists){
        
                            \App\Models\EventLayoutSection::where('event_id', $event->id)
                            ->where('layout_id', $i)
                            ->where('module_alias', $key)
                            ->update([
                                "variation_slug" => "Variation$module",
                                "status" => 1,
                                "sort_order" => $j
                            ]);
                        }
                        else{
                            \App\Models\EventLayoutSection::create([
                                'module_alias' => $key,
                                'layout_id'=> $i,
                                "event_id" => "$event->id",
                                "variation_slug" => "Variation$module",
                                "status" => 1,
                                "sort_order" => $j
                            ]);
                        }
                        $j++;
                    }
                    $i++;
                }
            }
        }

        echo "done!";

        exit;
    }

    public function updateModuleVariationOptions()
    {
        //  update Header
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'header')
        ->update([
            'background_allowed' => 0,
            'text_align_allowed' => 0
        ]);
        // banner top
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'top_banner')
        ->update([
            'background_allowed' => 0,
            'text_align_allowed' => 0
        ]);
        // register now
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'register_now')
        ->update([
            'text_align_allowed' => 0
        ]);
        // event info
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'event_info')
        ->update([
            'text_align_allowed' => 0
        ]);
        // agenda
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'agenda')
        ->update([
            'text_align_allowed' => 0,
            'background_allowed' => 0
        ]);
        // social
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'social_media_share')
        ->update([
            'text_align_allowed' => 0
        ]);
        // video
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'video')
        ->update([
            'background_allowed' => 0
        ]);
        // gallery
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'gallery')
        ->update([
            'background_allowed' => 0
        ]);
        // exhibitor
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'exhibitor')
        ->update([
            'background_allowed' => 0
        ]);
        // sponsor
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'sponsor')
        ->update([
            'background_allowed' => 0
        ]);
        // newsletter
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'newsletter_subscription')
        ->update([
            'text_align_allowed' => 0
        ]);

        // Page header 
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->where('alias', 'page_header')
        ->update([
            'text_align_allowed' => 0
        ]);
        
        // map, timetabe, news
        \App\Models\ThemeModuleVariation::where('theme_id', 1)
        ->whereIn('alias', ['map', 'time_table', 'news'])
        ->update([
            'background_allowed' => 0,
            'text_align_allowed' => 0
        ]);


        $modules = [
            "attendee" => [2,5,6,7,8], 
            "speaker" => [2,5,6,7,8],
            "register_now" => [1,3,5],
            "social_media_share" => [1,2,4,5,6],
            "page_header" => [2,3],
            "newsletter_subscription" => [1,3],
            "event_info" => [2],
        ];


        foreach ($modules as $key => $values) {
            foreach ($values as $lock => $variation) {
                \App\Models\ThemeModuleVariation::where('theme_id', 1)
                ->where('alias', $key)
                ->where('variation_slug', 'Variation'.$variation)
                ->update([
                    'background_allowed' => 0
                ]);
            }
        }


         echo "done!";

        exit;
    }
}
