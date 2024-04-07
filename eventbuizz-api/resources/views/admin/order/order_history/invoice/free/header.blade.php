@if($pdf)
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        .invoice-box {
            max-width: 900px;
            margin: auto;
            padding: 0px 0;
            font-size: 15px;
            line-height: 1.4;
            font-family:'Open Sans', sans-serif;
            color: #636466;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table.top {
            border-bottom: 1px solid #bebebe;
            padding-bottom: 20px;
        }

        .invoice-box table.top td {
            padding-bottom: 5px;
        }

        .invoice-box table.top td strong {
            color: #101010;
            font-size: 24px;
            line-height: 1;
        }

        .invoice-box table td {
            padding: 0px;
            vertical-align: top;
        }

        .invoice-box table tr td:last-child {
            text-align: right;
        }

        .invoice-box table tr td:first-child {
            text-align: left;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
    
    </style>
</head>

<body style="padding: 0px; margin: 0px;">
    <div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
@endif
        <thead class="thead">
            <tr>
                <td colspan="2" style="font-size: 14px">
                    <table class="top">
                        <tr>
                            <td colspan="2">
                                @if(!empty($order_detail['order_main_attendee']['info']['company_name']))
                                    <strong>{{ $order_detail['order_main_attendee']['info']['company_name'] }}</strong>
                                @else
                                    <strong>{{ $order_detail['order_main_attendee']['first_name'].' '.$order_detail['order_main_attendee']['last_name'] }}</strong>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                @if(!empty($order_detail['order_billing_detail']['billing_company_street']) || !empty($order_detail['order_billing_detail']['billing_company_house_number']))
                                {{ $order_detail['order_billing_detail']['billing_company_street'].' '.$order_detail['order_billing_detail']['billing_company_house_number'] }} <br>
                                @endif
                                @if(!empty($order_detail['order_billing_detail']['billing_company_post_code']) || !empty($order_detail['order_billing_detail']['billing_company_city']))
                                    {{ $order_detail['order_billing_detail']['billing_company_post_code'].' '.$order_detail['order_billing_detail']['billing_company_city'] }} <br>
                                @endif
                                @if(!empty($order_detail['order_billing_detail']['billing_company_country']))
                                    {{ getCountryName($order_detail['order_billing_detail']['billing_company_country']) }} <br>
                                @endif
                                @if($order_detail['order_billing_detail']['billing_ean'] && $order_detail['order_billing_detail']['billing_company_type'] == 'public' &&
                                        strtolower($order_detail['order_billing_detail']['billing_ean']) != 'ean')
                                    {{ $billing_fields['ean'].': '.$order_detail['order_billing_detail']['billing_ean'] }} <br>
                                @endif
                                @if($order_detail['order_billing_detail']['billing_company_registration_number'])
                                    {{ $billing_fields['company_registration_number'].': '.$order_detail['order_billing_detail']['billing_company_registration_number'] }}
                                @endif
                            </td>

                            <td class="title">
                                @if($eventSetting['invoice_logo'])
                                    {{HTML::image(cdn('assets/event/invoice/' . $eventSetting['invoice_logo']), '', ['width' => '250', 'height' => '85'])}}
                                @elseif($eventSetting['header_logo'])
                                    {{HTML::image(cdn('assets/event/branding/' . $eventSetting['header_logo']), '', ['width' => '250', 'height' => '85'])}}
                                @else
                                    {{ HTML::image(cdn('_mobile_assets/images/logo-header@2x.png'), '', ['width' => '250', 'height' => '85']) }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </thead>
@if($pdf)
    </table>
    </div>
</body>
</html>
@endif