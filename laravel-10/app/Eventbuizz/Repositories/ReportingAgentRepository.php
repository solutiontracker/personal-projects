<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Support\Carbon;
class ReportingAgentRepository extends AbstractRepository
{
	public function __construct()
	{
	}

    function getOrganizerEventCountries($organizer_id) {
        $country_ids = \App\Models\Event::select(['country_id'])->where('organizer_id','=',$organizer_id)->groupBy('country_id')->pluck('country_id');
        $countries = \App\Models\Country::select(['id', 'name'])->whereIn('id', $country_ids)->pluck('name', 'id')->toArray();
        $new_countries = [];
        foreach ($countries as $key => $country) {
            $new_countries[]= ['id'=>$key, 'name'=>$country];
        }
        return $new_countries; 
    }

    function getOrganizerOfficeCountries($organizer_id) {
        $country_ids = \App\Models\Event::select(['office_country_id'])->where('organizer_id','=',$organizer_id)->groupBy('office_country_id')->pluck('office_country_id');
        $countries = \App\Models\Country::select(['id', 'name'])->whereIn('id', $country_ids)->pluck('name', 'id')->toArray();
        $new_countries = [];
        foreach ($countries as $key => $country) {
            $new_countries[]= ['id'=>$key, 'name'=>$country];
        }
        return $new_countries;  
    }

    function getOrganizerEventCurrencies($organizer_id) {

        $event_ids = \App\Models\Event::select(['id'])->where('organizer_id','=',$organizer_id)->pluck('id');
        $currencies = \App\Models\EventsitePaymentSetting::whereIn('event_id',$event_ids)->pluck('eventsite_currency')->toArray();
        $currency_array = getCurrencyArray();
        $new_currrencies=[];
        $added_currrencies=[];
        foreach($currencies as $curr) { 
            if($curr !== '' && $curr !== 0 && $curr !== '0' && !in_array($curr, $added_currrencies) && array_key_exists($curr, $currency_array)){
                $new_currrencies[] =  [ 'id' =>$curr, 'name'=> $currency_array[$curr]];
                $added_currrencies[] = $curr;
            }
        }
        return $new_currrencies;
    }

	public function getReportingAgentStats($formInput, $default_currency_name, $event_ids)
	{
		
        
        $totalReportingData =  \App\Models\ReportingRevenueTable::select(\DB::raw('SUM(total_tickets) as total_tickets, SUM(total_revenue) as total_revenue, SUM(waiting_tickets) as waiting_tickets'))->whereIn('event_id', $event_ids)->whereNull('deleted_at')->get()->toArray();
        $totalReportingData = $totalReportingData[0];

        $total_events_tickets = \App\Models\EventsiteSetting::select(\DB::raw('SUM(ticket_left) as ticket_left'))->whereIn('event_id', $event_ids)->where('registration_form_id', 0)->get()->toArray();
        $total_events_tickets = $total_events_tickets[0];
        
		$waiting_list_attendees = $totalReportingData['waiting_tickets'];
        
        $total_revenue = $totalReportingData['total_revenue'];
        
        $total_sold_tickets = $totalReportingData['total_tickets'];
        
        $total_tickets = $total_events_tickets['ticket_left'];
        		
		$range_reporting_stats =  $this->getRangeReportingStats($formInput, $event_ids, $default_currency_name);

		return [
			'waiting_list_attendees' => $waiting_list_attendees,
			'total_revenue' => $total_revenue,
			'total_revenue_text' => getCurrency($total_revenue,$default_currency_name).' '.$default_currency_name,
			'total_sold_tickets' => $total_sold_tickets,
			'total_tickets' => $total_tickets,
			'range_reporting_stats' => $range_reporting_stats,
			'totalReportingData' => $totalReportingData,
			'total_events_tickets' => $total_events_tickets,

		];


	}

	public function getRangeReportingStats($formInput, $event_ids, $default_currency_name)
	{

        $range = isset($formInput['range']) ? $formInput['range'] : '';
        if ($range == '') {
            $range = 0;
        }
        if ($range == 'today' || $range == '') {
            $start_date = date('Y-m-d');
            $end_date = '';
        } else if ($range == 'thisw') {
            $monday = strtotime('last monday', strtotime('tomorrow'));
            $sunday = strtotime('+6 days', $monday);
            $start_date = date('Y-m-d', $monday);
            $end_date = date('Y-m-d', $sunday);
        } else if ($range == 'prevw') {
            $previous_week = strtotime("-1 week +1 day");
            $start_week = strtotime("last monday", $previous_week);
            $end_week = strtotime("next sunday", $start_week);
            $start_date = date("Y-m-d", $start_week);
            $end_date = date("Y-m-d", $end_week);
        } else if ($range == 'thism') {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date = date('Y-m-t');
        } else if ($range == 'prevm') {
            $start_date = date('Y-m-d', strtotime('first day of last month'));
            $end_date = date('Y-m-d', strtotime('last day of last month'));
        } else if ($range == 'custom') {
            $start_date = date('Y-m-d', strtotime($formInput['start_date']));
            $end_date = date('Y-m-d', strtotime($formInput['end_date']));
        } else {
            $start_date = '';
            $end_date = '';
        }
        if($start_date != '' && $end_date == '') {
            $results = \App\Models\ReportingRevenueTable::select(\DB::raw('SUM(total_tickets) as total_tickets, SUM(total_revenue) as total_revenue, SUM(waiting_tickets) as waiting_tickets'))->whereIn('event_id',$event_ids)->whereDate('date','=',$start_date)->get()->toArray();
        }
        else if($start_date != '' && $end_date != '') {
            $results = \App\Models\ReportingRevenueTable::select(\DB::raw('SUM(total_tickets) as total_tickets, SUM(total_revenue) as total_revenue, SUM(waiting_tickets) as waiting_tickets'))->whereIn('event_id',$event_ids)->whereDate('date','>=',$start_date)->whereDate('date','<=',$end_date)->get()->toArray();
        }
        else {
            $results = \App\Models\ReportingRevenueTable::select(\DB::raw('SUM(total_tickets) as total_tickets, SUM(total_revenue) as total_revenue, SUM(waiting_tickets) as waiting_tickets'))->whereIn('event_id',$event_ids)->get()->toArray();
        }

		$total_range_revenue = $results[0]['total_revenue'];
        $range_sold_tickets = $results[0]['total_tickets'];
		$range_waiting_list_attendees = $results[0]['waiting_tickets'];

		return [
			'total_range_revenue' => $total_range_revenue,
			'total_range_revenue_text' => getCurrency($total_range_revenue,$default_currency_name).' '.$default_currency_name,
			'range_sold_tickets' => $range_sold_tickets,
			'range_waiting_list_attendees' => $range_waiting_list_attendees,
		];
	}

    public function getReportingAgentEvents($formInput, $user)
    {

        $organizer_id = $user->organizer_id;
       
        $organizerEventCurrencies = $this->getOrganizerEventCurrencies($organizer_id);
        $default_currency = $formInput['currency'] ? $formInput['currency'] : '';
        if ($default_currency == '') {
            foreach ($organizerEventCurrencies as $id => $curr_temp) {
                $default_currency = $curr_temp['id'];
                break;
            }
        }

		$currency_array = getCurrencyArray();
        $default_currency_name = $currency_array[$default_currency];


	
        $country = $formInput['country'];

        $officeLocationCountry = $formInput['office_country_id'];

        $event_ids = \App\Models\EventReportingAgent::where('reporting_agent_id', $user->id)->whereNull('deleted_at')->pluck('event_id');
        $allEvents = \App\Models\Event::whereIn('id', $event_ids)->whereNull('deleted_at');
        
        // location check
        if ($formInput['location']) {
            $allEvents->where('office_country_id', $formInput['location']);
        }
        
        // sort check
        if ($formInput['sort_by']) {
            $allEvents->orderBy($formInput['sort_by'], 'asc');
        }
        else
        {
            $allEvents->orderBy('start_date', 'ASC')->orderBy('start_time','ASC');
        }
        
        
        // search check
        if ($formInput['searchTextEvents']) {
            $allEvents->where('name', 'LIKE', '%' . $formInput['searchTextEvents'] . '%');
        }
        
        
        // action check
        $action = $formInput['event_action'] ? $formInput['event_action'] : 'active_future';

        if ($action == 'active_future') {

            $allEvents->where(function ($query) {
                $query->where('start_date', '>', Carbon::now()->format('Y-m-d'))
                    ->orwhere('end_date', '>=', Carbon::now()->format('Y-m-d'));
            });

        } else if ($action == 'active') {

            $allEvents->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where('end_date', '>=', Carbon::now()->format('Y-m-d'));

        } else if ($action == 'expired') {

            $allEvents->where('end_date', '<', Carbon::now()->format('Y-m-d'));

        } else if ($action == 'future') {

            $allEvents->where('start_date', '>', Carbon::now()->format('Y-m-d'));

        }

        //Country Filter
        if ($country != '' && $country != 0) {
            $allEvents->where('country_id', $country);
            
        }
        //Office Location (Country) Filter
        if ($officeLocationCountry != '' && $officeLocationCountry != 0) {
            $allEvents->where('office_country_id', $officeLocationCountry);
            
        }

        $currency = $formInput['currency'];

        if($currency != '' && $currency != 0){
            $allEvents = $allEvents->whereHas('eventsiteSettings', function ($q) {
                 return   $q->where('payment_type', 1)->where('registration_form_id', 0);
            })->whereHas('eventsitePaymentsettings', function ($q) use($currency) {
                return   $q->where('eventsite_currency', $currency)->where('registration_form_id', 0);
            });
        }

        $limit = $formInput['limit'] ? $formInput['limit'] : 10;

        $filtered_event_ids = $allEvents->pluck('id');

        $allEvents = $allEvents->paginate($limit)->toArray();
        
        $tickets_left=0;

        foreach ($allEvents['data'] as $key => $event) {

            $event = \App\Models\Event::where('id','=',$event['id'])->with(['eventsitesettings','info'=>function($q) use($event) {
                return $q->where('languages_id', '=', $event['language_id']);
            },'organizer','settings'=>function($q){
                return $q->where('name','=','header_logo');
            },'eventsitePaymentsettings'])->get()->toArray();


            $event = $event[0];
            $fields = array();
            
            foreach ($event['info'] as $info) {
                $fields[$info['name']] = $info['value'];
            }


            $owner = $event['organizer']['first_name'] . ' ' . $event['organizer']['last_name'];

            $image_name = '';

            if (count($event['settings']) > 0) {
                $image_name = $event['settings'][0]['value'];
            }

            $event_status = \App\Models\AssignPackageUsed::where('event_id', '=', $event['id'])->get()->toArray();
            $event_status = $event_status[0];

            $totalReportingData =  \App\Models\ReportingRevenueTable::select(\DB::raw('SUM(total_tickets) as total_tickets, SUM(total_revenue) as total_revenue, SUM(waiting_tickets) as waiting_tickets'))->where('event_id', $event['id'])->whereNull('deleted_at')->get()->toArray();
            $totalReportingData = $totalReportingData[0];

            $total_events_tickets = \App\Models\EventsiteSetting::select(\DB::raw('SUM(ticket_left) as ticket_left'))->where('event_id', $event['id'])->where('registration_form_id', 0)->get()->toArray();
            $total_events_tickets = $total_events_tickets[0];

            $allEvents['data'][$key]['info'] = $fields; 
            $allEvents['data'][$key]['owner'] = $owner; 
            $allEvents['data'][$key]['header_logo'] = $image_name; 
            $allEvents['data'][$key]['is_expire'] = $event_status['is_expire']; 
            $allEvents['data'][$key]['reporting_data']['sold_tickets'] = $totalReportingData['total_tickets']; 
            $allEvents['data'][$key]['reporting_data']['waiting_tickets'] = $totalReportingData['waiting_tickets']; 
            $allEvents['data'][$key]['reporting_data']['total_tickets'] = $total_events_tickets['ticket_left']; 
            
            $allEvents['data'][$key]['reporting_data']['total_revenue'] = $totalReportingData['total_revenue']; 
            $allEvents['data'][$key]['reporting_data']['total_revenue_text'] = getCurrency($totalReportingData['total_revenue'],$default_currency_name).' '.$default_currency_name; 
            
            $event_rang_data = $this->getRangeReportingStats($formInput, [$event['id']], $default_currency_name);

            $allEvents['data'][$key]['reporting_data']['total_range_revenue'] = $event_rang_data['total_range_revenue']; 
            $allEvents['data'][$key]['reporting_data']['total_range_revenue_text'] = $event_rang_data['total_range_revenue_text']; 
            $allEvents['data'][$key]['reporting_data']['range_sold_tickets'] = $event_rang_data['range_sold_tickets']; 
            $allEvents['data'][$key]['reporting_data']['range_waiting_list_attendees'] = $event_rang_data['range_waiting_list_attendees']; 
            
            $tickets_left = $total_events_tickets['ticket_left'] > 0 ? ($tickets_left + ($total_events_tickets['ticket_left'] - $totalReportingData['total_tickets'])) : $tickets_left;
        }

        $allEvents['stats'] = $this->getReportingAgentStats($formInput, $default_currency_name, $filtered_event_ids);
        $allEvents['stats']['tickets_left'] = $tickets_left;
        return $allEvents;
    }

    public function agentEventStatsAndOrders($formInput, $user, $event_id) 
    {
        
        $event = \App\Models\Event::where('id', '=', $event_id)->first();
        $event = $event ? $event->toArray() : $event;

        $payment_settings = \App\Models\EventsitePaymentSetting::where('event_id', '=', $event_id)->first();
        
        $is_free = $payment_settings->eventsite_billing == 1  ? 0 : 1;

        $billing_currency = getCurrencyArray();
        $default_currency_name		= $billing_currency[$payment_settings->eventsite_currency];
        
        $data = $this->getReportingEventOrders($formInput, $event_id, $event['language_id'], $is_free,'0', $formInput['regFormId']);

        $brand_logo = '';
        $event_name = '';
        $event_date = '';
        $event_location = '';


        $event_name = $event['name'];

        $event_settings = \App\Models\EventSetting::where('event_id', '=', $event_id)->where('name', '=', 'header_logo')->get()->toArray();

        if ($event_settings[0]['value'] != '' && $event_settings[0]['value'] != 'NULL') {
            $src = '/assets/event/branding/' . $event_settings[0]['value'];
        } else {
            $src = "/_admin_assets/images/eventbuizz_logo.png";
        }


        $brand_logo = $src;
        $event_date = date('d F Y',strtotime($event['start_date'])).' - '.date('d F Y',strtotime($event['end_date']));
        $event_address = \App\Models\EventInfo::where('event_id','=',$event_id)
                        ->where('languages_id','=',$event['language_id'])
                        ->where('name','=','location_address')
                        ->get()
                        ->toArray();
        $event_location = \App\Models\EventInfo::where('event_id','=',$event_id)
                        ->where('languages_id','=',$event['language_id'])
                        ->where('name','=','location_name')
                        ->get()
                        ->toArray();
        
        if(trim($event_address[0]['value']) != '' && trim($event_location[0]['value']) != '') {
            $event_location = $event_address[0]['value'].', '.$event_location[0]['value'];
        }
        else if(trim($event_address[0]['value']) != '') {
            $event_location = $event_address[0]['value'];
        }
        else if(trim($event_location[0]['value']) != '') {
            $event_location = $event_location[0]['value'];
        }
        else {
            $event_location = '';
        }

        $event_stats = array(
            'waiting'=>'0',
            'total_tickets'=>'0',
            'tickets_left'=>'0',
            'tickets_sold'=>'0'
        );
        $waiting_attendees = \App\Models\WaitingListAttendee::where('event_id','=',$event_id)
                            ->whereNull('deleted_at')
                            ->get()
                            ->toArray();

        foreach($waiting_attendees as $waiting) {
            if($waiting['status'] == '1' || $waiting['status'] == '0') {
                $event_stats['waiting'] += '1';
            }
        }

        $eventsite_settings = \App\Models\EventsiteSetting::where('event_id','=',$event_id)->get()->toArray();
        $eventsite_settings = $eventsite_settings[0];
        $registration_end_date = $eventsite_settings['registration_end_date'];
        
        $date_ended = false;
        if(trim($registration_end_date) != '0000-00-00 00:00:00' ) {
            $end_date = date('Y-m-d',strtotime($registration_end_date));
            $today_date = date('Y-m-d');
            if($end_date<$today_date) {
                $date_ended = true;
            }
        }

        $event_stats['total_tickets'] = $eventsite_settings['ticket_left'];
        if($event_stats['total_tickets'] == '') {
            $event_stats['total_tickets'] = '0';
        }

        $data_all = $this->getRangeOrders($formInput, array($event_id), $event['language_id']);
        $sales_agent_stats = array('tickets_sold'=>'0','revenue'=>'0');
        foreach($data_all as $order) {
            $event_orders = \App\Models\BillingOrder::where('event_id','=',$event_id)
                ->where('id','=',$order['billing_order_id'])
                ->with(['order_attendees'])
                ->get()->toArray();
            foreach($event_orders as $eorder) {
                if(count($eorder['order_attendees']) == 0) {
                    $sales_agent_stats['tickets_sold'] += 1;
                }
                else {
                    $sales_agent_stats['tickets_sold'] += count($eorder['order_attendees']);
                }
            }
            $total = $order['reporting_panel_total'];
            $sales_agent_stats['revenue'] += $total;
        }



        $i=0;
        foreach ($data as $row) {
            $temp = array();
            if (count($row['order_attendee']['detail']) > 0) {
                foreach ($row['order_attendee']['detail'] as $val) {
                    $temp[$val['name']] = $val['value'];
                }
            }
            unset($row['order_attendee']['detail']);
            $row['order_attendee']['detail'] = $temp;
            $data[$i] = $row;
            $tickets = \App\Models\BillingOrderAttendee::where('order_id','=',$row['billing_order_id'])->count();
            $data[$i]['tickets_sold'] = $tickets;
            $total = $row['reporting_panel_total'];
            $data[$i]['corrected_total'] = $total;
            $data[$i]['reporting_panel_total_text'] = getCurrency($row['reporting_panel_total'], $default_currency_name).' '.$default_currency_name;
            $sale_agent =  $row['sale_agent_id'] != 0 ? \App\Models\SaleAgent::find($row['sale_agent_id']) : null;
            $data[$i]['sales_agent_name'] = $sale_agent ?  $sale_agent->first_name.' '.$sale_agent->last_name : 'Reg. site';
            $i++;
        }

        
        $eventsite_settings = \App\Models\EventsiteSetting::select('payment_type')
            ->where('event_id', '=', $event_id)->get()->toArray();
        $eventsite_settings = $eventsite_settings[0];
        if($eventsite_settings['payment_type']==0) {
            $pageHeading = "Order History";
        }
        if($eventsite_settings['payment_type']==1) {
            $pageHeading = "Billing list";
        }
        
        $registration_end_date = date('m/d/Y',strtotime($registration_end_date));
        $waiting_list_status = \App\Models\EventWaitingListSetting::where('event_id', '=', $event_id)->get()->toArray();
        
        //Calculating All order sold and revenue
        $payment_settings = \App\Models\EventsitePaymentSetting::where('event_id','=',$event_id)->get()->toArray();
        $payment_settings = $payment_settings[0];
        $eventsite_settings = \App\Models\EventsiteSetting::where('event_id','=',$event_id)->get()->toArray();
        $eventsite_settings = $eventsite_settings[0];
        $total_event_tickets = $eventsite_settings['ticket_left'];
        
        
        
        $parent_orders = \App\Models\BillingOrder::where('event_id', '=', $event_id)
                        ->where('status', '<>', 'cancelled')
                        ->where('is_free', '=', $is_free)
                        ->where('parent_id','=','0')
                        ->where('is_archive','=','0')
                        ->with('child_orders')
                        ->get()
                        ->toArray();
        $order_ids_to_display = [];
        foreach ($parent_orders as $orderTemp) {
            $child_orders = $orderTemp['child_orders'];
            if(count($child_orders)>0) {
                $child_orders = $child_orders[count($child_orders)-1];
                $order_ids_to_display[] = $child_orders['id'];
            }
            else {
                $order_ids_to_display[] = $orderTemp['id'];
            }
        }
        $event_orders_all = \App\Models\BillingOrder::where('event_id','=',$event_id)
                            ->whereIn('id',$order_ids_to_display)
                            ->where('is_archive','=','0')
                            ->with(['order_attendees'])
                            ->whereNull('deleted_at')
                            ->get()
                            ->toArray();

        $event_tickets_left = 0;
        $total_revenue_event = 0;
        $total_sold_event_tickets = 0;
        foreach($event_orders_all as $eorder) {
            $total = $eorder['reporting_panel_total'];
            $total_revenue_event += $total;
            if(count($eorder['order_attendees']) == 0) {
                $total_sold_event_tickets += 1;
            }
            else {
                $total_sold_event_tickets += count($eorder['order_attendees']);
            }
        }
        if($total_event_tickets != '' && $total_event_tickets != 0) {
            $event_tickets_left = $total_event_tickets - $total_sold_event_tickets;
        }
        $settings = \App\Models\ReportingAgentSetting::where('organizer_id','=', $user->organizer_id)->get()->toArray();
        $settings = $settings[0];

        $countries = \App\Models\Country::get();

        $reporting_data = \App\Models\ReportingRevenueTable::select(\DB::raw('SUM(total_tickets) as total_tickets, SUM(total_revenue) as total_revenue, SUM(waiting_tickets) as waiting_tickets'))
            ->where('event_id','=',$event_id)->whereNull('deleted_at')->get()->toArray();
        $reporting_data = $reporting_data[0];
        $reporting_data['total_revenue_text'] = getCurrency($reporting_data['total_revenue'],$default_currency_name).' '.$default_currency_name;
        $orders_range_stats = $this->getRangeReportingStats($formInput, [$event_id], $default_currency_name);
    
        return [
            'event'=> [
                'event_id' => $event_id,
                'brand_logo' => $brand_logo,
                'event_name' => $event_name,
                'event_date' => $event_date,
                'event_location' => $event_location,
                'date_ended' => $date_ended,
                'registration_end_date' => $registration_end_date,
                'registration_form_id' => $event['registration_form_id'],
            ],
            'paymentSettings' => $payment_settings,
            'event_stats' =>[
                'event_stats' => $event_stats,    
                'default_currency_name' => $default_currency_name,    
                'total_revenue_event' => $total_revenue_event,
                'total_sold_event_tickets' => $total_sold_event_tickets,
                'event_tickets_left' => $event_tickets_left,
                'sales_agent_stats' => $sales_agent_stats,
                'orders_range_stats' => $orders_range_stats,
                'reporting_data' => $reporting_data,
                'waiting_list_status' => $waiting_list_status,
            ],
            'data' => $data,
        ];
    }


    public function getReportingEventOrders($formInput, $event_id, $language_id, $is_free = 0, $is_archived = 0, $registration_form_id = 0)
    {
        
        $sortBy = 'order_number';
        $order = 'desc';
        $searchKey = '';
        $searchOperator = '<>';
        $searchField = 'conf_billing_orders.status';
        $searchValue = 'null';
        $start_date = '';
        $end_date = '';
        $range = isset($formInput['range']) ? $formInput['range'] : 0;


        
        $searchKey = isset($formInput['searchText']) ? trim($formInput['searchText']) : '';
        
        if ($range == 'today') {
            $start_date = date('Y-m-d');
            $end_date = '';
        } else if ($range == 'thisw') {
            $monday = strtotime('last monday', strtotime('tomorrow'));
            $sunday = strtotime('+6 days', $monday);
            $start_date = date('Y-m-d', $monday);
            $end_date = date('Y-m-d', $sunday);
        } else if ($range == 'prevw') {
            $previous_week = strtotime("-1 week +1 day");

            $start_week = strtotime("last monday", $previous_week);
            $end_week = strtotime("next sunday", $start_week);

            $start_date = date("Y-m-d", $start_week);
            $end_date = date("Y-m-d", $end_week);
        } else if ($range == 'thism') {
            $start_date = date('Y-m-01'); //hard-coded '01' for first day
            $end_date = date('Y-m-t');
        } else if ($range == 'prevm') {
            $start_date = date('Y-m-d', strtotime('first day of last month'));
            $end_date = date('Y-m-d', strtotime('last day of last month'));
        } else if ($range == 'custom') {
            $start_date = date('Y-m-d', strtotime($formInput['start_date']));
            $end_date = date('Y-m-d', strtotime($formInput['end_date']));
        }
        else{
            $start_date='';
            $end_date='';
        }


        $columns = [
            'conf_billing_orders.*',
            'conf_billing_orders.id as billing_order_id',
            'conf_billing_orders.status as billing_order_status'
        ];

        $valid_order_ids = \App\Models\BillingOrder::where('event_id',$event_id)->where('is_free', $is_free)->where('is_archive','=', $is_archived)->currentActiveOrders()->pluck('id');
        
        if($registration_form_id != 0) {
            $valid_order_ids = \App\Models\BillingOrderAttendee::whereIn('order_id', $valid_order_ids)->where('registration_form_id', $registration_form_id)->pluck('order_id');
        }


        $result = \App\Models\BillingOrder::where('event_id', '=', $event_id)
            ->where($searchField, $searchOperator, $searchValue)
            ->where('is_waitinglist','=','0')
            ->where('conf_billing_orders.status','=','completed')
            ->with(['order_attendee.detail' => function ($query) use($language_id) {
                return $query->where('languages_id', '=', $language_id);
            }, 'order_attendees', 'order_attendees.attendee_detail']);


        if (trim($start_date) != '' && trim($end_date) != '') {
            $result->whereDate('order_date', '>=', $start_date)->whereDate('order_date', '<=', $end_date);
        } else if (trim($start_date) != '' && trim($end_date) == '') {
            $result->whereDate('order_date', '=', $start_date);
        }

        $result->whereIn('conf_billing_orders.id', $valid_order_ids);

        $result->whereNull('conf_billing_orders.deleted_at');

        //Limit
        $limit = isset($formInput['limit']) ? $formInput['limit'] : 10;


        $temp_order_id = array();

        $results_temp = $result->get();

        foreach($results_temp as $rec_order) {
            $temp_order_id[] = $rec_order->id;
        }

        $result->join('conf_billing_order_attendees', 'conf_billing_orders.id', '=', 'conf_billing_order_attendees.order_id');

        $result->join('conf_attendees', 'conf_attendees.id', '=', 'conf_billing_order_attendees.attendee_id');

        $result->join('conf_attendees_info', 'conf_attendees_info.attendee_id', '=', 'conf_billing_orders.attendee_id');

        //search from attendee Table
        if (trim($searchKey) != '') {
            //search from order table
            if ($formInput['field'] == "order_number") {
                $result->where('conf_billing_orders.order_number', '=', $searchKey);
            } else if($formInput['field'] == "name") {
                $result->where('conf_attendees.id', '<>', '');
                $result->whereRaw("LOWER(CONCAT(conf_attendees.first_name, ' ', conf_attendees.last_name)) LIKE ?", ['%' . strtolower($searchKey) . '%']);
            } else if($formInput['field'] == "email") {
                $result->where('conf_attendees.id', '<>', '');
                $result->whereRaw("LOWER(conf_attendees.email) LIKE ?", ['%' . strtolower($searchKey) . '%']);
            } else if($formInput['field'] == "job_title") {
                $result->whereIn('name', ['title'])
                ->groupBy('conf_attendees_info.attendee_id')
                ->whereRaw("LOWER(conf_attendees_info.value) LIKE ?", ['%' . strtolower($searchKey) . '%']);
            } else if($formInput['field'] == "company") {
                $result->whereIn('name', ['company_name'])
                ->groupBy('conf_attendees_info.attendee_id')
                ->whereRaw("LOWER(conf_attendees_info.value) LIKE ?", ['%' . strtolower($searchKey) . '%']);
            }
        }

        if(isset($formInput['sort']) && isset($formInput['sort_col'])) {
            $sort_val = $formInput['sort_col'];
            $sort_type = $formInput['sort'];
            $sorting_columns = [
                'order_number' => 'order_number',
                'order_date' => 'order_date',
                'name' => "CONCAT(conf_attendees.first_name,conf_attendees.last_name)",
                'email' => "conf_attendees.email",
                'amount' => 'grand_total',
                'job_title' => "conf_attendees_info.name = 'title'",
                'company' => "conf_attendees_info.name = 'company_name'",
                'sales_agent' => 'sale_agent_id',
                'order_status' => 'status',
                'payment_status' => 'is_payment_received'
            ];
            $sort = [
                'asc' => 'asc',
                'desc' => 'desc'
            ];
            if(isset($sorting_columns[$sort_val]) && isset($sort[$sort_type])){
                if($sort_val == 'job_title') {
                        $result->whereIn('name', ['title'])
                        ->groupBy('conf_attendees_info.attendee_id')
                        ->orderBy(\DB::raw('conf_attendees_info.value'), $sort[$sort_type]);
                } else if($sort_val == 'company') {
                    $result->whereIn('name', ['company_name'])
                    ->groupBy('conf_attendees_info.attendee_id')
                    ->orderBy(\DB::raw('conf_attendees_info.value'), $sort[$sort_type]);
                } else if($sort_val == 'name' || $sort_val == 'email') {
                    $result->orderBy(\DB::raw($sorting_columns[$sort_val]), $sort[$sort_type]);
                } else {
                    $result->orderBy($sorting_columns[$sort_val], $sort[$sort_type]);
                }
            }
        } else {
            $result->orderBy($sortBy, $order);
        }
        $result->select($columns);
        $result->groupBy('conf_billing_orders.id');
        $result = $result->paginate($limit);
        return $result;
    }

    public function getRangeOrders($formInput, $event_id_array, $language_id, $is_free = 0, $is_archived = 0)
    {
        $sortBy = 'order_number';
        $order = 'desc';
        $searchKey = '';
        $searchOperator = '<>';
        $searchField = 'conf_billing_orders.status';
        $searchValue = 'null';
        $start_date = '';
        $end_date = '';
        $range = $formInput['range'];

        if ($formInput['searchText']) {
            $searchKey = '%' . $formInput['searchText'] . '%';
        }

        if ($range == '') {
            $range = 0;
        }

        if ($range == 'today' || $range == '') {
            $start_date = date('Y-m-d');
            $end_date = '';
        } else if ($range == 'thisw') {
            $monday = strtotime('last monday', strtotime('tomorrow'));
            $sunday = strtotime('+6 days', $monday);
            $start_date = date('Y-m-d', $monday);
            $end_date = date('Y-m-d', $sunday);
        } else if ($range == 'prevw') {
            $previous_week = strtotime("-1 week +1 day");
            $start_week = strtotime("last monday", $previous_week);
            $end_week = strtotime("next sunday", $start_week);
            $start_date = date("Y-m-d", $start_week);
            $end_date = date("Y-m-d", $end_week);
        } else if ($range == 'thism') {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date = date('Y-m-t');
        } else if ($range == 'prevm') {
            $start_date = date('Y-m-d', strtotime('first day of last month'));
            $end_date = date('Y-m-d', strtotime('last day of last month'));
        } else if ($range == 'custom') {
            $start_date = date('Y-m-d', strtotime($formInput['start_date']));
            $end_date = date('Y-m-d', strtotime($formInput['end_date']));
        } else {
            $start_date = '';
            $end_date = '';
        }

        $columns = [
            'conf_billing_orders.*',
            'conf_billing_orders.id as billing_order_id',
            'conf_billing_orders.status as billing_order_status',
        ];

        $valid_order_ids = \App\Models\BillingOrder::whereIn('event_id',$event_id_array)->where('is_free', $is_free)->where('is_archive','=', $is_archived)->currentActiveOrders()->pluck('id');

        if($formInput['regFormId'] != 0) {
            $valid_order_ids = \App\Models\BillingOrderAttendee::whereIn('order_id', $valid_order_ids)->where('registration_form_id', $formInput['regFormId'])->pluck('order_id');
        }
        
        
        $result = \App\Models\BillingOrder::whereIn('event_id', $event_id_array)
            ->where($searchField, $searchOperator, $searchValue)
            ->where('is_waitinglist', 0)
            ->where('conf_billing_orders.status','=','completed')
            ->with(['order_attendee.info' => function ($query) use($language_id) {
                return $query->where('languages_id', '=', $language_id);
            }, 'order_attendees']);

        if (trim($start_date) != '' && trim($end_date) != '') {
            $result->whereDate('order_date', '>=', $start_date)->whereDate('order_date', '<=', $end_date);
        } else if (trim($start_date) != '' && trim($end_date) == '') {
            $result->whereDate('order_date', '=', $start_date);
        }

        $result->whereIn('conf_billing_orders.id', $valid_order_ids);

        if (trim($searchKey) != '') {
            if (is_numeric(trim($formInput['searchText']))) {
                $result->where('order_number', '=', $formInput['searchText']);
            } else {
                $result->whereHas('order_attendee', function ($q) use ($searchKey) {
                    $q->where('id', '<>', '')
                        ->where(function ($query) use ($searchKey) {
                            $query->where('first_name', 'LIKE', '%' . $searchKey . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $searchKey . '%');
                        });
                });
            }
        }

        $result->whereNull('conf_billing_orders.deleted_at');

        $temp_order_id = array();

        $results_temp = $result->get();

        foreach($results_temp as $rec_order) {
            $temp_order_id[] = $rec_order->id;
        }

        if($formInput['sort'] && $formInput['sort_col']) {
            $sort_val = $formInput['sort_col'];
            $sort_type = $formInput['sort'];
            $sorting_columns = [
                'order_number' => 'order_number',
                'order_date' => 'order_date',
                'name' => "CONCAT(conf_attendees.first_name,conf_attendees.last_name)",
                'email' => "conf_attendees.email",
                'amount' => 'grand_total',
                'job_title' => "conf_attendees_info.name = 'title'",
                'company' => "conf_attendees_info.name = 'company_name'",
                'sales_agent' => 'sale_agent_id',
                'order_status' => 'status'
            ];
            $sort = [
                'asc' => 'asc',
                'desc' => 'desc'
            ];
            if(isset($sorting_columns[$sort_val]) && isset($sort[$sort_type])){
                if($sort_val == 'job_title' || $sort_val == 'company'){
                    $result->join('conf_attendees_info', 'conf_attendees_info.attendee_id', '=', 'conf_billing_orders.attendee_id')
                        ->whereIn('name', ['title', 'company_name'])
                        ->groupBy('conf_attendees_info.attendee_id')
                        ->orderBy(\DB::raw('conf_attendees_info.value'), $sort[$sort_type]);
                }else if($sort_val == 'name' || $sort_val == 'email'){
                    $result->join('conf_attendees', 'conf_attendees.id', '=', 'conf_billing_orders.attendee_id')
                        ->orderBy(\DB::raw($sorting_columns[$sort_val]), $sort[$sort_type]);
                }else{
                    $result->orderBy($sorting_columns[$sort_val], $sort[$sort_type]);
                }
            }
        } else {
            $result->orderBy($sortBy, $order);
        }

        $result->select($columns);
        $result = $result->get()->toArray();
        return $result;
    }

}