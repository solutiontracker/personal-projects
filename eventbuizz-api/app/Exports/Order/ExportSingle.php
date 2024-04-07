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

class ExportSingle implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    protected $eventId;
    protected $languageId;
    protected $request;
    protected $results;
    protected $secLabels;
    protected $regLabels;
    protected $billing_items;
    protected $subRegistration;
    protected $hotel_col_array;
    protected $fields;

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

        //Billing items
        $this->billing_items = \App\Models\BillingItem::where('event_id', $this->eventId)->where('type', '<>', 'group')->where('type', '<>', 'admin_fee')->where('is_archive', '0')->with(['info' => function ($query) {
            return $query->where('languages_id', $this->languageId);
        }])->get()->toArray();
        $this->billing_items = returnArrayKeys($this->billing_items, ['info']);

        //sub registrations
        $this->subRegistration = \App\Models\EventSubRegistration::where('event_id', $this->eventId)->with(['question.info', 'results' => function ($query) {
            return $query->orderBy('id', 'DESC');
        }])->get()->toArray();

        //Hotel columns
        $this->hotel_col_array = array('Hotel item', 'Rooms', 'Check in date', 'Check out date', 'Days', 'Price', 'Price Type', 'Subtotal', 'Vat%', 'Vat Price', 'Total');

        //Fields
        $this->fields = \App\Models\EventCustomField::where('event_id', $this->eventId)->where('parent_id', 0)
                ->with(['info' => function ($q) {
                    return $q->where('languages_id', $this->languageId);
                }])->get();
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:CX1'; // All headers
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
            $array[$key]['order_date'] = date('d-m-Y', strtotime($row['order_date']));
            $array[$key]['order_time'] = date('H.i.s', strtotime($row['order_date']));
            $array[$key]['updated_at'] = date('d-m-Y', strtotime($row['updated_at']));
            $array[$key]['initial'] = $attendee['initial'];
            $array[$key]['first_name'] = $attendee['first_name'];
            $array[$key]['last_name'] = $attendee['last_name'];
            $array[$key]['email'] = $attendee['email'];
            $array[$key]['company_name'] = $attendee['company_name'];
            $array[$key]['company_key'] = $attendee['company_key'];
            $array[$key]['department'] = $attendee['department'];
            $array[$key]['delegate_number'] = $attendee['delegate_number'];
            $array[$key]['table_number'] = $attendee['table_number'];
            $array[$key]['network_group'] = $attendee['network_group'];
            $array[$key]['age'] = $attendee['age'];
            $array[$key]['gender'] = $attendee['gender'];
            $array[$key]['organization'] = $attendee['organization'];
            $array[$key]['jobs'] = $attendee['jobs'];
            $array[$key]['interests'] = $attendee['interests'];
            $array[$key]['title'] = $attendee['title'];
            $array[$key]['industry'] = $attendee['industry'];
            $array[$key]['about'] = $attendee['about'];
            if ($attendee['phone'] == '+-') {
                $array[$key]['phone'] = "";
            } else {
                $array[$key]['phone'] = $attendee['phone'];
            }
            $array[$key]['country'] = getCountryName($attendee['country']);
            $array[$key]['FIRST_NAME_PASSPORT'] = $attendee['FIRST_NAME_PASSPORT'];
            $array[$key]['LAST_NAME_PASSPORT'] = $attendee['LAST_NAME_PASSPORT'];
            $array[$key]['BIRTHDAY_YEAR'] = ($attendee['BIRTHDAY_YEAR'] != '' ? date('d-m-Y', strtotime($attendee['BIRTHDAY_YEAR'])) : '');
            $array[$key]['place_of_birth'] = $attendee['place_of_birth'];
            $array[$key]['passport_no'] = $attendee['passport_no'];
            $array[$key]['date_of_issue_passport'] = ($attendee['date_of_issue_passport'] != '' ? date('d-m-Y', strtotime($attendee['date_of_issue_passport'])) : '');
            $array[$key]['date_of_expiry_passport'] = ($attendee['date_of_expiry_passport'] != '' ? date('d-m-Y', strtotime($attendee['date_of_expiry_passport'])) : '');
            $array[$key]['SPOKEN_LANGUAGE'] = $attendee['SPOKEN_LANGUAGE'];
            $array[$key]['EMPLOYMENT_DATE'] = ($attendee['EMPLOYMENT_DATE'] ? date('d-m-Y', strtotime($attendee['EMPLOYMENT_DATE'])) : '');
            $array[$key]['private_address'] = (($attendee['private_street']) ? $attendee['private_street'] . ' ' : '') . (($attendee['private_house_number']) ? $attendee['private_house_number'] . ' ' : '');
            $array[$key]['private_post_code'] = (($attendee['private_post_code']) ? $attendee['private_post_code'] . ' ' : '');
            $array[$key]['private_city'] = (($attendee['private_city']) ? $attendee['private_city'] . ' ' : '');
            $array[$key]['private_country'] = (($attendee['private_country']) ? getCountryName($attendee['private_country']) . '' : '');
            $array[$key]['gdpr'] = $gdpr;

            //custom fields
            foreach ($this->fields as $field) {
                $string = '';
                $custom_field_array = explode(',', $attendee['custom_field_id' . $this->eventId]);
                foreach ($custom_field_array as $attendee_custom_field) {
                    $field_value = \App\Models\EventCustomField::where('id', '=', $attendee_custom_field)->where('parent_id', '=', $field['id'])->with(['info' => function ($q) {
                        return $q->where('languages_id', $this->languageId);
                    }])->first();

                    if ($field_value->info[0]['value']) {
                        $string = $field_value->info[0]['value'];
                    }
                }

                $array[$key][$field->info[0]['value']] = $string;
            }
            //End

            //Billing items
            foreach ($this->billing_items as $item) {
                $group_name = '';
                if ($item['group_id'] != 0) {
                    $group_name = \App\Models\BillingItemInfo::where('item_id', $item['group_id'])->where('languages_id', $this->languageId)->where('name', 'group_name')->get()->toArray();
                    $group_name = $group_name[0]['value'] . ' - ';
                }
                $bit = false;
                foreach ($row['order_addons'] as $addon) {
                    if ($item['id'] == $addon['addon_id'] && $addon['attendee_id'] == $row['main_attendee_id']) {
                        $bit = true;
                        $array[$key][$group_name . $item['item_name']] = 'X';
                        $array[$key][$group_name . $item['item_name'] . ' - quantity'] = $addon['qty'];
                    }
                }
                if (!$bit) {
                    $array[$key][$group_name . $item['item_name']] = '';
                    $array[$key][$group_name . $item['item_name'] . ' - quantity'] = '';
                }
            }

            
            $array[$key]['fik_number'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $invoice_number : '');

            //Ean number
            $billing = \App\Models\AttendeeBilling::where('attendee_id', $row['attendee_id'])->where('event_id', $this->eventId)->where('order_id', $row['id'])->first();
            $array[$key]['ean'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_ean : '');

            $array[$key]['order_type'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $row['order_type'] : '');
            $array[$key]['event_qty'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $row['event_qty'] : '');
            $array[$key]['vat_amount'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $vat_amount : '');
            $array[$key]['summary_sub_total'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $summary_sub_total : '');
            $array[$key]['grand_total'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $grand_total : '');

            if ($row['is_waitinglist'] == '1') {
                $array[$key]['status'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? 'Waiting' : '');
            } elseif ($row['is_cancelled_wcn'] == '1') {
                $array[$key]['status'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? 'Cancelled without creditnote' : '');
            } else {
                $array[$key]['status'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $row['status'] : '');
            }
            $array[$key]['cancellation_note'] = $row['status'] == 'cancelled' && $row['main_attendee_id'] == $row['order_attendee_id'] ? $row['comments'] : '';
            $array[$key]['voucher_code'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $row['code'] : '');

            //sub registrations
            foreach ($this->subRegistration as $subReg) {
                foreach ($subReg['question'] as $question) {
                    $comments = '';
                    $answer_string = '';
                    if ($question['question_type'] == 'open') {
                        $comments = '';
                        foreach ($subReg['results'] as $result) {
                            if ($result['question_id'] == $question['id'] && $result['attendee_id'] == $row['main_attendee_id']) {
                                $answer_string = $result['answer'];
                                if ((is_array($result['comments'])&&count($result['comments']) > 0) || is_string($result['comments'])&&$result['comments']!=='') {
                                    $comments = $result['comments'];
                                }
                                break;
                            }
                        }
                    } else if ($question['question_type'] == 'single') {
                        $comments = '';
                        foreach ($subReg['results'] as $result) {
                            if ($result['question_id'] == $question['id'] && $result['attendee_id'] == $row['main_attendee_id']) {
                                $answer_value = \App\Models\EventSubRegistrationAnswer::where('id', $result['answer_id'])->with(['info' => function ($query) {
                                    return $query->where('languages_id', $this->languageId);
                                }])->first()->toArray();
                                if (count($answer_value) > 0) {
                                    $answer_value = readArrayKey($answer_value, [], 'info');
                                    $answer_string = $answer_value['answer'];
                                } else {
                                    $answer_string = "";
                                }
                                if ((is_array($result['comments'])&&count($result['comments']) > 0) || is_string($result['comments'])&&$result['comments']!=='') {
                                    $comments = $result['comments'];
                                }
                                break;
                            }
                        }
                    } else if ($question['question_type'] == 'date_time') {
                        foreach ($subReg['results'] as $result) {
                            if ($result['question_id'] == $question['id'] && $result['attendee_id'] == $row['main_attendee_id']) {
                                $answer_string = date('d-m-Y H:i', strtotime($result['answer']));
                                if ((is_array($result['comments'])&&count($result['comments']) > 0) || is_string($result['comments'])&&$result['comments']!=='') {
                                    $comments = $result['comments'];
                                }
                                break;
                            }
                        }
                    } else if ($question['question_type'] == 'date') {
                        foreach ($subReg['results'] as $result) {
                            if ($result['question_id'] == $question['id'] && $result['attendee_id'] == $row['main_attendee_id']) {
                                $answer_string = date('d-m-Y', strtotime($result['answer']));
                                if ((is_array($result['comments'])&&count($result['comments']) > 0) || is_string($result['comments'])&&$result['comments']!=='') {
                                    $comments = $result['comments'];
                                }
                                break;
                            }
                        }
                    } else if ($question['question_type'] == 'number') {
                        foreach ($subReg['results'] as $result) {
                            if ($result['question_id'] == $question['id'] && $result['attendee_id'] == $row['main_attendee_id']) {
                                $answer_string = $result['answer'];
                                if ((is_array($result['comments'])&&count($result['comments']) > 0) || is_string($result['comments'])&&$result['comments']!=='') {
                                    $comments = $result['comments'];
                                }
                                break;
                            }
                        }
                    } else if ($question['question_type'] == 'dropdown') {
                        foreach ($subReg['results'] as $result) {
                            if ($result['question_id'] == $question['id'] && $result['attendee_id'] == $row['main_attendee_id']) {
                                $answer_value = \App\Models\EventSubRegistrationAnswer::where('id', $result['answer_id'])->with(['info' => function ($query) {
                                    return $query->where('languages_id', $this->languageId);
                                }])->first()->toArray();
                                if (count($answer_value) > 0) {
                                    $answer_value = readArrayKey($answer_value, [], 'info');
                                    $answer_string = $answer_value['answer'];
                                } else {
                                    $answer_string = "";
                                }
                                if ((is_array($result['comments'])&&count($result['comments']) > 0) || is_string($result['comments'])&&$result['comments']!=='') {
                                    $comments = $result['comments'];
                                }
                                break;
                            }
                        }
                    } else {
                        foreach ($subReg['results'] as $result3) {
                            if ($result3['question_id'] == $question['id'] && $result3['attendee_id'] == $row['main_attendee_id']) {
                                $answer_value = \App\Models\EventSubRegistrationAnswer::where('id', $result3['answer_id'])->with(['info' => function ($query) {
                                    return $query->where('languages_id', $this->languageId);
                                }])->first()->toArray();
                                if (count($answer_value) > 0) {
                                    $answer_value = readArrayKey($answer_value, [], 'info');
                                    $answer_string .= $answer_value['answer'] . ',';
                                }
                                if ((is_array($result3['comments'])&&count($result3['comments']) > 0) || is_string($result3['comments'])&&$result3['comments']!=='') {
                                    $comments = $result3['comments'];

                                }
                            }
                        }
                        if (trim($answer_string) == '') {
                            $answer_string = $answer_string;
                        } else {
                            $answer_string = substr($answer_string, 0, -1);
                        }
                    }
                    $array[$key][$question['info'][0]['value'].$question['id']] = $answer_string;
                    $array[$key]['Comments'.$question['id']] = $comments;
                }
            }

            $array[$key]['billing_contact_person_name'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_contact_person_name : '');
            $array[$key]['billing_contact_person_email'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_contact_person_email : '');
            $array[$key]['billing_contact_person_mobile_number'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_contact_person_mobile_number : '');
            $array[$key]['billing_company_street'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_company_street : '');
            $array[$key]['billing_company_house_number'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_company_house_number : '');
            $array[$key]['billing_company_post_code'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_company_post_code : '');
            $array[$key]['billing_company_city'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_company_city : '');
            $array[$key]['billing_company_country'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? getCountryName($billing->billing_company_country) : '');
            $array[$key]['billing_company_invoice_payer_company_name'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_company_invoice_payer_company_name : '');
            $array[$key]['billing_company_invoice_payer_street_house_number'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_company_invoice_payer_street_house_number : '');
            $array[$key]['billing_company_invoice_payer_post_code'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_company_invoice_payer_post_code : '');
            $array[$key]['billing_company_invoice_payer_city'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_company_invoice_payer_city : '');
            $array[$key]['billing_company_invoice_payer_country'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_company_invoice_payer_country : '');
            $array[$key]['billing_company_registration_number'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_company_registration_number : '');
            $array[$key]['billing_poNumber'] = ($row['main_attendee_id'] == $row['order_attendee_id'] ? $billing->billing_poNumber : '');

            //Hotel data
            $order_hotel = \App\Models\EventOrderHotel::where('order_id', $row['id'])->get();
            if (count($order_hotel) && $row['main_attendee_id'] == $row['order_attendee_id']) {
                $order_hotel = $order_hotel[0];
                $rooms = $order_hotel['rooms'];
                $checkinValue = $order_hotel['checkin'];
                $checkOutValue = $order_hotel['checkout'];
                $checkinDate = ($checkinValue ? date('d-m-Y', strtotime($checkinValue)) : '');
                $checkoutDate = ($checkOutValue ? date('d-m-Y', strtotime($checkOutValue)) : '');
                $checkinDateString = strtotime($checkinValue);
                $checkoutDateString = strtotime($checkOutValue);
                $datediff = $checkoutDateString - $checkinDateString;
                $days = floor($datediff / (60 * 60 * 24));
                if ($days == 0) {
                    $days = 1;
                }

                $array[$key]['Hotel item'] = $order_hotel['name'];
                $array[$key]['Rooms'] = $order_hotel['rooms'];
                $array[$key]['Check in date'] = $checkinDate;
                $array[$key]['Check out date'] = $checkoutDate;
                $array[$key]['Days'] = $days;
                $array[$key]['Price'] = $order_hotel['price'];
                if ($order_hotel['price_type'] == 'fixed') {
                    $array[$key]['Price Type'] = 'Fixed';
                } else if ($order_hotel['price_type'] == 'notfixed') {
                    $array[$key]['Price Type'] = 'Not Fixed';
                } else {
                    $array[$key]['Price Type'] = '';
                }
                if ($order_hotel['price_type'] == 'fixed') {
                    $subtotal = $rooms * $order_hotel['price'];
                } else {
                    $subtotal = $rooms * $order_hotel['price'] * $days;
                }
                $final_total = $subtotal + $order_hotel['vat_price'];
                $array[$key]['Subtotal'] = $subtotal;
                $array[$key]['Vat%'] = ($order_hotel['vat'] ? $order_hotel['vat'] : 0);
                $array[$key]['Vat Price'] = ($order_hotel['vat_price'] ? $order_hotel['vat_price'] : 0);
                $array[$key]['Total'] = $final_total;

                $hotel_persons = \App\Models\EventHotelPerson::where('order_id', $row['id'])->get();
                if (count($hotel_persons) > 0) {
                    foreach ($hotel_persons as $person) {
                        $array[$key]['name'] = $person['name'];
                        $array[$key]['dob'] = $person['dob'];
                    }
                }

            } else {
                foreach ($this->hotel_col_array as $head) {
                    $array[$key][$head] = "";
                }
            }
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
            'Updated Date',
            $this->regLabels['initial'],
            $this->regLabels['first_name'],
            $this->regLabels['last_name'],
            $this->regLabels['email'],
            $this->regLabels['company_name'],
            "Company Key",
            $this->regLabels['department'],
            $this->regLabels['delegate'],
            $this->regLabels['table_number'],
            $this->regLabels['network_group'],
            $this->regLabels['age'],
            $this->regLabels['gender'],
            $this->regLabels['organization'],
            $this->regLabels['jobs'],
            $this->regLabels['interests'],
            $this->regLabels['title'],
            $this->regLabels['industry'],
            $this->regLabels['about'],
            $this->regLabels['phone'],
            (isset($this->regLabels['country']) ? $this->regLabels['country'] : 'Country'),
            $this->regLabels['FIRST_NAME_PASSPORT'],
            $this->regLabels['LAST_NAME_PASSPORT'],
            $this->regLabels['BIRTHDAY_YEAR'],
            (isset($this->regLabels['place_of_birth']) ? $this->regLabels['place_of_birth'] : "Place of birth"),
            (isset($this->regLabels['passport_no']) ? $this->regLabels['passport_no'] : "Passport no"),
            (isset($this->regLabels['date_of_issue_passport']) ? $this->regLabels['date_of_issue_passport'] : "Date of issue passport"),
            (isset($this->regLabels['date_of_expiry_passport']) ? $this->regLabels['date_of_expiry_passport'] : "Date of expiry passport"),
            $this->regLabels['SPOKEN_LANGUAGE'],
            $this->regLabels['EMPLOYMENT_DATE'],
            $this->secLabels['address_private'] . '(' . $this->regLabels['private_house_number'] . ' + ' . $this->regLabels['private_street'] . ')',
            $this->secLabels['address_private'] . '(' . $this->regLabels['private_post_code'] . ')',
            $this->secLabels['address_private'] . '(' . $this->regLabels['private_city'] . ')',
            $this->secLabels['address_private'] . '(' . $this->regLabels['private_country'] . ')',
            "GDPR",
        ];

        //custom fields
        foreach ($this->fields as $field) {
            array_push($heading, $field->info[0]['value']);
        }

        //Billing items
        foreach ($this->billing_items as $item) {
            $group_name = '';
            if ($item['group_id'] != 0) {
                $group_name = \App\Models\BillingItemInfo::where('item_id', $item['group_id'])->where('languages_id', $this->languageId)->where('name', 'group_name')->get()->toArray();
                $group_name = $group_name[0]['value'] . ' - ';
            }
            array_push($heading, $group_name . $item['item_name']);
            array_push($heading, $group_name . $item['item_name'] . ' - quantity');
        }

        array_push($heading, "FIK number");
        array_push($heading, $this->regLabels['ean']);
        array_push($heading, 'Order type');
        array_push($heading, 'Quantity');
        array_push($heading, 'Vat amount');
        array_push($heading, 'Sub total');
        array_push($heading, 'Grand Total');
        array_push($heading, 'Status');
        array_push($heading, 'Cancellation Note');
        array_push($heading, 'Voucher Code');

        //sub registrations
        foreach ($this->subRegistration as $subReg) {
            foreach ($subReg['question'] as $question) {
                array_push($heading, $question['info'][0]['value']);
                array_push($heading, 'Comments');
            }
        }

        array_push($heading, $this->regLabels['contact_person_name']);
        array_push($heading, $this->regLabels['contact_person_email']);
        array_push($heading, $this->regLabels['contact_person_mobile_number']);
        array_push($heading, $this->secLabels['company_detail'] . '(' . $this->regLabels['company_street'] . ' + ' . $this->regLabels['company_house_number'] . ')');
        array_push($heading, $this->secLabels['company_detail'] . '(' . $this->regLabels['company_house_number'] . ')');
        array_push($heading, $this->secLabels['company_detail'] . '(' . $this->regLabels['company_post_code'] . ')');
        array_push($heading, $this->secLabels['company_detail'] . '(' . $this->regLabels['company_city'] . ')');
        array_push($heading, $this->secLabels['company_detail'] . '(' . $this->regLabels['company_country'] . ')');
        array_push($heading, $this->secLabels['company_detail'] . '(' . $this->regLabels['company_invoice_payer_company_name'] . ')');
        array_push($heading, $this->secLabels['company_detail'] . '(' . $this->regLabels['company_invoice_payer_street_house_number'] . ')');
        array_push($heading, $this->secLabels['company_detail'] . '(' . $this->regLabels['company_invoice_payer_post_code'] . ')');
        array_push($heading, $this->secLabels['company_detail'] . '(' . $this->regLabels['company_invoice_payer_city'] . ')');
        array_push($heading, $this->secLabels['company_detail'] . '(' . $this->regLabels['company_invoice_payer_country'] . ')');
        array_push($heading, $this->regLabels['company_registration_number']);
        array_push($heading, $this->regLabels['poNumber']);

        //Hotel columns
        foreach ($this->hotel_col_array as $head) {
            array_push($heading, $head);
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
