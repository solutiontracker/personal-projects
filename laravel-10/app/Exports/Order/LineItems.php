<?php
namespace App\Exports\Order;

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
    public function __construct(Request $request, $results, $secLabels, $regLabels)
    {
        $this->request = $request->all();
        $this->eventId = $request->event_id;
        $this->languageId = $request->language_id; 
        $this->secLabels = $secLabels; 
        $this->regLabels = $regLabels; 
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

            $attendee = \App\Models\Attendee::where('id', $row['main_attendee_id'])->with(['info' => function ($query) {
                return $query->where('languages_id', $this->languageId);
            }])->first()->toArray();
            $info = readArrayKey($attendee, [], 'info');
            $attendee = array_merge($attendee, $info);
            $billing_address = \App\Models\AttendeeBilling::where('attendee_id', $row['main_attendee_id'])->where("event_id", $this->eventId)->orderBy('id', 'DESC')->first();

            $voucher_result = \App\Models\BillingVoucher::where('event_id', $this->eventId)->where('code', $row['code'])->first();

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
                    $name = getBillingItemName($item, $this->languageId);
                } else {
                    if (trim($item['name']) == '') {
                        $billing_item = \App\Models\BillingItem::where('id', $item['addon_id'])->with(['info' => function ($query) {
                            return $query->where('languages_id', $this->languageId);
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
                    $billingItemGroup =  \App\Models\BillingItem::where('id', $item['has_group'])->with(['info' => function ($query) {
                        return $query->where('languages_id', $this->languageId);
                    }])->first();
                    $group_name = $billingItemGroup->info[0]['value'];
                }

                if ($row['is_waitinglist'] == '1') {
                    $status = 'Waiting';
                } else {
                    $status = $row['status'];
                }

                $array[$key]['order_number'] = $order_number;
                $array[$key]['order_date'] = date('Y-m-d', strtotime($row['order_date']));
                $array[$key]['order_time'] = date('H.i.s', strtotime($row['order_date']));
                $array[$key]['first_name'] = $row['order_attendee']['first_name'];
                $array[$key]['group_name'] = $group_name;
                $array[$key]['name'] = $name;
                $array[$key]['qty'] = $item['qty'];
                $array[$key]['currency'] = $currency;
                $array[$key]['price'] = $item['price'];
                $array[$key]['discount'] = $item['discount'];
                $array[$key]['discount_item'] = (($item['price'] * $item['qty']) - $item['discount']);
                $array[$key]['discount_code'] = $addon_code;
                $array[$key]['status'] = $status;
                $array[$key]['billing_company_registration_number'] = $billing_address['billing_company_registration_number'];
                $array[$key]['billing_company_street'] = $billing_address['billing_company_street'];
                $array[$key]['billing_company_house_number'] = $billing_address['billing_company_house_number'];
                $array[$key]['billing_company_post_code'] = $billing_address['billing_company_post_code'];
                $array[$key]['billing_company_city'] = $billing_address['billing_company_city'];
                $array[$key]['billing_company_country'] = getCountryName($billing_address['billing_company_country']);
                $array[$key]['company_name'] = $attendee['company_name'];
                $array[$key]['jobs'] = $attendee['jobs'];
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
            'Order Number',
            'Date',
            'Time',
            $this->regLabels['first_name'],
            $this->regLabels['last_name'],
            $this->regLabels['email'],
            $this->regLabels['phone'],
            "Group",
            "Item",
            "Quantity",
            "Currency",
            "Amount excl. VAT",
            "Discount Line Item",
            "Total excl. VAT",
            "Discount code",
            "Status",
            $this->regLabels['company_registration_number'],
            $this->regLabels['company_street'],
            $this->regLabels['company_house_number'],
            $this->regLabels['company_post_code'],
            $this->regLabels['company_city'],
            $this->regLabels['company_country'],
            "Company",
            $this->regLabels['jobs'],
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
