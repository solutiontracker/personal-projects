<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Super\Requests\AgentEventsRequest;
use App\Http\Helpers\HttpHelper;
use App\Eventbuizz\Repositories\Sales\AgentEventRepository;
use Illuminate\Http\Request;
use App\Eventbuizz\Repositories\Sales\SaleAgentRepository;

class AgentEventsController extends Controller
{


    protected $repository;
    protected $saleAgentRepository;
    protected $saleAgent;


    public function __construct(AgentEventRepository $repository, SaleAgentRepository $saleAgentRepository)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->saleAgentRepository = $saleAgentRepository;

    }


    public function index(AgentEventsRequest $request) {
        try {
            $saleAgent = $request->user();
            $agentEventsResponse = $this->repository->getAgentEvents($saleAgent->id);
            if (!$agentEventsResponse['success']) {
                return HttpHelper::errorJsonResponse($agentEventsResponse['message'], $agentEventsResponse['title']);
            }

            $agentWithEvents = $agentEventsResponse['data'];
            //  $events = $agentEventsResponse['data']['agentEvents'][0]['events'];
            return HttpHelper::successJsonResponse('Events data', '', $agentWithEvents);
        } catch (\Exception $e) {
            return HttpHelper::exceptionJsonResponse($e);
        }
    }
    
    public function eventData(Request $request, $event_id) {
        try {
            $saleAgent = $request->user();

            $event_id_assigned = \App\Models\EventSaleAgent::where('event_id', $event_id)->where('sale_agent_id', $saleAgent->id)->whereNull('deleted_at')->first();

            if(!$event_id_assigned){
                return HttpHelper::errorJsonResponse('Event not assigned', 'Invalid event_id');
            }

            $getEventWithOrders = $this->repository->getEventWithOrders($request->all(), $event_id, $saleAgent->id);

            return HttpHelper::successJsonResponse('Events orders', '', $getEventWithOrders);
        } catch (\Exception $e) {
            return HttpHelper::exceptionJsonResponse($e);
        }
    }
    
    public function eventOrders(Request $request, $event_id) {
        try {
            $saleAgent = $request->user();

            $event_id_assigned = \App\Models\EventSaleAgent::where('event_id', $event_id)->where('sale_agent_id', $saleAgent->id)->whereNull('deleted_at')->first();

            if(!$event_id_assigned){
                return HttpHelper::errorJsonResponse('Event not assigned', 'Invalid event_id');
            }

            $event = \App\Models\Event::where('id', $event_id)->first();

            $event_language_id = $event->language_id;

            $request->merge(['language_id'=>$event_language_id]);

            $getEventWithOrders = $this->repository->getSalesAgendEventOrders($request->all(), $event_id, $saleAgent->id, $event_language_id);

            return HttpHelper::successJsonResponse('Events orders', '', $getEventWithOrders);
        } catch (\Exception $e) {
            return HttpHelper::exceptionJsonResponse($e);
        }
    }
    
    public function orderInvoice(Request $request, $event_id, $order_id) {
        try {
            $saleAgent = $request->user();

            $event_id_assigned = \App\Models\EventSaleAgent::where('event_id', $event_id)->where('sale_agent_id', $saleAgent->id)->whereNull('deleted_at')->first();

            if(!$event_id_assigned){
                return HttpHelper::errorJsonResponse('Event not assigned', 'Invalid event_id');
            }

            $orderInvoice = $this->repository->getOrderInvoice($request->all(), $event_id, $order_id);

            return HttpHelper::successJsonResponse('Events orders', '', $orderInvoice);
        } catch (\Exception $e) {
            return HttpHelper::exceptionJsonResponse($e);
        }
    }

    public function getFormBasedTicketingStats(Request $request, $event_id)
    {
       $reg_forms = $this->repository->formBasedTicketingStats($request->all(), $event_id);
        
        // get forms stats
        return response()->json([
            "success" => true,
            "message" => "Form data retrieved successfully.",
            "data" => $reg_forms
        ], 200);
    }

}
