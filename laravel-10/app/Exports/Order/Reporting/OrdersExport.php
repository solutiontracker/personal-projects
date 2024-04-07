<?php 
namespace App\Exports\Order\Reporting;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Http\Request;

class OrdersExport implements WithMultipleSheets
{
    use Exportable;

    protected $eventId;

    protected $orderIds;

    protected $languageId;

    protected $request;

    protected $secLabels;

    protected $regLabels;

    protected $type;
    
    /**
     * @param Request $request
     */
    public function __construct(Request $request, $type)
    {
        $this->request = $request;
        $this->eventId = $request->event_id ?? null;
        $this->orderIds = $request->order_ids ?? [];
        $this->languageId = $request->language_id ?? null; 
        $this->type = $type; 
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $is_archived = 0;
        $formInput = $this->request->all();

        $valid_order_ids = \App\Models\BillingOrder::whereIn('id', $this->orderIds)->where('is_archive', $is_archived)->currentActiveOrders()->pluck('id');
 
        $query = \App\Models\BillingOrder::join("conf_attendees", "conf_attendees.id", "=", "conf_billing_orders.attendee_id")
            ->join("conf_billing_order_attendees", "conf_billing_order_attendees.order_id", "=", "conf_billing_orders.id")
            ->whereIn('conf_billing_orders.id',  $valid_order_ids)
            ->where('conf_billing_orders.is_archive', '=', $is_archived);
            // ->with(['order_attendee.info' => function ($query) use ($formInput) {
            //     return $query->where('languages_id', '=', $formInput['language_id']);
            // }, 'order_attendees', 'order_addons']);

        $query->orderBy('conf_billing_orders.order_number', 'DESC');
 
        $query->groupBy('conf_billing_orders.id', 'conf_billing_order_attendees.attendee_id');

        $query->select('conf_billing_orders.*', 'conf_billing_order_attendees.attendee_id as main_attendee_id', 'conf_billing_orders.attendee_id as order_attendee_id');

        $results = $query->get()->toArray();

        $sheets = [];
        
        $sheets[] = new OrdersList($this->request, $results);
        $sheets[] = new LineItems($this->request, $results);
        return $sheets;
    }

}
