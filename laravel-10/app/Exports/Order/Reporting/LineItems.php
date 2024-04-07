<?php
namespace App\Exports\Order\Reporting;

use App\Models\AttendeeBilling;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LineItems implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    protected $eventId;
    protected $languageId;
    protected $request;
    protected $results;
    protected $secLabels;
    protected $regLabels;

    /**
     * @param Request $request
     * @param mixed $results
     * @param mixed $secLabels
     * @param mixed $regLabels
     */
    public function __construct(Request $request, $results)
    {
        $this->request = $request->all();
        $this->eventId = $request->event_id ?? null;
        $this->languageId = $request->language_id ?? null; 
        $this->results = $results;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:BP1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

    /**
     * @return [type]
     */
    public function collection()
    {
        $array = array();

        $key = 0;

        foreach ($this->results as $row) {

            $event_id = $row['event_id'];
            $event = \App\Models\Event::where('id', $event_id)->first();
            $language_id = $event->language_id;

            $currency = '';
            if (trim($row['order_number'])) {
                $order_number = $row['order_number'];
            } else {
                $order_number = $row['id'];
            }

            $billing_currency = getCurrencyArray();
            foreach ($billing_currency as $i => $cur) {
                if ($row['eventsite_currency'] == $i) {
                    $currency = $cur;
                }
            }

            $attendee = \App\Models\Attendee::where('id', $row['main_attendee_id'])->with(['detail' => function ($query) use($language_id) {
                return $query->where('languages_id', $language_id);
            }])->first()->toArray();
            $info = readArrayKey($attendee, [], 'detail');
            $attendee = array_merge($attendee, $info);
            $billing_address = \App\Models\AttendeeBilling::where('attendee_id', $row['main_attendee_id'])->where("event_id", $event_id)->orderBy('id', 'DESC')->first();

            $voucher_result = \App\Models\BillingVoucher::where('event_id', $event_id)->where('code', $row['code'])->first();

            $row['event_qty'] += (int) \App\Models\Attendee::join('conf_billing_order_attendees', 'conf_billing_order_attendees.attendee_id', '=', 'conf_attendees.id')->where('conf_billing_order_attendees.order_id', $row['id'])->where('conf_billing_order_attendees.attendee_id', '!=', $row['main_attendee_id'])->sum('conf_billing_order_attendees.event_qty');

            $event_items = \App\Models\BillingOrderAddon::join('conf_billing_items', 'conf_billing_items.id', '=', 'conf_billing_order_addons.addon_id')->where('conf_billing_order_addons.attendee_id', $row['main_attendee_id'])
            ->where('conf_billing_order_addons.order_id', $row['id'])
            ->select('conf_billing_order_addons.*', 'conf_billing_items.group_id AS has_group')->get();
            
            foreach($event_items as $item) {
                if ($voucher_result->type == 'billing_items') {
                    $addon_code = $row['code'];
                } else {
                    $addon_code = '';
                }

                if (($item['link_to']) && ($item['link_to'] <> 'none')) {
                    $name = getBillingItemName($item, $language_id);
                } else {
                    if (trim($item['name']) == '') {
                        $billing_item = \App\Models\BillingItem::where('id', $item['addon_id'])->with(['info' => function ($query) use($language_id) {
                            return $query->where('languages_id', $language_id);
                        }])->first()->toArray();
                        $info = readArrayKey($billing_item, [], 'info');
                        $billing_item = array_merge($billing_item, $info);
                        $name = stripslashes($billing_item['item_name']);
                    } else {
                        $name = stripslashes($item['name']);
                    }
                }

                $group_name = '';
                if ($item['has_group']) {
                    $billingItemGroup =  \App\Models\BillingItem::where('id', $item['has_group'])->with(['info' => function ($query) use($language_id) {
                        return $query->where('languages_id', $language_id);
                    }])->first();
                    $group_name = $billingItemGroup->info[0]['value'];
                }

                if ($row['is_waitinglist'] == '1') {
                    $status = 'Waiting';
                } else {
                    $status = $row['status'];
                }

                $array[$key]['event_code'] = $event_id;
                $array[$key]['event_name'] = $event->name;
                $array[$key]['order_number'] = $order_number;
                $array[$key]['order_date'] = date('Y-m-d', strtotime($row['order_date']));
                $array[$key]['order_time'] = date('H.i.s', strtotime($row['order_date']));
                $array[$key]['first_name'] = $attendee['first_name'];
                $array[$key]['last_name'] = $attendee['last_name'];
                $array[$key]['email'] = $attendee['email'];
                $array[$key]['company_name'] = $attendee['company_name'];
                $array[$key]['main_item'] = $name;
                $array[$key]['group_name'] = $group_name;
                
                $array[$key]['item'] = $name;
                $array[$key]['qty'] = $item['qty'];
                $array[$key]['currency'] = $currency;
                $array[$key]['price'] = $item['price'];
                $array[$key]['discount'] = $item['discount'];
                $array[$key]['discount_item'] = (($item['price'] * $item['qty']) - $item['discount']);
                $array[$key]['discount_code'] = $addon_code;
                $array[$key]['status'] = $status;
                $key++;
            }
        }

        return collect($array);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            "Event code",
            "Event name",
            "Order Number",
            "Date",
            "Time",
            "First name",
            "Last name",
            "Email",
            "Company",
            "Main item",
            // new column added by irfan
            "Group",

            "Item",
            "Quantity",
            "Currency",
            "Amount excl. VAT",
            "Discount Line Item",
            "Total excl. VAT",
            "Discount code",
            "Status",
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Line items';
    }
}
