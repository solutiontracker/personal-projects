<?php 
namespace App\Exports\Order;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Http\Request;

class OrdersExport implements WithMultipleSheets
{
    use Exportable;

    protected $eventId;

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
        $this->eventId = $request->event_id;
        $this->languageId = $request->language_id; 
        $this->secLabels = $this->billingSections($this->eventId, $this->languageId); 
        $this->regLabels = $this->billingFields($this->eventId, $this->languageId); 
        $this->type = $type; 
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $is_archived = 0;
        $formInput = $this->request->all();
        $setting = \App\Models\EventsitePaymentSetting::where('event_id', $formInput['event_id'])->where('registration_form_id', 0)->first();
        $is_free = $setting->eventsite_billing == 1 ? 0 : 1;
        $searchKey = '';
        $searchOperator = '<>';
        $searchField = 'status';
        $searchValue = 'null';
        $searchKey = $formInput['query'] != '' ? $searchKey = $formInput['query'] : '';
        if ($formInput['type'] != 'all') {
            if ($formInput['type'] == 'completed') {
                $searchOperator = '=';
                $searchValue = 'completed';
            } elseif ($formInput['type'] == 'cancelled' || $formInput['type'] == 'cancelled_without_creditnote') {
                $searchOperator = '=';
                $searchValue = $formInput['type'];
            } elseif ($formInput['type'] == 'pending') {
                $searchOperator = '=';
                $searchValue = 'pending';
            }
        }

        $valid_order_ids = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->where('is_free', $is_free)->where('is_archive', $is_archived)->currentOrder()->pluck('id');

        if (trim($searchKey) != '') {
            if (!is_numeric(trim($formInput['query']))) {
                $result = \App\Models\BillingOrder::join('conf_attendees', function ($join) {
                    $join->on('conf_attendees.id', '=', 'conf_billing_orders.attendee_id');
                })
                    ->join('conf_attendees_info AS b_title', function ($join) use ($formInput) {
                        $join->on('conf_attendees.id', '=', 'b_title.attendee_id')
                            ->where('b_title.languages_id', '=', $formInput['language_id']);
                    })
                    ->join('conf_attendees_info AS b_company', function ($join) use ($formInput) {
                        $join->on('conf_attendees.id', '=', 'b_company.attendee_id')
                            ->where('b_company.languages_id', '=', $formInput['language_id']);
                    })->leftJoin('conf_billing_order_attendees', function ($join) {
                    $join->on('conf_billing_orders.id', '=', 'conf_billing_order_attendees.order_id');
                })
                    ->join('conf_attendees AS additional', function ($join) {
                        $join->on('additional.id', '=', 'conf_billing_order_attendees.attendee_id');
                    })->where(function ($query) use ($searchKey) {
                    $query->where(\DB::raw('CONCAT(conf_attendees.first_name, " ", conf_attendees.last_name)'), 'LIKE', '%' . trim($searchKey) . '%')
                        ->orWhere('conf_attendees.email', 'LIKE', '%' . trim($searchKey) . '%')
                        ->orWhere('b_title.value', 'LIKE', '%' . trim($searchKey) . '%')
                        ->orWhere('b_company.value', 'LIKE', '%' . trim($searchKey) . '%')
                        ->orWhere('additional.email', 'LIKE', '%' . trim($searchKey) . '%')
                        ->orWhere(\DB::raw('CONCAT(additional.first_name, " ", additional.last_name)'), 'LIKE', '%' . trim($searchKey) . '%');
                })
                    ->where('conf_billing_orders.event_id', '=', $formInput['event_id'])
                    ->where('conf_billing_orders.is_free', '=', $is_free)
                    ->whereIn('conf_billing_orders.id', $valid_order_ids)
                    //->where('conf_billing_orders.is_waitinglist', '=', '0')
                    ->where('conf_billing_orders.is_archive', '=', $is_archived)
                    ->where('conf_billing_orders.language_id', '=', $formInput['language_id'])
                    ->where('b_title.name', '=', 'title')
                    ->where('b_company.name', '=', 'company_name')
                    ->select(array('conf_billing_orders.id as billing_order_id', 'conf_attendees.*', 'b_title.value as title', 'b_company.value as company', 'conf_billing_order_attendees.*'))
                    ->get();

                $temp_order_id = array();
                foreach ($result as $row) {
                    $temp_order_id[] = $row->billing_order_id;
                }
            } else {
                $temp_order_id = $valid_order_ids;
            }
        }

        $query = \App\Models\BillingOrder::join("conf_attendees", "conf_attendees.id", "=", "conf_billing_orders.attendee_id")
            ->join("conf_billing_order_attendees", "conf_billing_order_attendees.order_id", "=", "conf_billing_orders.id")
            ->where('conf_billing_orders.event_id', '=', $formInput['event_id'])
            ->where('conf_billing_orders.is_archive', '=', $is_archived)
            ->where('conf_billing_orders.is_free', '=', $is_free)
            //->where('conf_billing_orders.is_waitinglist', '=', '0')
            ->with(['order_attendee.info' => function ($query) use ($formInput) {
                return $query->where('languages_id', '=', $formInput['language_id']);
            }, 'order_attendees', 'order_addons']);

        //Filter date range
        if (isset($formInput['fromDate']) && $formInput['fromDate']) {
            $query->whereDate('conf_billing_orders.created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
        }
        if (isset($formInput['toDate']) && $formInput['toDate']) {
            $query->whereDate('conf_billing_orders.created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
        }

        if (trim($searchKey) != '') {
            if (is_numeric(trim($formInput['query']))) {
                $query->whereIn('conf_billing_orders.id', $valid_order_ids)
                    ->where('conf_billing_orders.order_number', '=', $formInput['query']);
            } else {
                if ($searchValue == 'cancelled_without_creditnote') {
                    $query->whereIn('conf_billing_orders.id', $temp_order_id)
                        ->where('conf_billing_orders.is_cancelled_wcn', 1)
                        ->where("conf_billing_orders." . $searchField, $searchOperator, 'cancelled');
                } else {
                    $query->whereIn('conf_billing_orders.id', $temp_order_id)
                        ->where("conf_billing_orders." . $searchField, $searchOperator, $searchValue);
                }
            }
        } else {
            if ($searchValue == 'cancelled_without_creditnote') {
                $query->whereIn('conf_billing_orders.id', $valid_order_ids)
                    ->where('conf_billing_orders.is_cancelled_wcn', 1)
                    ->where("conf_billing_orders." . $searchField, $searchOperator, 'cancelled');
            } else {
                $query->whereIn('conf_billing_orders.id', $valid_order_ids)
                    ->where("conf_billing_orders." . $searchField, $searchOperator, $searchValue);
            }
        }

        if ($formInput['payment_status'] == 'payment_received') {
            $query->where("conf_billing_orders.is_payment_received", "=", '1');
        } elseif ($formInput['payment_status'] == 'payment_pending') {
            $query->where("conf_billing_orders.is_payment_received", "=", '0');
        }

        if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && in_array($formInput['sort_by'], ['first_name', 'email']))) {
            $query->orderBy("conf_attendees." . $formInput['sort_by'], $formInput['order_by']);
        } else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && in_array($formInput['sort_by'], ['order_number', 'order_date', 'grand_total', 'status']))) {
            $query->orderBy("conf_billing_orders." . $formInput['sort_by'], $formInput['order_by']);
        } else {
            $query->orderBy('conf_billing_orders.order_number', 'DESC');
        }

        $query->groupBy('conf_billing_orders.id', 'conf_billing_order_attendees.attendee_id');

        $query->select('conf_billing_orders.*', 'conf_billing_order_attendees.attendee_id as main_attendee_id', 'conf_billing_orders.attendee_id as order_attendee_id');

        $results = $query->get()->toArray();

        $sheets = [];
        if($this->type == 'order-list') {
            $sheets[] = new OrdersList($this->request, $results, $this->secLabels, $this->regLabels);
            $sheets[] = new LineItems($this->request, $results, $this->secLabels, $this->regLabels);
            $allOrders = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->get();
            $sheets[] = new OrderHistory($this->request, $allOrders, $this->secLabels, $this->regLabels);
        } else {
            $sheets[] = new ExportSingle($this->request, $results, $this->secLabels, $this->regLabels);
        }
        return $sheets;
    }

    /**
     * @param mixed $event_id
     * @param mixed $language_id
     * 
     * @return [type]
     */
    public function billingSections($event_id, $language_id)
    {
        $labels = \App\Models\BillingField::where('event_id', '=', $event_id)->where('type', '=', 'section')->with(['info' => function ($query) use ($language_id) {
            return $query->where('languages_id', '=', $language_id);
        }])->orderBy('sort_order', 'asc')->get()->toArray();
        $sectionLabels = [];
        foreach ($labels as $label) {
            if (count($label['info'] ?? []) > 0) {
                $sectionLabels[$label['field_alias']] = $label['info'][0]['value'];
            }
        }
        return $sectionLabels;
    }

    /**
     * @param mixed $event_id
     * @param mixed $language_id
     * 
     * @return [type]
     */
    public function billingFields($event_id, $language_id)
    {
        $labels = \App\Models\BillingField::where('event_id', '=', $event_id)->where('type', '=', 'field')
            ->whereIN('section_alias', ['basic', 'membership', 'company_detail', 'address_private', 'attendee_type_head', 'attendee_type', 'po_number'])->with(['info' => function ($query) use ($language_id) {
            return $query->where('languages_id', '=', $language_id);
        },
        ])->orderBy('sort_order', 'asc')->get()->toArray();
        $regLabels = [];
        foreach ($labels as $label) {
            if (count($label['info'] ?? []) > 0) {
                $regLabels[$label['field_alias']] = $label['info'][0]['value'];
            }
        }
        return $regLabels;
    }
}
