<?php

namespace App\Http\Controllers\Reporting;

use App\Eventbuizz\Repositories\ReportingAgentRepository;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GrahamCampbell\ResultType\Success;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Order\Reporting\OrdersExport;

class ReportingAgentController extends Controller
{
    public $successStatus = 200;

    protected $reportingAgentRepository;

    public function __construct(ReportingAgentRepository $reportingAgentRepository)
    {
        $this->reportingAgentRepository = $reportingAgentRepository;
    }
        
    /**
     * getReportingAgentEvents
     *
     * @param  mixed $request
     * @return void
     */
    public function getReportingAgentfilters(Request $request)
    {   $user = $request->user();
        $organizer_id = $user->organizer_id;
        $event_countries = $this->reportingAgentRepository->getOrganizerEventCountries($organizer_id);
        $office_countries = $this->reportingAgentRepository->getOrganizerOfficeCountries($organizer_id);
        $currencies = $this->reportingAgentRepository->getOrganizerEventCurrencies($organizer_id);
        return response()->json([
            'success' => 1,
            'message' => 'data retrieved successfully',
            'data' => [
                'event_countries' => $event_countries,
                'office_countries' => $office_countries,
                'currencies' => $currencies,
            ]
        ], 200);
    }
    
    
    /**
     * getReportingAgentEvents
     *
     * @param  mixed $request
     * @return void
     */
    public function getReportingAgentEvents(Request $request)
    {   $user = $request->user();
        $data = $this->reportingAgentRepository->getReportingAgentEvents($request->all(), $user);
        return response()->json([
            'success' => 1,
            'message' => 'data retrieved successfully',
            'data' => [
                "events" => $data,
            ],
        ], 200);
    }
        
    /**
     * agentEventStatsAndOrders
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @return void
     */
    public function agentEventStatsAndOrders(Request $request, $event_id)
    {   $user = $request->user();
        $data = $this->reportingAgentRepository->agentEventStatsAndOrders($request->all(),  $user, $event_id);
        return response()->json([
            'success' => 1,
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
    
    /**
     * reportingAgentExportOrders
     *
     * @param  mixed $request
     * @return void
     */
    public function reportingAgentExportOrders(Request $request)
    {   
        $user = $request->user();
        $events = $this->reportingAgentRepository->getReportingAgentEvents($request->all(), $user);
        $event_id_array=[];
        foreach($events['data'] as $event) {
            $event_id_array[] = $event['id'];
        }
        $order_ids_string=[];
        $orders_range = $this->reportingAgentRepository->getRangeOrders($request->all(),$event_id_array,1);
        foreach($orders_range as $order) {
            $order_ids_string[] = $order['id'];
        }
        $request->merge(['order_ids'=> $order_ids_string]);
        return Excel::download(new OrdersExport($request, 'order-list'), 'orders.xlsx');
    }
    
        
    /**
     * reportingAgentExportEventOrders
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @return void
     */
    public function reportingAgentExportEventOrders(Request $request, $event_id)
    {   
        $order_ids_string=[];
        $orders_range = $this->reportingAgentRepository->getRangeOrders($request->all(),[$event_id],1);
        foreach($orders_range as $order) {
            $order_ids_string[] = $order['id'];
        }
        $request->merge(['order_ids'=> $order_ids_string]);
        return Excel::download(new OrdersExport($request, 'order-list'), 'orders.xlsx');
    }
    
    /**
     * getFormBasedTicketingStats
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @return void
     */
    public function getFormBasedTicketingStats(Request $request, $event_id)
    {
        // get forms
        $reg_forms = \App\Models\RegistrationForm::where('event_id', $event_id)
                    ->where('status', 1)
                    ->with(['attendee_type'])
                    ->whereNull('deleted_at')
                    ->get();

        $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $event_id, 'status' => ['completed'], 'waiting_list' => 0], false, true);

        $waiting_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $event_id, 'status' => ['completed'], 'waiting_list' => 1]);

        $waiting_attendee = [];
        $waiting_attendee_orders=[];
            foreach ($waiting_orders_ids as $key => $order) {
                $tickets_waiting = \App\Models\WaitingListAttendee::where('event_id', $event_id)->where('attendee_id', $order->attendee_id)->whereNotIn('status',  [3,4])->where('type', 1)->whereNull('deleted_at')->count();
                if($tickets_waiting > 0){
                    $waiting_attendee[] = $order->attendee_id;
                    $waiting_attendee_orders[] = $order->id;
                }
        }
        
        foreach ($reg_forms as $key => $form) {
            $eventsite_settings = \App\Models\EventsiteSetting::select(['ticket_left', 'registration_end_date', 'registration_end_time'])->where('event_id', $event_id)->where('registration_form_id', $form->id)->first();
                //Validate form stock
            $tickets_sold = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => $form->id], true);

            $waiting_count = \App\Models\BillingOrderAttendee::whereIn('order_id', $waiting_attendee_orders)->where('registration_form_id', $form->id)->count();
                        
            
            //Validate form stock
        
            $reg_forms[$key]['eventsite_settings'] = $eventsite_settings;
            $reg_forms[$key]['total_tickets'] = $eventsite_settings->ticket_left;

            $total_tickets = $eventsite_settings->ticket_left === '' ? 0 : (int) $eventsite_settings->ticket_left;

            $reg_forms[$key]['total_tickets'] = $total_tickets;
            $reg_forms[$key]['tickets_sold'] = (int) $tickets_sold;
            $reg_forms[$key]['tickets_left'] = $total_tickets - (int) $tickets_sold > 0  ? $total_tickets - (int) $tickets_sold : 0;
            $reg_forms[$key]['tickets_waiting'] = $waiting_count;
        }
        

        // get forms stats
        return response()->json([
            "success" => true,
            "message" => "Form data retrieved successfully.",
            "data" => $reg_forms
        ], 200);
    }

}
