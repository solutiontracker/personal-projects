<?php
namespace App\Exports\Order;

use App\Models\AttendeeBilling;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class OrdersList implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
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

        foreach ($this->results as $key => $row) {
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

            $invoice_number = html_entity_decode($row['invoice_reference_no']);
            $attendee = \App\Models\Attendee::where('id', $row['main_attendee_id'])->with(['info' => function ($query) {
                return $query->where('languages_id', $this->languageId);
            }])->first()->toArray();
            $info = readArrayKey($attendee, [], 'info');
            $attendee = array_merge($attendee, $info);
            $billing_address = \App\Models\AttendeeBilling::where('attendee_id', $row['main_attendee_id'])->where("event_id", $this->eventId)->orderBy('id', 'DESC')->first();

            if ($billing_address->billing_membership == 1) {
                $membership = 'Yes';
            } else {
                $membership = 'No';
            }

            $invoice = '';
            if ($billing_address->billing_company_type == 'invoice') {
                $invoice = 'x';
            }

            $is_main_attendee = \App\Models\BillingOrder::where('attendee_id', $row['main_attendee_id'])->where('event_id', $this->eventId)->where('is_archive', 0)->count();
            if ($is_main_attendee > 0) {
                $parentAttendee = '';
                $grand_total = $row['grand_total'];
                $vat_amount = $row['vat_amount'];
                $discount_amount = $row['discount_amount'];
                $code = $row['code'];
                if ($row['is_waitinglist'] == '1') {
                    $status = 'Waiting';
                } elseif ($row['is_cancelled_wcn'] == '1') {
                    $status = 'Cancelled without creditnote';
                } else {
                    $status = $row['status'];
                }
                $cancel_note = $row['comments'];
                $Paymentstatus = '';
                if ($row['is_payment_received'] == 1) {
                    $Paymentstatus = "Received";
                } else {
                    $Paymentstatus = "Pending";
                }
            } else {
                $parentAttendee = 'X';
                $currency = '';
                $grand_total = '';
                $vat_amount = '';
                $discount_amount = '';
                $code = '';
                //$status = '';
            }

            if ($row['status'] != 'cancelled') {
                $cancel_note = '';
            }

            $voucher_result = \App\Models\BillingVoucher::where('event_id', $this->eventId)->where('code', $code)->first();
            if ($voucher_result->type == 'billing_items') {
                $code = '';
            }

            if (!empty($row['sale_type'])) {
                $sale_type = \App\Models\SaleType::find($row['sale_type']);
                if ($sale_type) {
                    $sales_type = $sale_type->name;
                    $sales_code = $sale_type->code;
                }
            }
            if ($row['status'] == 'cancelled') {
                $sales_code = 'A03';
            }

            if ($row['status'] == 'completed' && $sales_code == "") {
                $sales_code = 'A01';
            }

            $summary_sub_total = $row['summary_sub_total'];
            $reporting_panel_total = $row['summary_sub_total'];
            $sub_total_with_discount = (float)$summary_sub_total - (float)$discount_amount;
            if ($sub_total_with_discount < 0) {
                $sub_total_with_discount = 0;
            }
            $grand_total_final = $grand_total; // - $discount_amount;
            if ($grand_total_final < 0) {
                $grand_total_final = 0;
            }

            $event_attendees = \App\Models\EventAttendee::where('event_id', $this->eventId)->where('attendee_id', '=', $row['main_attendee_id'])->first();
            if ($event_attendees->gdpr == '1') {
                $gdpr = 'Yes';
            } else {
                $gdpr = 'No';
            }

            $array[$key]['order_number'] = $order_number;
            $array[$key]['transaction_id'] = $row['transaction_id'];
            $array[$key]['order_date'] = date('Y-m-d', strtotime($row['order_date']));
            $array[$key]['order_time'] = date('H.i.s', strtotime($row['order_date']));
            $array[$key]['membership'] = $membership;
            $array[$key]['member_number'] = $billing_address->billing_member_number;
            $array[$key]['additional_attendee'] = $parentAttendee;
            $array[$key]['title'] = $attendee['title'];
            $array[$key]['initial'] = $attendee['initial'];
            $array[$key]['first_name'] = $attendee['first_name'];
            $array[$key]['last_name'] = $attendee['last_name'];
            $array[$key]['email'] = $attendee['email'];
            $array[$key]['phone'] = $attendee['phone'];
            $array[$key]['FIRST_NAME_PASSPORT'] = $attendee['FIRST_NAME_PASSPORT'];
            $array[$key]['LAST_NAME_PASSPORT'] = $attendee['LAST_NAME_PASSPORT'];
            $array[$key]['BIRTHDAY_YEAR'] = $attendee['BIRTHDAY_YEAR'];
            $array[$key]['place_of_birth'] = $attendee['place_of_birth'];
            $array[$key]['passport_no'] = $attendee['passport_no'];
            $array[$key]['date_of_issue_passport'] = ($attendee['date_of_issue_passport'] != '' ? date('Y-m-d', strtotime($attendee['date_of_issue_passport'])) : '');
            $array[$key]['date_of_expiry_passport'] = ($attendee['date_of_expiry_passport'] != '' ? date('Y-m-d', strtotime($attendee['date_of_expiry_passport'])) : '');
            $array[$key]['SPOKEN_LANGUAGE'] = $attendee['SPOKEN_LANGUAGE'];
            $array[$key]['EMPLOYMENT_DATE'] = $attendee['EMPLOYMENT_DATE'];
            $array[$key]['fik_number'] = $invoice_number;
            $array[$key]['invoice_payment'] = $invoice;
            $array[$key]['billing_ean'] = $billing_address->billing_ean;
            $array[$key]['company_name'] = $attendee['company_name'];
            $array[$key]['company_key'] = $attendee['company_key'];
            $array[$key]['department'] = $attendee['department'];
            $array[$key]['table_number'] = $attendee['table_number'];
            $array[$key]['organization'] = $attendee['organization'];
            $array[$key]['delegate_number'] = $attendee['delegate_number'];
            $array[$key]['network_group'] = $attendee['network_group'];
            $array[$key]['age'] = $attendee['age'];
            $array[$key]['gender'] = $attendee['gender'];
            $array[$key]['jobs'] = $attendee['jobs'];
            $array[$key]['interests'] = $attendee['interests'];
            $array[$key]['industry'] = $attendee['industry'];
            $array[$key]['about'] = $attendee['about'];
            $array[$key]['private_address'] = (($attendee['private_street']) ? $attendee['private_street'] . ' ' : '') . (($attendee['private_house_number']) ? $attendee['private_house_number'] . ' ' : '');
            $array[$key]['private_post_code'] = (($attendee['private_post_code']) ? $attendee['private_post_code'] . ' ' : '');
            $array[$key]['private_city'] = (($attendee['private_city']) ? $attendee['private_city'] . ' ' : '');
            $array[$key]['private_country'] = (($attendee['private_country']) ? getCountryName($attendee['private_country']) . '' : '');
            $array[$key]['billing_company_type'] = (($billing_address['billing_company_type']) ? $billing_address['billing_company_type'] . ' ' : '');
            $array[$key]['billing_company_registration_number'] = (($billing_address['billing_company_registration_number']) ? $billing_address['billing_company_registration_number'] . ' ' : '');
            $array[$key]['billing_company_address'] = (($billing_address['billing_company_street']) ? $billing_address['billing_company_street'] . ' ' : '') . (($billing_address['billing_company_house_number']) ? $billing_address['billing_company_house_number'] . ' ' : '');
            $array[$key]['billing_company_post_code'] = (($billing_address['billing_company_post_code']) ? $billing_address['billing_company_post_code'] . ' ' : '');
            $array[$key]['billing_company_city'] = (($billing_address['billing_company_city']) ? $billing_address['billing_company_city'] . ' ' : '');
            $array[$key]['billing_company_country'] = (($billing_address['billing_company_country']) ? getCountryName($billing_address['billing_company_country']) . '' : '');
            $array[$key]['billing_company_invoice_payer_company_name'] = (($billing_address['billing_company_invoice_payer_company_name']) ? $billing_address['billing_company_invoice_payer_company_name'] . ' ' : '');
            $array[$key]['billing_company_invoice_payer_street_house_number'] = (($billing_address['billing_company_invoice_payer_street_house_number']) ? $billing_address['billing_company_invoice_payer_street_house_number'] . ' ' : '');
            $array[$key]['billing_company_invoice_payer_post_code'] = (($billing_address['billing_company_invoice_payer_post_code']) ? $billing_address['billing_company_invoice_payer_post_code'] . ' ' : '');
            $array[$key]['billing_company_invoice_payer_city'] = (($billing_address['billing_company_invoice_payer_city']) ? $billing_address['billing_company_invoice_payer_city'] : '');
            $array[$key]['billing_company_invoice_payer_country'] = (($billing_address['billing_company_invoice_payer_country']) ? $billing_address['billing_company_invoice_payer_country'] . '' : '');
            $array[$key]['billing_contact_person_name'] = $billing_address['billing_contact_person_name'];
            $array[$key]['billing_contact_person_email'] = $billing_address['billing_contact_person_email'];
            $array[$key]['billing_contact_person_mobile_number'] = $billing_address['billing_contact_person_mobile_number'];
            $array[$key]['billing_poNumber'] = $billing_address['billing_poNumber'];

            if ($row['new_imp_flag'] == '1') {
                $array[$key]['currency'] = $currency;
                $array[$key]['subtotal'] = (float)$reporting_panel_total + (float)$discount_amount;
                $array[$key]['discount_amount'] = $discount_amount;
                $array[$key]['code'] = $code;
                $array[$key]['sub_total_with_discount'] = $summary_sub_total;
                $array[$key]['vat_amount'] = $vat_amount;
                $array[$key]['grand_total'] = $grand_total;
                $array[$key]['status'] = $status;
                $array[$key]['cancel_note'] = $cancel_note;
                $array[$key]['Paymentstatus'] = $Paymentstatus;
                $array[$key]['sales_code'] = $sales_code;
                $array[$key]['sales_type'] = $sales_type;
                $array[$key]['gdpr'] = $gdpr;
            } else {
                $vat_amount = (((float)$summary_sub_total - (float)$discount_amount) * $row['vat']) / 100;
                $array[$key]['currency'] = $currency;
                $array[$key]['subtotal'] = $reporting_panel_total;
                $array[$key]['discount_amount'] = $discount_amount;
                $array[$key]['code'] = $code;
                $array[$key]['sub_total_with_discount'] = $sub_total_with_discount;
                $array[$key]['vat_amount'] = $vat_amount;
                $array[$key]['grand_total'] = $grand_total;
                $array[$key]['status'] = $status;
                $array[$key]['cancel_note'] = $cancel_note;
                $array[$key]['Paymentstatus'] = $Paymentstatus;
                $array[$key]['sales_code'] = $sales_code;
                $array[$key]['sales_type'] = $sales_type;
                $array[$key]['gdpr'] = $gdpr;
            }

            //custom fields
            $fields = \App\Models\EventCustomField::where('event_id', $this->eventId)->where('parent_id', 0)
                ->with(['info' => function ($q) {
                    return $q->where('languages_id', $this->languageId);
                }])->get();

            foreach ($fields as $field) {
                $custom_field_array = explode(',', $attendee['custom_field_id'.$this->eventId]);
                foreach ($custom_field_array as $attendee_custom_field) {
                    $field_value = \App\Models\EventCustomField::where('id', '=', $attendee_custom_field)->where('parent_id', '=', $field['id'])->with(['info' => function ($q) {
                        return $q->where('languages_id', $this->languageId);
                    }])->first();

                    if ($field_value->info[0]['value']) {
                        $array[$key][$field_value->info[0]['value']] = $field_value->info[0]['value'];
                    } 
                }
            }
            //End
        }

        return collect($array);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $heading = [
            'Order Number',
            'Transaction Id',
            'Date',
            'Time',
            $this->secLabels['membership'],
            $this->regLabels['member_number'],
            'Additional attendee',
            $this->regLabels['title'],
            $this->regLabels['initial'],
            $this->regLabels['first_name'],
            $this->regLabels['last_name'],
            $this->regLabels['email'],
            $this->regLabels['phone'],
            $this->regLabels['FIRST_NAME_PASSPORT'],
            $this->regLabels['LAST_NAME_PASSPORT'],
            $this->regLabels['BIRTHDAY_YEAR'],
            $this->regLabels['place_of_birth'],
            $this->regLabels['passport_no'],
            $this->regLabels['date_of_issue_passport'],
            $this->regLabels['date_of_expiry_passport'],
            $this->regLabels['SPOKEN_LANGUAGE'],
            $this->regLabels['EMPLOYMENT_DATE'],
            "FIK number",
            "Invoice Payment",
            $this->regLabels['ean'],
            $this->regLabels['company_name'],
            "Company Key",
            $this->regLabels['department'],
            $this->regLabels['table_number'],
            $this->regLabels['organization'],
            $this->regLabels['delegate'],
            $this->regLabels['network_group'],
            $this->regLabels['age'],
            $this->regLabels['gender'],
            $this->regLabels['jobs'],
            $this->regLabels['interests'],
            $this->regLabels['industry'],
            $this->regLabels['about'],
            $this->secLabels['address_private'] . '(' . $this->regLabels['private_house_number'] . ' + ' . $this->regLabels['private_street'] . ')',
            $this->secLabels['address_private'] . '(' . $this->regLabels['private_post_code'] . ')',
            $this->secLabels['address_private'] . '(' . $this->regLabels['private_city'] . ')',
            $this->secLabels['address_private'] . '(' . $this->regLabels['private_country'] . ')',
            "Company type",
            $this->regLabels['company_registration_number'],
            $this->secLabels['company_detail'] . '(' . $this->regLabels['company_street'] . ' + ' . $this->regLabels['company_house_number'] . ')',
            $this->secLabels['company_detail'] . '(' . $this->regLabels['company_post_code'] . ')',
            $this->secLabels['company_detail'] . '(' . $this->regLabels['company_city'] . ')',
            $this->secLabels['company_detail'] . '(' . $this->regLabels['company_country'] . ')',
            $this->secLabels['company_detail'] . '(' . $this->regLabels['company_invoice_payer_company_name'] .')',
            $this->secLabels['company_detail'] . '(' . $this->regLabels['company_invoice_payer_street_house_number'] . ')',
            $this->secLabels['company_detail'] . '(' . $this->regLabels['company_invoice_payer_post_code'] . ')',
            $this->secLabels['company_detail'] . '(' . $this->regLabels['company_invoice_payer_city'] . ')',
            $this->secLabels['company_detail'] . '(' . $this->regLabels['company_invoice_payer_country'] . ')',
            $this->regLabels['contact_person_name'],
            $this->regLabels['contact_person_email'],
            $this->regLabels['contact_person_mobile_number'],
            $this->regLabels['poNumber'],
            "Currency",
            "Subtotal",
            "Discount Amount",
            "Discount code",
            "Subtotal with discount",
            "VAT Amount",
            "Grand total",
            "Status",
            "Cancellation Note",
            "Payment Status",
            "Sales code",
            "Sales type",
            "GDPR",
        ];

        //custom fields
        $fields = \App\Models\EventCustomField::where('event_id', $this->eventId)->where('parent_id', 0)
            ->with(['info' => function ($q) {
                return $q->where('languages_id', $this->languageId);
            }])->get();
        foreach ($fields as $field) {
            $heading[$field->info[0]['value']] = $field->info[0]['value'];
        }
        
        return $heading;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Orders';
    }
}
