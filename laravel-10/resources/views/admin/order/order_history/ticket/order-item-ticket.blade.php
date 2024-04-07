<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print page</title>
    @if (!$css_included)
        <style>
            html {
                box-sizing: border-box;
            }

            *,
            *:before,
            *:after {
                box-sizing: inherit;
            }

            * {
                max-height: 1000000px;
            }

            article,
            aside,
            details,
            figcaption,
            figure,
            footer,
            header,
            main,
            nav,
            section,
            summary {
                display: block;
            }

            img {
                border-style: none;
            }

            a {
                text-decoration: none;
                color: #808184;
            }

            a:hover {
                color: #808184;
                text-decoration: none;
            }

            sup {
                top: -.5em;
            }

            sub {
                bottom: -.25em;
            }

            table {
                border-collapse: collapse;
                border-spacing: 0;
            }

            #wrapper {
                width: 100%;
                position: relative;
            }

            body {
                margin: 0;
                color: #808184;
                background: #fff;
                font: normal 11.89px/13.89px Arial, sans-serif;
                min-width: 930px;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: none;
            }

            .pdf-page {
                width: 930px;
                margin: 0 auto;
                padding: 77px 15px 0;
            }

            .pdf-header {
                background: #f4f4f4;
                overflow: hidden;
                padding: 11px 22px 8px;
                border: 1px solid #ebecec;
                border-bottom: 0;
            }

            .pdf-header-right {
                float: right;
                text-align: right;
                padding: 6px 5px 0 0;
            }

            .pdf-header-right strong {
                font-size: 20.39px;
                line-height: 22.39px;
                color: #231f20;
                text-transform: uppercase;
            }

            .pdf-header-left {
                overflow: hidden;
            }

            .pdf-header-left ul {
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .pdf-header-left ul>li {
                width: 33%;
                display: inline-block;
                vertical-align: top;
                margin: 0 -4px 0 0;
            }

            .pdf-header-left ul>li>span {
                display: block;
                color: #808184;
                font-size: 11.89px;
                line-height: 14.89px;
                font-weight: normal;
                text-transform: uppercase;
            }

            .pdf-header-left ul>li>strong {
                display: block;
                color: #231f20;
                font-size: 16.99px;
                line-height: 19.99px;
                font-weight: normal;
            }

            .pdf-content {
                border: 1px solid #ebecec;
                border-top: 0;
                padding: 16px 22px 0;
                background-color: #fff;
            }

            .content-holder {
                padding: 0;
                overflow: hidden;
            }

            .barcode {
                width: 125px;
                float: right;
                margin: 0 -9px 0 0;
            }

            .barcode img {
                display: block;
                margin: 0 auto;
                max-width: 100%;
            }

            .pdf-content-left {
                overflow: hidden;
            }

            .pdf-logo {
                float: left;
                width: 212px;
                margin: 11px 0 0;
            }

            .pdf-logo img {
                display: block;
                height: auto;
                max-width: 100%;
                margin: 0 auto;
            }

            .content-text {
                padding: 9px 12px 0 32px;
                overflow: hidden;
                color: #000000;
            }

            .content-text strong {
                display: block;
                color: #000;
                font-weight: bold;
                font-size: 26.63px;
                line-height: 30.59px;
            }

            .ticket-type {
                overflow: hidden;
                padding: 0 0 16px;
                border-bottom: 1px solid #ebecec;
            }

            .ticket-type span {
                display: block;
                color: #808184;
                font-size: 11.89px;
                line-height: 15.89px;
                text-transform: uppercase;
            }

            .ticket-type strong {
                display: block;
                color: #231f20;
                font-weight: normal;
                font-size: 25.49px;
                line-height: 28.49px;
            }

            .ticket-info {
                padding: 14px 0 22px 0;
                overflow: hidden;
            }

            .ticket-info ul {
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .ticket-info ul>li {
                padding: 0 53px 0 0;
                display: inline-block;
                vertical-align: top;
            }

            .ticket-info ul>li:last-child {
                padding: 0;
            }

            .ticket-info ul>li>span {
                display: block;
                color: #808184;
                font-size: 11.89px;
                line-height: 13.89px;
            }

            .ticket-info ul>li>strong {
                display: block;
                color: #231f20;
                font-weight: normal;
                font-size: 16.99px;
                line-height: 19.99px;
            }

            .location-bar {
                padding: 16px 22px;
                overflow: hidden;
                background: #0019a5;
                color: #fff;
                margin: 0 -22px;
            }

            .location-bar strong {
                display: block;
                position: relative;
                font-size: 16.99px;
                font-weight: bold;
                line-height: 22.09px;
                padding: 0 0 0 20px;
            }

            .location-bar strong span {
                font-weight: normal;
            }

            .location-bar strong:before {
                content: "";
                width: 11px;
                height: 17px;
                left: 0;
                top: 0;
                position: absolute;
                background: url("{{ cdn('_admin_assets/images/ico-1.png') }}") no-repeat 0 0;
            }

            .pdf-footer {
                text-align: right;
                overflow: hidden;
                padding: 11px 3px 21px;
            }

            .pdf-footer strong {
                font-size: 15.29px;
                line-height: 22.09px;
                font-weight: bold;
                color: #231f20;
                display: inline-block;
                vertical-align: top;
            }

            .pdf-footer strong span {
                font-weight: normal;
                font-style: italic;
            }

        </style>
    @endif
</head>

<body>
    <div id="wrapper">
        <div class="pdf-page">
            <div class="pdf-header">
                <div class="pdf-header-right">
                    <strong>#{{ $ticket->serial }}</strong>
                </div>
                <div class="pdf-header-left">
                    <ul>
                        @if ($ticket->type == 'billing')
                            <li>
                                <span>{{ $labels['TICKET_HOLDER'] }}</span>
                                <strong>{{ ucwords($ticket->addon->attendee->first_name . ' ' . $ticket->addon->attendee->last_name) }}</strong>
                            </li>
                        @endif
                        <li>
                            <span>{{ $ticket->type == 'billing' ? $labels['TICKET_ISSUED_BY'] : $labels['TICKET_ISSUED_TO'] }}</span>
                            <strong>{{ $ticket->type == 'billing' ? ucwords($event['organizer_name']) : ucwords($ticket->addon->order->user->name) }}</strong>
                        </li>
                        <li>
                            <span>{{ $labels['TICKET_ISSUED_ON'] }}</span>
                            <strong>{{ \Carbon\Carbon::parse($ticket->addon->created_at)->format('d-m-Y H:i') }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="pdf-content">
                <div class="content-holder">
                    <div class="barcode">
                        <img src="{{ config('app.eventcenter_url') . '/api/QrCode?chl=' . $ticket->qrString }}" alt="">
                    </div>
                    <div class="pdf-content-left">
                        <div class="pdf-logo">
                            @if ($event_setting['header_logo'] != '' && $event_setting['header_logo'] != 'NULL')
                                <img src="{{ cdn('assets/event/branding/' . $event_setting['header_logo']) }}" />
                            @else
                                <img src="{{ cdn('_admin_assets/images/eventbuizz_logo.png') }}" />
                            @endif
                        </div>
                        <div class="content-text">
                            <strong>{{ $event->name }}</strong>
                                @if($labels['TICKET_DESCRIPTION'])
                                    <p>{{ $labels['TICKET_DESCRIPTION'] }}</p>
                                @endif
                        </div>
                    </div>
                </div>
                <div class="ticket-type">
                    <span>{{ $labels['TICKET_TYPE'] }}</span>
                    <strong>
                        {{ $ticket->ticket_item->item_name }}&nbsp;
                        @if($eventTicketSetting->show_item_name)
                            ({{ $ticket->addon->name }})
                        @endif
                    </strong>
                </div>
                <div class="ticket-info">
                    @foreach ($ticket->validity as $validity)
                    <ul>
                        {{-- to add backward compatibility --}}
                        @if ($eventTicketSetting->show_price != '0')
                        <li>
                            <span>{{ $labels['TICKET_PRICE'] }}</span>
                            <strong>{{ $currency }}
                                {{ getCurrency($ticket->price, $currency) }}</strong>
                            </li>
                            @endif
                            @if(!$eventTicketSetting->hide_validity_detail)
                                <li>
                                    <span>{{ $labels['TICKETS_USAGE'] }}</span>
                                    <strong>{{ $validity['usage_limit'] }}</strong>
                                </li>
                               
                                <li>
                                    <span>{{ $labels['TICKETS_VALID_FROM'] }}</span>
                                    <strong>{{ \Carbon\Carbon::parse($validity['valid_from'])->format('d-m-Y') }}</strong>
                                </li>
                                <li>
                                    <span>{{ $labels['TICKETS_VALID_TILL'] }}</span>
                                    <strong>{{ \Carbon\Carbon::parse($validity['valid_to'])->format('d-m-Y') }}</strong>
                                </li>
            
                                <li>
                                    <span>{{ $labels['TICKETS_EVENT_DATE'] }}</span>
                                    @if (\Carbon\Carbon::parse($event_start_date)->format('d-m-Y') != Carbon\Carbon::parse($event_end_date)->format('d-m-Y'))
                                        <strong>{{ \Carbon\Carbon::parse($event_start_date)->format('d-m-Y') . '–' . \Carbon\Carbon::parse($event_end_date)->format('d-m-Y') }}</strong>
                                    @else
                                        <strong>{{ \Carbon\Carbon::parse($event_start_date)->format('d-m-Y') }}</strong>
                                    @endif
                                </li>
                                @endif
                            </ul>
                        @endforeach
                    </div>
                <div class="location-bar" style="background: {{ $event_setting['primary_color'] }};">
                    <strong>{{ $eventInfo['location_name'] }}, <span>{{ $eventInfo['location_address'] }},
                            {{ getCountryName($event['country_id']) }}</span></strong>
                </div>
                <div class="pdf-footer">
                    <strong><span>{{ $labels['TICKETS_ORGANISED_BY'] }}:</span>
                        {{ ucwords($event['organizer_name']) }}</strong>
                </div>
            </div>
            <div style="padding-top:30px;">
                {!! $description !!}
            </div>
        </div>
    </div>
</body>

</html>
