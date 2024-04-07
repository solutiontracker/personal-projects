@if($print)
		<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>{{$eventSetting['name']}}_{{$order_id}}</title>
	@endif
	<style>
		/* invoice header css*/
		.invoice-box {

			@if($print)
max-width: 900px;
			@endif
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
			@if($print)
max-width: 900px;
			@endif
line-height: 1.5;
			letter-spacing: normal;
			color: #636466;
			border-top: 2px solid {{$eventSetting['primary_color']}};
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
			color: {{ $eventSetting['primary_color'] }};
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
			padding-top: 15px;
			font-weight: bold;
			vertical-align: top;
			padding-bottom: 0;
			box-sizing: border-box;
			padding-left: 0;
			padding-right: 0;
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
			display: table;
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
		/* New Css */
		.divDetail h4 {
			font-size: 14px;
			color: #000;
			padding-bottom: 15px;
			border-bottom: 1px solid #BEBEBE;
			margin: 0 0 0px 0;
		}
		.ebs-fields-flex {
			font-size: 12px;
			padding-bottom: 0px;
		}
		.ebs-fields-flex strong {
			font-weight: 400;
			color: #000;
			width: 28%;
			display: inline-block;
			vertical-align: text-top;
			padding-top: 10px;
		}
		.ebs-fields-flex span {
			width: 70%;
			background: #FAFAFA;
			border-radius: 2px;
			padding: 8px 15px;
			box-sizing: border-box;
			font-weight: 400;
			display: inline-block;
			line-height: 18px;
			vertical-align: text-top;
		}
		/* .page-break-always  {
			page-break-after: always;
		} */


		.divBody .divDetail {
			width: 50%;
		}
		.ebsTableodd .divDetail:nth-child(even) {
			padding-left: 15px;
		}
		.ebsTableodd .divDetail:nth-child(odd) {
			padding-right: 15px;
		}
		.divNoBorder .divTableHead {
			border-top: none;
			font-size: 14px;
			color: #000;
			padding-top: 30px;
		}
		/*  */
		@media screen {
			.page {
				margin: 0 auto;
				width: 100%;
				display: table;
				background: #000;
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
	@if($print)
</head>
<body style="margin: 0px; padding: 0px;">
@endif
<div class="invoiceBox invoice-box">
	@if($order_detail['order']['is_payment_received'] == 1 && $print)
		<div class="watermarkPaid"></div>
	@endif
	@if(!$pdf)
		@include('admin.order.order_history.invoice.free.header', compact('eventSetting', 'pdf', 'order_detail', 'billing_fields'))
	@endif
	<div style="page-break-inside: avoid;" class="divBlock">
		<div class="divTable tableHeader">
			<div class="divTableBody">
				<div class="divTableRow">
					<div class="divTableCell divSummaryDetail" style="width: 30%;">

					</div>
					<div class="divTableCell divSummaryDetail" style="width: 40%;">
						<div class="divInvoiceHeading">
							@if($is_credit)
								<strong>{{ $labels['EVENTSITE_BILLING_CREDIT_HEADING'] }}</strong>
							@elseif($order_detail['order']['order_type'] != 'order')
								<strong>{{ $labels['EVENTSITE_BILLING_INVOICE_HEADING'] }}</strong>
							@endif
						</div>
						<div class="divCompanyTitle">
							<strong>{{ $order_detail['order_event_detail']['name'] }}</strong>
						</div>
						<div class="divSummaryTitle">
							<strong>{{ $labels['EVENTSITE_BILLING_SUMMARY'] }}</strong>
						</div>
					</div>
					<div class="divTableCell divSummaryDetail" style="width: 30%;text-align: right;">
						<div style="display: inline-block; vertical-align: middle; text-align: left; word-break: break-word;  ">
							@if($is_credit)
								{{ $labels['EVENTSITE_BILLING_CREDIT_ORDER_NUMBER']}}:
							@else
								{{$labels['EVENTSITE_BILLING_ORDER_NUMBER']}}:
							@endif
							@if(trim($payment_setting['eventsite_invoice_prefix']) != '')
								{{ $payment_setting['eventsite_invoice_prefix'].'-'.$order_detail['order']['order_number'] }}
							@else
								{{ $order_detail['order']['order_number'] }}
							@endif
							@if($is_credit)
								<br>
								{{ $labels['EVENTSITE_BILLING_REFERENCE'] }}:
								@if(trim($payment_setting['eventsite_invoice_prefix']) != '')
									{{ $payment_setting['eventsite_invoice_prefix'].'-'.$reference_credit_note_no }}
								@else
									{{ $reference_credit_note_no }}
								@endif
							@endif
							<br>
							@if($is_credit)
								{{ $labels['EVENTSITE_BILLING_CREDIT_ORDER_DATE'] }}: {{ getFormatDate(getDateFormat($date_format_id), $order_detail['order']['credit_note_create_date']) }} <br>
							@else
								{{ $labels['EVENTSITE_BILLING_ORDER_DATE']  }}: {{ getFormatDate(getDateFormat($date_format_id), $order_detail['order']['order_date']) }} <br>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
		@if(count((array)$order_detail['order_summary_detail']['group_addons']) > 0 || count((array)$order_detail['order_summary_detail']['single_addons']) > 0)
			<div class="divTable">
				<div class="divTableBody">
					<div class="divTableRow">
						<div class="divTableHead divTableHeading divTableDesc" style="border-top: none;">{{ $labels['EVENTSITE_BILLING_ITEMS'] }}</div>
						<div class="divTableHead divTableHeading divTableQty" style="border-top: none;">{{ $labels['EVENTSITE_BILLING_QTY'] }}</div>
					</div>
					@foreach($order_detail['order_summary_detail']['group_addons'] as $group)
						<div class="divTableRow">
							<div class="divTableCell divTableDesc">
								{{ $group['group_name'] }}
							</div>
							<div class="divTableCell"></div>
						</div>
						@foreach($group['addons'] as $addon)
							<div class="divTableRow divBgColor">
								<div class="divTableCell divTableDesc">
									{{ $addon['name'] }}
									@if($payment_setting['eventsite_enable_billing_item_desc'] && !empty($addon['description']))
										<br>{{ $addon['description'] }}
									@endif
								</div>
								<div class="divTableCell divTableQty">{{ $addon['qty'] }}</div>
							</div>
						@endforeach
					@endforeach
					@foreach($order_detail['order_summary_detail']['single_addons'] as $addon)
						<div class="divTableRow">
							<div class="divTableCell divTableDesc">
								{{ $addon['name'] }}
								@if($payment_setting['eventsite_enable_billing_item_desc'] && !empty($addon['description']))
									<br>{{ $addon['description'] }}
								@endif
							</div>
							<div class="divTableCell divTableQty">{{ $addon['qty'] }}</div>
						</div>
					@endforeach
				</div>
			</div>
			<div style="clear:both;"></div>
		@endif
		@if($payment_setting['eventsite_billing_detail'])
			@foreach($order_detail['attendee_summary_detail'] as $index => $attendee_summary)
				@php 
				$info = readArrayKey($attendee_summary['attendee_info'], array(), 'info'); 
				$attendee_detail = array_merge($info, $attendee_summary['attendee_info']);
				@endphp
				<div class="divTable divBlock">
					<div class="divTableBody">
						<!--  -->
						<div class="divBlock">
						<div class="divTable divBlock">
							<div class="divTableBody">
								<!--  -->
								<div class="divTableRow">
									<div class="divTableHead divDetail">
										<h4>{{$attendee_summary['attendee_info']['first_name'].' '.$attendee_summary['attendee_info']['last_name']}}</h4>
									</div>
								</div>
								<!--  -->
							</div>
						</div>
					</div>
						<div  style="display:block;font-size:0;padding: 12px 0;" class="divTableRow ebsTableodd">
							@foreach ($attendee_summary['sections'] as $key => $section)
								@foreach ($section['fields'] as $k => $field)
									@if(!in_array($field['field_alias'], ['password', 'confirm_password', 'member_number']))
										@if(in_array($field['field_alias'], ["custom_field_id"]))
											@if(count((array)$attendee_summary['custom_fields']) > 0)
												@foreach((array)$attendee_summary['custom_fields'] as $custom_field)
													<div style="display: inline-block;width:50%;verticle-align:top" class="divTableHead divDetail">
														<div class="ebs-fields-flex">
															<strong>{{ $custom_field['name']}}</strong>
															<span class="ebs-content">
																@if(count((array)$custom_field['answers']) > 0)
																	@foreach((array)$custom_field['answers'] as $key => $answer)
																		{{($key+1) == count((array)$custom_field['answers']) ? $answer['label'] : $answer['label'].','}}
																	@endforeach
																@endif
															</span>
														</div>
													</div>
												@endforeach
											@endif
										@else 
											<div style="display: inline-block;width:50%;verticle-align:top" class="divTableHead divDetail">
												<div class="ebs-fields-flex">
													<strong>{{ $field['detail']['name']}}</strong>
													<span class="ebs-content">
														@if(in_array($field['field_alias'], ["private_country", "company_country", "country"]))
															@if($attendee_detail[$field['field_alias']])
																{{getCountryName($attendee_detail[$field['field_alias']])}}
															@endif
														@elseif(in_array($field['field_alias'], ["delegate"]) && $attendee_detail['delegate_number'])
															{{$attendee_detail['delegate_number']}}
														@elseif(isset($attendee_detail[$field['field_alias']]) && $attendee_detail[$field['field_alias']])
															{{ $attendee_detail[$field['field_alias']] }}
														@else 
															&nbsp;
														@endif
													</span>
												</div>
											</div>
										@endif
									@endif
								@endforeach
							@endforeach
						</div>
						<!--  -->
						<div style="clear: both;"></div>
					</div>
				</div>
				@if(count($attendee_summary['addons']['group_addons']) > 0)
					<div style="page-break-inside: avoid;" class="divTable divBlock">
						<div class="divTableBody">
								<div class="divTableRow">
									<div class="divTableHead divTableHeading divTableDesc" style="border-top: none;">{{ $labels['EVENTSITE_BILLING_ITEMS'] }}</div>
									<div class="divTableHead divTableHeading divTableQty" style="border-top: none;">{{ $labels['EVENTSITE_BILLING_QTY'] }}</div>
								</div>
								@foreach($attendee_summary['addons']['group_addons'] as $group_addon)
									<div class="divTableRow">
										<div class="divTableCell divTableDesc">
											{{ $group['group_name'] }}
										</div>
										<div class="divTableCell divTableQty"></div>
									</div>
									@foreach($group_addon['addons'] as $addon)
										<div class="divTableRow divBgColor">
											<div class="divTableCell divTableDesc">
												{{ $addon['name'] }}
												@if($payment_setting['eventsite_enable_billing_item_desc'] && !empty($addon['description']))
													<br>{{ $addon['description'] }}
												@endif
											</div>
											<div class="divTableCell divTableQty">{{ $addon['qty'] }}</div>
										</div>
									@endforeach
								@endforeach
								@foreach($attendee_summary['addons']['single_addons'] as $addon)
									<div class="divTableRow">
										<div class="divTableCell divTableDesc">
											{{ $addon['name'] }}
											@if($payment_setting['eventsite_enable_billing_item_desc'] && !empty($addon['description']))
												<br>{{ $addon['description'] }}
											@endif
										</div>
										<div class="divTableCell divTableQty">{{ $addon['qty'] }}</div>
									</div>
								@endforeach
							</div>
					</div>
				@endif
			@endforeach
		@endif
	</div>
	<div style="page-break-inside: avoid;" class="divBlock">
		@if($order_detail['is_hotel_attached'] && count($order_detail['hotel']) > 0)
			<div style="clear:both;"></div>
			<div class="divBlock">
				<div class="divTable">
					<div class="divTableBody">
						<div class="divTableRow divNoBorder">
							<div class="divTableHead divTableHeading divTableDesc">{{ $labels['EVENTSITE_HOTEL_HOTEL_MANAGEMENT'] }}</div>
							<div class="divTableHead divTableHeading divTableQty">{{ $labels['EVENTSITE_HOTEL_ROOMS'] }}</div>
							@if($payment_setting['show_hotel_prices'] == '1')
								<div class="divTableHead divTableHeading">{{ $labels['EVENTSITE_HOTEL_PRICE'] }}</div>
								<div class="divTableHead divTableHeading">{{ $labels['EVENTSITE_HOTEL_SUBTOTAL'] }}<br>{{ $labels['EVENTSITE_HOTEL_EXCL_VAT'] }}</div>
							@endif
						</div>
						@foreach($order_detail['hotel'] as $hotel)
							<div class="divTableRow">
								<div class="divTableCell divTableDesc">
									{{ $hotel['name'] }}<br>
									{{ getFormatDate(getDateFormat($date_format_id),$hotel['check_in']) }} - {{ getFormatDate(getDateFormat($date_format_id), $hotel['check_out']) }}
									@if($hotel['description'])
										<br>{{ $hotel['description'] }}
									@endif
								</div>
								<div class="divTableCell">{{ $hotel['rooms'] }}</div>
								@if($payment_setting['show_hotel_prices'] == '1')
									<div class="divTableCell">{{ getCurrency($hotel['price'], $currency) }}</div>
									<div class="divTableCell">{{ getCurrency($hotel['sub_total'], $currency) }}</div>
								@endif
							</div>
						@endforeach
					</div>
				</div>
				@if($order_detail['is_hotel_attached'] && $payment_setting['show_hotel_prices'] == '1')
					<div class="divTable">
						<div class="divTableBody">
							<div class="divTableRow">
								<div class="divTableCell divTotalValue divTotalValueLabel">
									{{ $labels['EVENTSITE_BILLING_TOTAL_LABEL'] }}
								</div>
								<div class="divTableCell divTotalValue">
									{{ getCurrency($order_detail['hotel_sub_total'], $currency).' '.$currency }}
								</div>
							</div>
						</div>
					</div>
				@endif
			</div>
		@endif
		<div class="divBlock">
			@if($order_detail['order']['grand_total'] > 0)
				<div class="divTable">
					<div class="divTableBody">
						<div class="divTableRow">
							<div class="divTableCell divTotalValue divTotalValueLabel">
								{{ $labels['EVENTSITE_BILLING_SUBTOTAL'] }}
							</div>
							<div class="divTableCell divTotalValue">
								{{ getCurrency($order_detail['order']['grand_total'] - $order_detail['total_vat_amount'], $currency).' '.$currency }}
							</div>
						</div>
						@if($order_detail['is_vat_applied'])
							@if($payment_setting['hotel_vat_status'] && $eventsite_setting['payment_type'] == 0)
								<div class="divTableRow">
									<div class="divTableCell divTotalValue divTotalValueLabel">
										{{ $labels['EVENTSITE_BILLING_VAT'] }} @if(isset($order_detail['hotel'][0]['vat_rate'])) ({{ $order_detail['hotel'][0]['vat_rate'] }}%) @endif
									</div>
									<div class="divTableCell divTotalValue">
										{{ getCurrency($order_detail['total_vat_amount'], $currency).' '.$currency }}
									</div>
								</div>
							@else
								@if($order_detail['order']['item_level_vat'] == 0 && $order_detail['order']['vat'] > 0)
									<div class="divTableRow">
										<div class="divTableCell divTotalValue divTotalValueLabel">
											{{ $labels['EVENTSITE_BILLING_VAT'] }} ({{ $order_detail['order']['vat'] }}%)
										</div>
										<div class="divTableCell divTotalValue">
											{{ getCurrency($order_detail['total_vat_amount'], $currency).' '.$currency }}
										</div>
									</div>
								@else
									@foreach($order_detail['vat_detail'] as $vat => $vat_amount)
										<div class="divTableRow">
											<div class="divTableCell divTotalValue divTotalValueLabel">
												{{ $labels['EVENTSITE_BILLING_VAT'] }} ({{ $vat }}%)
											</div>
											<div class="divTableCell divTotalValue">
												{{ getCurrency($vat_amount, $currency).' '.$currency }}
											</div>
										</div>
									@endforeach
								@endif
							@endif
						@endif
						<div class="divTableRow">
							<div class="divTableCell divTotalValue divTotalIncVat divTotalValueLabel">
								{{ $order_detail['is_vat_applied'] ? $labels['EVENTSITE_BILLING_PAYMENT_TOTAL'] : $labels['EVENTSITE_BILLING_PAYMENT_EXC_TOTAL'] }}
							</div>
							<div class="divTableCell divTotalValue divTotalIncVat">
								{{ getCurrency($order_detail['order']['grand_total'],$currency).' '.$currency }}
							</div>
						</div>
					</div>
				</div>
			@endif
			<div style="clear:both;"></div>
			@if(strlen(trim(stripslashes($payment_setting['payment_terms']))) > 0 && ($order_detail['order']['order_type'] == 'invoice' || request()->order_type == "invoice") && !$is_credit)
				<div class="divTable">
					<div class="divTableBody">
						<div class="divTableRow">
							<div class="divTableCell divPaymentParam">
								{!! $payment_setting['payment_terms'] !!}
							</div>
						</div>
						@if($order_detail['order']['invoice_reference_no'])
							<div class="divTableRow">
								<div class="divTableCell divPaymentParam">
									{!! $order_detail['order']['invoice_reference_no'] !!}
								</div>
							</div>
						@endif
					</div>
				</div>
			@endif
		</div>
		<div style="clear: both;"></div>
		@if($order_detail['is_hotel_attached'] && $payment_setting['hotel_person'] == 1 && count((array)$order_detail['hotel']) > 0)
			<div class="divTable divBlock">
				<div class="divTableBody">
					<div class="divTableRow">
						<div class="divTableCell divTotalValue divTotalValueLabel" style="width: 100%; padding-bottom: 0;">
							<strong class="personHeading" style="color: #636466;">{{ ucfirst($labels['EVENTSITE_HOTEL_HOTEL_MANAGEMENT']) }}</strong>
						</div>
					</div>
					@foreach($order_detail['hotel'] as $hotel)
						@if(count($hotel['persons']) > 0)
							<div class="divTableRow">
								<div class="divTableHead divDetail" style="padding: 0 0 0 7px; font-size: 12px;">
									{{ $hotel['name'].' ('.getFormatDate(getDateFormat($date_format_id),$hotel['check_in']) }} - {{ getFormatDate(getDateFormat($date_format_id), $hotel['check_out']).')' }}
								</div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell divTotalValue divTotalValueLabel" style="width: 100%;border: none;padding-top: 4px;">
									<div class="name-person">
										<div class="namerow">
											<span class="person-title">@if(!empty($labels['EVENTSITE_HOTEL_NUMBER_OF_ROOMS'])){{  ucfirst($labels['EVENTSITE_HOTEL_NUMBER_OF_ROOMS']) }}: @else {{ 'Number of rooms:' }} @endif </span>
											<span class="person-name">{{ count($hotel['persons']) }}</span>
										</div>
										<div class="namerow">
											<span class="person-title">{{  ucfirst($labels['EVENTSITE_HOTEL_PERSON_NAME']) }}:</span>
											@foreach($hotel['persons'] as $index => $person)
												<?php
												$person_name = '';
												if($person->attendee_id != 0) {
													$att = $person->attendee_detail()->first();
													$person_name = ucfirst(trim($att->first_name.' '.$att->last_name));
												}
												?>
												@if($index == 0)
													<span class="person-name">{{ $person_name }}</span>
												@elseif(!empty($person_name))
													<br><span class="person-title"></span>
													<span class="person-name">{{ $person_name }}</span>
												@endif
											@endforeach
										</div>
									</div>
								</div>
							</div>
						@endif
					@endforeach
				</div>
			</div>
		@endif
		@if(!$pdf)
			@include('admin.order.order_history.invoice.footer', compact('eventSetting', 'pdf', 'payment_setting'))
		@endif
		@if(!$pdf && count($history_logs) > 0)
			<br>
			<table class="table table-bordered table-striped" style="border-right:1px solid #e6e7e8 !important;">
				<thead>
				<tr>
					<th style="border-right:none;" colspan="2">Changing History</th>
				</tr>
				</thead>
				<tbody>
				@foreach($history_logs as $history_log)
					<tr>
						<td colspan="2" style="padding-left:1%;font-size:12px; color:#434343; text-align: left; border-right:none;"><strong>{{ getFormatDate(getDateFormat($date_format_id), $history_log['update_date']) }}</strong></td>
					</tr>
					@if($is_credit == 1)
						<?php $inner_history = \DB::select(\DB::raw("SELECT * FROM conf_billing_order_log_credit_notes WHERE order_id = " . $order_id . " AND credit_note_id = " . $history_log['credit_note_id'] . " AND `update_date` = '" . $history_log['update_date'] . "'")); ?>
					@else
						<?php $inner_history = \DB::select(\DB::raw("SELECT * FROM conf_billing_order_log WHERE order_id = " . $order_id . " AND `update_date` = '" . $history_log['update_date'] . "'")); ?>
					@endif
					<?php $inner_history = object_to_array($inner_history); ?>
					@foreach($inner_history as $inner_his)
						<tr>
							<td style="padding-left:1%;font-size:12px; color:#434343; text-align: left; border-right:none;">{{ date('H:i:s', strtotime($inner_his['update_date_time'])) }}</td>
							<td style="padding-left:1%;font-size:12px; color:#434343; text-align: left; border-right:none;"><b>{{ ucfirst(str_replace('_', ' ', $inner_his['field_name'])).': ' }} </b>{{ $inner_his['data_log'] }} </td>
						</tr>
					@endforeach
				@endforeach
				</tbody>
			</table>
		@endif
	</div>
@if($print)
</body>
</html>
@endif