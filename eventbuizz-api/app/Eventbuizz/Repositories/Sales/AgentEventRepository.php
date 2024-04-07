<?php

namespace App\Eventbuizz\Repositories\Sales;


use App\Eventbuizz\Repositories\AbstractRepository;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use App\Http\Helpers\HttpHelper;
use App\Models\Event;
use App\Models\EventSaleAgent;
use Illuminate\Http\Request;
use App\Models\SaleAgent;
use App\Models\EventSetting;
use App\Models\EventWaitingListSetting;
use App\Models\WaitingListAttendee;
use App\Models\EventAttendee;
use App\Models\EventsitePaymentSetting;
use App\Models\EventsiteSetting;
use Carbon\Carbon;

class AgentEventRepository extends AbstractRepository
{

    protected $input;
    protected $model;
    protected $saleAgentRepository;
    protected $eventsiteBillingOrderRepository;


    public function __construct(Request $input, Event $model, SaleAgentRepository $saleAgentRepository, EventsiteBillingOrderRepository $eventsiteBillingOrderRepository)
    {
        $this->input = $input;
        $this->model = $model;
        $this->saleAgentRepository = $saleAgentRepository;
        $this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
    }


    public function getAgentEvents($saleAgentId)
    {
        try {
            $action = ((isset($this->input['event_action']) && $this->input['event_action']) ? $this->input['event_action'] : '');
            $sort = ((isset($this->input['sort_by']) && $this->input['sort_by']) ? $this->input['sort_by'] : 'start_date');
            $limit = ((isset($this->input['limit']) && $this->input['limit']) ? $this->input['limit'] : 50);

            $saleAgent = $this->saleAgentRepository->getAgentByColumn('id', $saleAgentId);
            $agentEvents = $saleAgent->events();

            // search text filter
            if (isset($this->input['search_text']) && $this->input['search_text']) {
                $agentEvents = $agentEvents->where(function ($query) {
                    $query->where('name', 'LIKE', '%' . trim($this->input['search_text']) . '%');
                });
            }

            // apply event type|date filter
            if ($action == 'active_future') {
                $agentEvents = $agentEvents->where(function ($query){ 
                    $query->where('start_date', '>', Carbon::now()->format('Y-m-d'))
                    ->orwhere('end_date', '>=', Carbon::now()->format('Y-m-d'));
                });
            } elseif ($action == 'active') {
                $agentEvents = $agentEvents->where(function ($query){
                    $query->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
                    ->where('end_date', '>=', Carbon::now()->format('Y-m-d'));
                });
            } elseif ($action == 'expired') {
                $agentEvents = $agentEvents->where('end_date', '<', Carbon::now()->format('Y-m-d'));
            } elseif ($action == 'future') {
                $agentEvents = $agentEvents->where('start_date', '>', Carbon::now()->format('Y-m-d'));
            }

            $agentEvents = $agentEvents->where('registration_form_id', 1)->orderBy($sort, 'ASC')->paginate($this->input['limit']);
            // $agentEvents = $agentEvents->orderBy($sort, 'ASC')->paginate(2);

            $processedEvents = [];
            foreach ($agentEvents->items() as $key => $event) {
                $eventSettings = EventSetting::where('event_id', '=', $event['id'])->where('name', '=', 'header_logo')->get()->toArray();
                $header_logo = '';
                if ($eventSettings[0]['value'] != '' && $eventSettings[0]['value'] != 'NULL') {
                    $header_logo = 'assets/event/branding/' . $eventSettings[0]['value'];
                } else {
                    $header_logo = '_admin_assets/images/eventbuizz_logo.png';
                }
                $processedEvents[$key]['header_logo'] = $header_logo;

                //STATS
                $eventId = $event['id'];
                

                $eventSiteSettings = EventsiteSetting::where('event_id', '=', $eventId)->get()->toArray();
                $eventSiteSettings = $eventSiteSettings[0];

                $registration_end_date = $eventSiteSettings['registration_end_date'];

                $eventStats['total_tickets'] = $eventSiteSettings['ticket_left'];
                if ($eventStats['total_tickets'] == '') {
                    $eventStats['total_tickets'] = 0;
                }

                $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $eventId, 'status' => ['completed'], 'waiting_list' => 0], false, true);
                //Validate form stock
                $soldTickets = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids], true);

                $eventStats['tickets_sold'] = $soldTickets;

                if ($eventStats['total_tickets'] == 0 || $eventStats['total_tickets'] == '') {
                    $eventStats['tickets_left'] = 0;
                } else {
                    $eventStats['tickets_left'] = (int) $eventStats['total_tickets'] - (int) $eventStats['tickets_sold'];
                }

                $saleAgentOrders = SaleAgent::where('id', '=', $saleAgentId)->with(['orders' => function ($query) use ($eventId) {
                    return $query->where('event_id', '=', $eventId)->whereIn('status', ['completed'])->where('is_waitinglist', 0)->currentActiveOrders();
                }, 'orders.order_attendees'])->get()->toArray();

                $salesAgentStats = array('tickets_sold' => 0, 'revenue' => 0);
                foreach ($saleAgentOrders[0]['orders'] as $order) {
                    $salesAgentStats['tickets_sold'] += count($order['order_attendees']);
                    $salesAgentStats['revenue'] += $order['reporting_panel_total'];
                }

                $paymentSettings = EventsitePaymentSetting::where('event_id', '=', $eventId)->first();
                $is_free = 1;
                if ($paymentSettings->eventsite_billing == 1) {
                    $is_free = 0;
                }
                $this->billing_currency = getCurrencyArray();
                $currency = $this->billing_currency[$paymentSettings->eventsite_currency];

                // $processedEvents[$key]['orders'] = $saleAgentOrders[0]['orders'];
                $processedEvents[$key]['event_stats'] = $eventStats;
                $processedEvents[$key]['sale_agent_stats'] = $salesAgentStats;
                $processedEvents[$key]['waiting_list_status'] = $waitingListStatus;
                $processedEvents[$key]['currency'] = $currency;

                // merge processed event
                $processedEvents[$key] = array_merge($event->toArray(), $processedEvents[$key]);
            }

            $result = [
                'agent' => $saleAgent,
                'events' => $processedEvents,
                'paginate' => HttpHelper::paginator($agentEvents)
            ];

            return HttpHelper::successResponse('Events', $result);
        } catch (\Exception $e) {
            return HttpHelper::errorResponse('Something went wrong, please try again later');
        }
    }

    public function getEventWithOrders($formInput, $event_id, $sales_agent_id)
    {
        
        $event = \App\Models\Event::where('id', $event_id)->first();

        $event_language_id = $event->language_id;

        $payment_settings = \App\Models\EventsitePaymentSetting::where('event_id', '=', $event_id)->first();
        
        $is_free= $payment_settings->eventsite_billing == 1 ? 0 : 1 ;

        $brand_logo =\App\Models\EventSetting::where('event_id','=',$event_id)
        ->where('name','=','header_logo')
        ->first();

        $event_name = $event['name'];
        $event_date = date('d F Y',strtotime($event['start_date'])).' - '.date('d F Y',strtotime($event['end_date']));
        $event_address = \App\Models\EventInfo::where('event_id','=',$event_id)
        ->where('languages_id','=',$event_language_id)
        ->where('name','=','location_address')
        ->first();

        $event_location = \App\Models\EventInfo::where('event_id','=',$event_id)
        ->where('languages_id','=',$event_language_id)
        ->where('name','=','location_name')
        ->first();
        
        $event_location = ($event_address ? $event_address->value : '').($event_location ? ', '.$event_location->value : '');


        $event_stats = array('waiting'=>'0','total_tickets'=>'0','tickets_left'=>'0','tickets_sold'=>'0');

        // 
        $waiting_attendees = \App\Models\WaitingListAttendee::where('event_id','=',$event_id)->where(function($q){
            $q->where('status', 1)->orWhere('status', 0);
        })->count();
        $event_stats['waiting'] = $waiting_attendees;
        

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


        $event_stats['total_tickets'] = $eventsite_settings['ticket_left'] !== '' ? (int) $eventsite_settings['ticket_left'] : 0;
        if($event_stats['total_tickets'] == '') {
            $event_stats['total_tickets'] = '0';
        }

        $currencies = getCurrencyArray();
        $currency = $currencies[$payment_settings->eventsite_currency];

        $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $event_id, 'status' => [ 'completed'], 'waiting_list' => 0], false, true);
                //Validate form stock
        $soldTickets = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids], true);
        
        $event_stats['tickets_sold'] = $soldTickets;
        
        $waiting_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $event_id, 'status' => [ 'completed'], 'waiting_list' => 1], false, true);
                //Validate form stock
        $waitingTickets = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $waiting_orders_ids], true);

        $event_stats['waiting_tickets'] = $waitingTickets;

        $event_stats['tickets_left'] = $event_stats['total_tickets'] !== 0 ? ($event_stats['total_tickets'] - $event_stats['tickets_sold']): 0;
        

        $sale_agent_orders = \App\Models\BillingOrder::where('event_id',$event_id)->where('sale_agent_id', $sales_agent_id)->where('status', '<>', 'cancelled')->where('is_waitinglist', 0)->where('is_archive','=','0')->currentActiveOrders()->with('order_attendees')->get();

        $sales_agent_stats = array('tickets_sold'=>'0','revenue'=>'0');
      

        foreach($sale_agent_orders as $order) {
            $sales_agent_stats['tickets_sold'] += count($order['order_attendees']);
            $sales_agent_stats['revenue'] += $order['reporting_panel_total'];
        }

        $sales_agent_stats['revenue_text'] = getCurrency($sales_agent_stats['revenue'],$currency).' '.$currency;

        
        
        return [
            "event_url" => $event->url,
            "event_stats" => $event_stats,
            "sales_agent_stats" => $sales_agent_stats,
            "event_location" => $event_location,
            "event_name" => $event_name,
            "event_date" => $event_date,
            "brand_logo" => $brand_logo->value,
            "payment_settings" => $payment_settings,
            "eventsite_settings" => $eventsite_settings,
            "currency" => $currency,
        ];
    }

    public function getEventOrders($event_id, $event_language_id, $sales_agent_id, $is_free = 0,$is_archived = 0,  $searchText = "", $type = 'all', $limit = 10, $sort_col = 'order_number', $sort = 'desc', $registration_form_id = 0, $waiting_list = 0)
    {
        $searchKey = '';
        $searchOperator = '!=';
        $searchField = 'conf_billing_orders.status';
        $searchValue = 'draft';

        if (trim($searchText) !== '') {
            $searchKey = '%' . $searchText . '%';
        }
        if ($type != 'all') {
            if ($type == 'completed') {
                $searchOperator = '=';
                $searchValue = 'completed';
            } elseif ($type == 'cancelled') {
                $searchOperator = '=';
                $searchValue = 'cancelled';
            } elseif ($type == 'pending') {
                $searchOperator = '=';
                $searchValue = 'pending';
            } elseif ($type == 'payment_received') {
                $searchField = 'is_payment_received';
                $searchOperator = '=';
                $searchValue = '1';
            } elseif ($type == 'payment_pending') {
                $searchField = 'is_payment_received';
                $searchOperator = '=';
                $searchValue = '0';
            }
        }

        $columns = [
            'conf_billing_orders.*',
            'conf_billing_orders.id as billing_order_id',
            'conf_billing_orders.status as billing_order_status'
        ];

        // applying registrationform filter
        $valid_order_ids = \App\Models\BillingOrder::where('event_id', $event_id)->where('is_free',$is_free)->where('is_waitinglist', $waiting_list)->where('is_archive', $is_archived)->currentActiveOrders()->pluck('id');

        if($registration_form_id != 0) {
            $valid_order_ids = \App\Models\BillingOrderAttendee::whereIn('order_id', $valid_order_ids)->where('registration_form_id', $registration_form_id)->pluck('order_id');
        }
        
        
        $result = \App\Models\BillingOrder::where('conf_billing_orders.event_id', $event_id)->whereIn('conf_billing_orders.id', $valid_order_ids)->currentActiveOrders()
        ->where('is_archive', '=', $is_archived)
        ->where($searchField, $searchOperator, $searchValue)
        ->with(['order_attendee.info', 'order_attendees', 'order_attendees.attendee_detail']);

        if(trim($searchKey) != '' && is_numeric(trim($searchText))){
            $result->where('order_number', '=', $searchText);
        }
        else if(trim($searchKey) != '' && is_string(trim($searchText))){
            $result->where(function($q) use ($searchKey) {
                $q->whereHas('order_attendee', function ($f) use ($searchKey) {
                    $f->where('id', '<>', '')
                        ->where(function ($query) use ($searchKey) {
                            $query->where('first_name', 'LIKE',  $searchKey )
                                ->orWhere('last_name', 'LIKE', $searchKey )
                                ->orWhere('email', 'LIKE', $searchKey );
                        });
                })->orWhereHas('order_attendee.info', function ($f) use ($searchKey) {
                    $f->where('id', '<>', '')
                        ->where(function ($query) use ($searchKey) {
                            $query->where('name', '=', 'company_name')->where('value', 'LIKE', $searchKey);
                        });
                });
            });
            
        }
        

        
        $result->whereNull('conf_billing_orders.deleted_at')
            ->where('language_id', '=', $event_language_id);

        if ($sales_agent_id != 0) {
            $result->where('sale_agent_id', '=', $sales_agent_id);
        }
        
        $result->select($columns);
        
        $sort_val = $sort_col;
        $sort_type = $sort;
        $sorting_columns = [
            'order_number' => 'order_number',
            'order_date' => 'order_date',
            'name' => "CONCAT(conf_attendees.first_name,conf_attendees.last_name)",
            'email' => "conf_attendees.email",
            'amount' => 'grand_total',
            'company' => "name = 'company_name'",
            'order_status' => 'status',
            'payment_status' => 'is_payment_received',
            'sold_tickets' => 'count(conf_billing_order_attendees.order_id)'
        ];
        $sort = [
            'asc' => 'asc',
            'desc' => 'desc'
        ];

        if(isset($sorting_columns[$sort_val]) && isset($sort[$sort_type])){
            if($sort_val == 'company'){
                $result->join('conf_attendees_info', 'conf_attendees_info.attendee_id', '=', 'conf_billing_orders.attendee_id')
                    ->whereIn('name', ['title', 'company_name'])
                    ->groupBy('conf_attendees_info.attendee_id')
                    ->orderBy(\DB::raw('conf_attendees_info.value'), $sort[$sort_type]);
            }else if($sort_val == 'name' || $sort_val == 'email'){
                $result->leftJoin('conf_attendees', 'conf_attendees.id', '=', 'conf_billing_orders.attendee_id')
                    ->orderBy(\DB::raw($sorting_columns[$sort_val]), $sort[$sort_type]);
            }else if($sort_val == 'sold_tickets'){
                $columns[] = \DB::raw('count(conf_billing_order_attendees.order_id)');
                $result->join('conf_billing_order_attendees', 'conf_billing_order_attendees.order_id', '=', 'conf_billing_orders.id')
                    ->groupBy('conf_billing_orders.id')
                    ->orderBy(\DB::raw($sorting_columns[$sort_val]), $sort[$sort_type]);
            }else{
                $result->orderBy($sorting_columns[$sort_val], $sort[$sort_type]);
            }
        }else{

            $result = $result->orderBy('conf_billing_orders.id', 'desc');
        }

       
        $result = $result->paginate($limit);

        return $result;
    
    }

    public function getSalesAgendEventOrders($formInput, $event_id, $sales_agent_id, $event_language_id)
    {
        
        $payment_settings = \App\Models\EventsitePaymentSetting::where('event_id', '=', $event_id)->first();
        
        $is_free= $payment_settings->eventsite_billing == 1 ? 0 : 1 ;

        $currencies = getCurrencyArray();
        $currency = $currencies[$payment_settings->eventsite_currency];
        
        $data = $this->getEventOrders($event_id, $event_language_id, $sales_agent_id, $is_free,'0', $formInput['searchText'], $formInput['type'], $formInput['limit'], $formInput['sort_col'], $formInput['sort'], $formInput['regFormId']);

        $i=0;
        foreach ($data as $row) {
            $temp = array();
            if (count($row['order_attendee']['info']) > 0) {
                foreach ($row['order_attendee']['info'] as $val) {
                    $temp[$val['name']] = $val['value'];
                }
            }
            $row['detail'] = $temp;

            unset($row['user_agent']);
            unset($row['security_key']);

            $data[$i] = $row;

            $tickets = \App\Models\BillingOrderAttendee::where('order_id','=',$row['id'])->count();
            $data[$i]['tickets_sold'] = $tickets;
            $data[$i]['grand_total_text'] = getCurrency($row['grand_total'],$currency).' '.$currency;
            $data[$i]['reporting_panel_total_text'] = getCurrency($row['reporting_panel_total'],$currency).' '.$currency;

            $i++;
        }

        return $data;
    }

    /**
     * getOrderInvoice
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $attendee_id
     * @return void
     */
    public function getOrderInvoice($formInput, $event_id, $order_id) {

        
        $event = \App\Models\Event::where('id', $event_id)->first();

        $event_language_id = $event->language_id;

        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

        // Order detail summary
        $order_detail = $EBOrder->getInvoiceSummary();

        //labels
        $labels = eventsite_labels(['eventsite', 'exportlabels'], ['event_id' => $event->id, 'language_id' => $event->language_id]);

        $event_id = $EBOrder->getOrderEventId();

        $language_id = $EBOrder->getUtility()->getLangaugeId();

        $payment_setting = $EBOrder->_getPaymentSetting();

        $billing_currency = $payment_setting['eventsite_currency'];
        
        $invoice = $this->eventsiteBillingOrderRepository->getOrderDetailInvoice("html", $EBOrder, $labels, $language_id, $event_id, $billing_currency, $order_detail['order']['id'],1, 1, true, false, 0, 0, true);
        
        return [
            'invoice' => $invoice
        ];

    }
    
    /**
     * formBasedTicketingStats
     *
     * @param  mixed $formInput
     * @param  mixed $event_id
     * @return void
     */
    public function formBasedTicketingStats($formInput, $event_id)
    {
         // get forms
         $reg_forms = \App\Models\RegistrationForm::where('event_id', $event_id)
         ->where('status', 1)
         ->with(['attendee_type'])
         ->whereNull('deleted_at')
         ->get();

        foreach ($reg_forms as $key => $form) {
            $eventsite_settings = \App\Models\EventsiteSetting::select(['ticket_left', 'registration_end_date', 'registration_end_time'])->where('event_id', $event_id)->where('registration_form_id', $form->id)->first();

            $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $event_id, 'status' => ['completed'], 'waiting_list' => 0], false, true);
            //Validate form stock
            $tickets_sold = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => $form->id], true);
            
            $active_waiting_list_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $event_id, 'status' => ['completed'], 'waiting_list' => 1], false, true);
            $waiting_attendees_count = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_waiting_list_orders_ids, 'registration_form_id' => $form->id], true);
            
            $reg_forms[$key]['eventsite_settings'] = $eventsite_settings;
            $reg_forms[$key]['total_tickets'] = $eventsite_settings->ticket_left;

            $total_tickets = $eventsite_settings->ticket_left === '' ? 0 : (int) $eventsite_settings->ticket_left;

            $reg_forms[$key]['waiting_attendees_count'] = $waiting_attendees_count;
            $reg_forms[$key]['total_tickets'] = $total_tickets;
            $reg_forms[$key]['tickets_sold'] = $tickets_sold;
            $reg_forms[$key]['tickets_left'] = $total_tickets -  (int) $tickets_sold > 0  ? $total_tickets -  (int) $tickets_sold : 0;
        }

        return $reg_forms;
    }

}
