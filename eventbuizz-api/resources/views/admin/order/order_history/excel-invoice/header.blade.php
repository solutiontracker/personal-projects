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
        <thead class="thead">
            <tr>
            <td colspan="2" style="font-size: 14px">
                <table class="top">

                    <tr>
                        <td colspan="2">
                            <strong>{{ $order['company_name'] }}</strong>
                        </td>
                    </tr>

                    <tr>

                        <td>
                            @if(trim($order['billing_company_street']))
                                {{ $order['billing_company_street'] }} <br>
                            @endif

                            @if(trim($order['billing_company_post_code']))
                                {{ $order['billing_company_post_code'].' '.$order['billing_company_city'] }} <br>
                            @endif

                            @if(trim($order['billing_ean']))
                                {{ 'Ean: '.$order['billing_ean'] }} <br>
                            @endif

                            @if(trim($order['billing_company_registration_number']))
                                {{ 'CVR: '.$order['billing_company_registration_number'] }}
                            @endif
                        </td>

                        <td class="title">
                            {{HTML::image('https://my.eventbuizz.com/assets/event/branding/090854_image_5400332871630573731.jpg', '', ['width' => '250', 'height' => '85'])}}
                        </td>

                    </tr>
                </table>
            </td>
        </tr>
        </thead>
        </table>
</div>
</body>
</html>