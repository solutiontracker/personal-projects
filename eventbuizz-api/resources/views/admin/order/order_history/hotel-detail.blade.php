<html>

<head>
    <meta charset="utf-8">
    <title>{{ $eventSetting['name'] }}-Hotel Detail</title>
    <style>
        /* invoice header css*/
        .invoiceBox {
            max-width: 900px;
            margin: auto;
            padding: 0px 0;
            font-size: 15px;
            line-height: 1.4;
            font-family: 'Open Sans', sans-serif;
            color: #636466;
        }


        .divTable {
            display: table;
            color: #636466;
            width: 100%;
            font-family: Open Sans, sans-serif;
            font-size: 14px;
        }

        .divTableRow {
            display: table-row;
            border: none;
        }

        .divTableHeading {
            font-size: 13px;
            font-weight: 700;
            background: transparent !important;
            display: table-header-group;
        }

        .divTableCell,
        .divTableHead {
            border-top: 1px solid #bebebe;
            display: table-cell;
            padding: 12px;
            text-align: right;
            width: 13%;
        }

        .divTableBody {
            display: table-row-group;
        }

        .divBlock {
            display: inline-table;
            margin: 0 auto;
            width: 100%;
        }

        .divBlock .divTable:last-of-type {
            border-bottom: none !important;
        }

    </style>
</head>

<body>
    <div class="invoiceBox">
        <div class="divBlock">
            <div class="divTable">
                <div class="divTableBody">
                    <div class="divTableRow">
                        <div class="divTableHead divTableHeading"
                            style="border-top: none;text-align: center;font-size: 27px; color: #101010;font-weight: 600;padding-bottom: 25px;padding-top: 25px;">
                            {{ $labels['EVENTSITE_HOTEL_HOTEL_MANAGEMENT'] }}</div>
                    </div>
                </div>
            </div>
            <div style="clear: both;"></div>
            <div class="divTable">
                <div class="divTableBody">
                    <div class="divTableRow">
                        <div class="divTableHead divTableHeading"
                            style="text-align: left;font-size: 22px;font-weight: 600;color: #000;padding-left: 0;padding-bottom: 20px;">
                            {{ $labels['EVENTSITE_HOTEL_RESERVATION_DETAILS'] }}
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($order_detail['hotel'] as $hotel)
                <?php
                $flag = false;
                $persons = $hotel['persons'];
                foreach ($persons as $person) {
                    if ($person['attendee_id'] == $attendee_id) {
                        $flag = true;
                    }
                }
                ?>
                @if ($flag)
                    <div style="clear: both;"></div>
                    <div class="divTable" style="margin-bottom: 25px;">
                        <div class="divTableBody">
                            <div class="divTableRow">
                                <div class="divTableHead divTableHeading"
                                    style="border-top: none; text-align: left;font-size: 20px;font-weight: 600;color: #000;padding: 3px;padding-left: 0;">
                                    {{ $hotel['name'] }}
                                </div>
                            </div>
                            <div class="divTableRow">
                                <div class="divTableCell"
                                    style="padding: 3px;text-align: left; padding-left: 0;font-size: 18px; color: #707070;border: none;">
                                    {{ $hotel['description'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both;"></div>
                    <div class="divTable" style="padding-bottom: 20px; border-bottom: 1px solid #707070;margin-bottom: 20px;">
                        <div class="divTableBody">
                            <div class="divTableRow">
                                <div class="divTableHead divTableHeading"
                                    style="border-top: none; text-align: left;font-size: 20px;font-weight: 600;color: #000;padding: 3px;padding-left: 0;">
                                    {{ $labels['EVENTSITE_HOTEL_ARRIVAL_DATE'] }}
                                </div>
                                <div class="divTableHead divTableHeading"
                                    style="border-top: none; text-align: left;font-size: 20px;font-weight: 600;color: #000;padding: 3px;padding-left: 0;">
                                    {{ $labels['EVENTSITE_HOTEL_DEPARTURE_DATE'] }}
                                </div>
                                <div class="divTableHead divTableHeading"
                                    style="border-top: none; text-align: left;font-size: 20px;font-weight: 600;color: #000;padding: 3px;padding-left: 0;">
                                    {{ $labels['EVENTSITE_HOTEL_TOTAL_NIGHTS'] }}
                                </div>
                            </div>
                            <div class="divTableRow">
                                <div class="divTableCell"
                                    style="padding: 3px;text-align: left; padding-left: 0;font-size: 18px; color: #707070;border: none;">
                                    {{ getFormatDate('%d.%m.%Y', $hotel['check_in']) }}
                                </div>
                                <div class="divTableCell"
                                    style="padding: 3px;text-align: left; padding-left: 0;font-size: 18px; color: #707070;border: none;">
                                    {{ getFormatDate('%d.%m.%Y', $hotel['check_out']) }}
                                </div>
                                <div class="divTableCell"
                                    style="padding: 3px;text-align: left; padding-left: 0;font-size: 18px; color: #707070;border: none;">
                                    <?php
                                    $num_of_days = \Carbon\Carbon::parse($hotel['check_in'])->diffInDays($hotel['check_out']);
                                    // if ($num_of_days < 2) { //0 or 1 $num_of_days=1; } 
                                    ?>
                                    {{ sprintf('%02d', $num_of_days) }} 
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</body>

</html>
