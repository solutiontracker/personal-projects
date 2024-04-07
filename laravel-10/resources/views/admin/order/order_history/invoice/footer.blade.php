@if(strlen(trim(stripslashes($payment_setting['footer_text']))) > 0)
@if($pdf)
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        .invoice-box {
            max-width: 900px;
            margin: auto;
            font-size: 15px;
            line-height: 1.4;
            font-family:'Open Sans', sans-serif;
            color: #636466;
        }

        .tfooter-text {
            padding: 10px 8px;
            max-width: 900px;
            line-height: 1.5;
            letter-spacing: normal;
            color: #636466;
            border-top: 2px solid {{$eventSetting['primary_color']}};
            font-size: 11px;
        }

    </style>
</head>

<body style="margin: 0px; padding: 0px;">
<div class="invoice-box">
@endif
    <div class="tfooter-text">
        {!! $payment_setting['footer_text'] !!}
    </div>
@if($pdf)
</div>
</body>
@endif

</html>
@endif