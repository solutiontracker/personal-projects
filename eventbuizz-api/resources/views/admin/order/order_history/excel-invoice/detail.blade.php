<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* invoice header css*/
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
        /* invoice footer css */
        .tfooter-text {
            padding: 10px 8px;
            max-width: 900px;
            line-height: 1.5;
            letter-spacing: normal;
            color: #636466;
            border-top: 2px solid #C42030;
            font-size: 11px;
        }
        /* invoice body css*/

        .divTable{
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
        .divTableCell, .divTableHead{
            border-top: 1px solid #bebebe;
            display: table-cell;
            padding: 12px;
            text-align: right;
            width: 13%;
        }
        .divTableDesc {
            width: 47% !important;
            text-align: left;
        }
        .divTableQty  {
            width: 3% !important;
        }
        .divTableCell.divTotalValue {
            width:50%;
            font-size: 15px;
            color: #636466;
            font-weight: bold;
            padding: 13px 8px;
            border-top: 1px solid #bebebe;
        }
        .divTableCell.divTotalValue.divTotalIncVat{
            font-size: 18px;
            color: #101010;
            font-weight: 700;
        }
        .divTableCell.divPerson {
            width:33.3%;
            font-size: 15px;
            color: #636466;
            font-weight: bold;
            padding: 13px 8px;
            border-top: 1px solid #bebebe;
        }
        .divTableCell.divSummaryDetail {
            font-size: 14px;
            color: #636466;
            padding-bottom: 5px;
            padding-top: 20px;
            text-align: left;
            vertical-align: top;
        }
        .divTableCell.divPaymentParam {
            width:100%;
            font-size: 15px;
            text-align: left;
            color: #636466;
            padding-top: 10px;
            padding-bottom: 10px;
            border-top: 2px solid #bebebe;
        }
        .divTotalValueLabel {
            text-align: left;
        }

        .divTableCell.divTotalAmount {
            width:50%;
            font-size: 15px;
            color: #636466;
            font-weight: bold;
            padding: 13px 8px;
            border-bottom: 2px solid #bebebe;
        }
        .divInvoiceHeading {
            font-size: 32px;
            color:  #C42030;
            line-height: 1.2;
            text-align: center;
            letter-spacing: 10px;
            text-transform: uppercase;
            padding-top: 0px;
        }
        .divCompanyTitle {
            text-align: center;
            font-size: 18px;
            color: #010101;
            line-height: 1.09;
            padding-bottom: 12px;
            padding-top: 19px;
        }
        .divSummaryTitle {
            text-align: center;
            font-size: 24px;
            text-transform: uppercase;
            color: #636466;
            line-height: 1.09;
            padding-bottom: 20px;
            padding-top: 10px;
        }
        .divBgColor {
            background: rgba(0,0,0,0.03);
            color: #636466;
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .divDetail {
            border-top: none;
            text-align: left;
            font-size: 18px;
            color: #636466;
            line-height: 1.09;
            padding-top: 25px;
            font-weight: bold;
        }
        .divDetailTitle {
            font-size: 16px;
            color: #636466;
            font-weight: bold;
        }
        .divTableFoot {
            background-color: #EEE;
            display: table-footer-group;
            font-weight: bold;
        }
        .divTableBody {
            display: table-row-group;
        }
        .divBlock {
            display: inline-table;
            margin: 0 auto;
            width: 100%;
        }
         .name-person {
            display: inline-block;
             color: #636466;
             vertical-align: text-top;
             /*margin-bottom: 15px;*/
             font-size: 13px;
        }
         .name-person span.person-title {
             display: inline-block;
             width: 125px;
             font-weight: 400;
         }
         .name-person span.person-name {
             font-weight: 700;
         }
        .watermarkPaid {
            position: fixed;
            top: 0;
            height: 99999em;
            width: 900px;
            margin: auto;
            background: url("{{cdn('_admin_assets/images/invoice_watermarks/".$language_id.".png') }}") no-repeat left top;
            z-index: -1;
        }
        @media screen {
           
            .page {
                page-break-before: always !important;
                display: block;
                margin: 0 auto;
                width: 100%;
            }
        }
        @media screen and (max-width: 767px) {
            div#center.container.box-padding.inner-contents.minHightOne {
                padding: 0 !important;
            }
            .invoice-box table.top,
            .invoice-box table.top tbody,
            .invoice-box table.top tr,
            .invoice-box table.top tr td,
            .divTable.tableHeader,
            .divTable.tableHeader .divTableBody,
            .divTable.tableHeader .divTableRow, 
            .divTable.tableHeader .divTableCell{
                display: block;
                width: 100% 
            }
            .invoice-box table tr td:last-child {
                text-align: left;
            }
            .invoice-box table.top tr td  {
                padding-bottom: 15px;    
            }
            .divTable.tableHeader .divTableCell.divSummaryDetail {
                width: 100% !important;
                text-align: center !important;
                padding: 25px 0 !important;
            }
            .divTable .divTableHeading {
                display: none !important;
            }
            .divTable .divTableCell.divTableDesc.divDetailTitle {
                display: block;
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="margin: 0px; padding: 0px;">
<div class="invoiceBox invoice-box">
    <div class="divBlock">
    <div class="divTable tableHeader">
        <div class="divTableBody">
            <div class="divTableRow">
                <div class="divTableCell divSummaryDetail" style="width: 30%;"></div>
                <div class="divTableCell divSummaryDetail" style="width: 40%;">
                    <div class="divInvoiceHeading">
                        <strong>{{ $labels['EVENTSITE_BILLING_INVOICE_HEADING'] }}</strong>
                    </div>
                    <div class="divCompanyTitle">
                        <strong>Danske Fysioterapeuters Fagkongres 2022</strong>
                    </div>
                    <div class="divSummaryTitle">
                        <strong>{{ $labels['EVENTSITE_BILLING_SUMMARY'] }}</strong>
                    </div>
                </div>
                <div class="divTableCell divSummaryDetail" style="width: 30%;text-align: right;">
                    <div style="display: inline-block; vertical-align: middle; text-align: left; word-break: break-word;  ">
                        {{ $labels['EVENTSITE_BILLING_INVOICE_NUMBER']  }}:
                        {{ '3-'.$order['order_number'] }}
                         <br>
                        {{ $labels['EVENTSITE_BILLING_INVOICE_DATE'] }}: {{ getFormatDate(getDateFormat(2), $order['order_date']) }} <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="clear: both;"></div>
    <div class="divTable">
        <div class="divTableBody">
            <div class="divTableRow">
                <div class="divTableHead divTableHeading divTableDesc" style="border-top: none;">Elementer</div>
                <div class="divTableHead divTableHeading divTableQty" style="border-top: none;">Antal</div>
                <div class="divTableHead divTableHeading" style="border-top: none;">Pris</div>
                <div class="divTableHead divTableHeading" style="border-top: none;">Heraf moms</div>
                <div class="divTableHead divTableHeading" style="border-top: none;">Total<br>(lnkl. Moms)</div>
            </div>
            <?php $total_vat_amount = 0 ; ?>
            @foreach($items as $key => $addon)
                <div class="divTableRow">
                    <div class="divTableCell divTableDesc">
                        {{ $key }}
                    </div>
                    <div class="divTableCell divTableQty">{{ $addon[0]['qty'] * count($addon) }}</div>
                    <div class="divTableCell">{{ getCurrency(str_replace(',', '', $addon[0]['unit_price']),  $order['eventsite_currency'])}}</div>
                    <div class="divTableCell">{{ getCurrency(str_replace(',', '', count($addon) * $addon[0]['vat_amount']),  $order['eventsite_currency'])}}</div>
                    <div class="divTableCell">{{ getCurrency((str_replace(',', '', $addon[0]['total']) * count($addon)),  $order['eventsite_currency'])}}</div>
                </div>
                <?php $total_vat_amount += str_replace(',', '', count($addon) * $addon[0]['vat_amount']); ?>
            @endforeach
            <div class="divTableRow">
                <div class="divTableCell divTableDesc">Hermaf moms (25%) </div>
                <div class="divTableCell divTableDesc"></div>
                <div class="divTableCell divTableDesc"></div>
                <div class="divTableCell divTableDesc">{{ getCurrency($total_vat_amount, $order['eventsite_currency']) }}</div>
                <div class="divTableCell divTableDesc"></div>
            </div>
        </div>
    </div>
    </div>
    <div class="divBlock">
        <div style="clear:both;"></div>
        <div class="divTable">
            <div class="divTableBody">
                <div class="divTableRow">
                    <div class="divTableCell divTotalValue divTotalIncVat divTotalValueLabel">
                        {{ $labels['EVENTSITE_BILLING_PAYMENT_TOTAL'] }}
                    </div>
                    <div class="divTableCell divTotalValue divTotalIncVat">
                    {{ getCurrency($order['grand_total'], $order['eventsite_currency']).' '.$order['eventsite_currency'] }}
                    </div>
                </div>
            </div>
        </div>
        <div style="clear:both;"></div>
        @if(strlen(trim(stripslashes($payment_setting['payment_terms']))) > 0 && $order_detail['order']['order_type'] == 'invoice')
            <div class="divTable">
                <div class="divTableBody">
                    <div class="divTableRow">
                        <div class="divTableCell divPaymentParam">
                            {{ stripslashes(($payment_setting['payment_terms'])) }}
                        </div>
                    </div>
                    @if($order_detail['order']['invoice_reference_no'])
                        <div class="divTableRow">
                            <div class="divTableCell divPaymentParam">
                                {{ $order_detail['order']['invoice_reference_no'] }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
</body>
</html>