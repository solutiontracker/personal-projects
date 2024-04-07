import React, { ReactElement, FC, useContext } from 'react';
import Event from '@/src/app/components/event/interface/Event';
import { EventContext } from "@/src//app/context/event/EventProvider";

type Props = {
	event?: Event;
}


const Theme: FC<Props> = (): ReactElement => {
	const { event } = useContext<any>(EventContext);
	const _styles = `
	:root {
		--lightgray: #E4E4E4;
		--white: #fff;
		--black: #000;
		--link: ${event.settings ? event.settings.primary_color : '#0FA1C1'};
		--gray: #6D6D6D;
		--border: #bbb;
		--darkcolor: ${event.settings ? event.settings.primary_color : '#0FA1C1'};
		--grayf5: #F5F5F5;
		--hover: ${event.settings ? event.settings.secondary_color : '#85BB24'};
		--wrapper: ${event.registration_flow_theme ? event.registration_flow_theme.wrapper_color : '#fff'};
		--textcolor: ${event.registration_flow_theme && event.registration_flow_theme.mode === 'light' ? '#6D6D6D' : '#B9B9B9'}
	}
	#loader {
		border-top-color: var(--darkcolor);
	}
	#loader::before {
		border-top-color: var(--hover);
	}
	.ebs-icon-solid,
	.ebs-icon-line  {
		padding-left: 5px;
		cursor: pointer;
	}
	.ebs-icon-line svg path,
	#Ellipse_107  {
		stroke: var(--link);
	}
	.ebs-nav-social .ebs-social-dropdown a .ebs-icon svg path,
	.ebs-icon-solid svg path,
	.ebs-icon-line svg text,
	.ebs-btn-cookie svg path {
		fill: var(--link);
	}
	.ebs-bottom-description-box {
		border-top-color: var(--lightgray);
	}
	.ebs-nav-social .ebs-social-dropdown a:hover .ebs-icon svg path {
		fill: var(--hover);
	}
	.ebs-collaspe-item h4 {
		color: var(--black);
	}
	.error-message {
		color: ${event.registration_flow_theme && event.registration_flow_theme.mode === 'light' ? '#000' : 'rgba(255,255,255,0.7)'} !important;
	}
	.success-message {
		color: ${event.registration_flow_theme && event.registration_flow_theme.mode === 'light' ? '#000' : 'rgba(255,255,255,0.7)'} !important;
	}
	body {
		color: var(--black);
		background: ${event.registration_flow_theme ? event.registration_flow_theme.body_color : '#E4E4E4'};
	}
	.footer h5.link, .footer, .footer h4, .footer h3,.footer #collaspe-item p.icon i, .footer a,
	.footer #collaspe-item address  {
		color: #fff;
	}
	.footer .wrapper-box {
		background: ${event.registration_flow_theme && event.registration_flow_theme.mode === 'light' && '#333'};
	}
	.ebs-popup-container .ebs-popup-wrapper {
		background: var(--wrapper);
	}
	.rdtPicker .rdtTimeToggle {
		background: var(--darkcolor);
		color: var(--white);
		padding-top: 3px;
		padding-bottom: 3px;
		border-radius: 3px;
		display: none;
	}
	.thumb-horizontal {
		border-radius: 30px;
  		background: var(--link) !important;
	}
	.rdtPicker .rdtTimeToggle:hover {
		background: var(--link);
	}
	.rdtPicker td.rdtSwitch {
		cursor: pointer;
		display: none;
	}
	.rdtPicker td.rdtSwitch:hover {
		background: #eee;
	}
	.ebs-date-wrapper .ebs-top-caption label span {
		color: #6b6b6b;
	}
	.network-cateogry-list.ebs-cateogry-filter {
		border-bottom: 1px solid var(--border);
	}
	.network-cateogry-list.ebs-cateogry-filter ul li label span {
		border: 1px solid var(--border);
		color: var(--textcolor);
	}
	.ebs-popup-container .ebs-popup-content {
		color: var(--textcolor);
	}
	.network-cateogry-list.ebs-cateogry-filter ul li label input:checked + span {
		background: var(--border);
	}
	.ebs-date-wrapper .ebs-top-caption label:hover input[type="radio"] ~ span,
	.ebs-date-wrapper .ebs-top-caption label input[type="radio"]:checked ~ span {
		border-bottom-color: #428bca;
		color: #428bca;
	}
	@media (max-width: 768px) { 
		.footer .row {
			margin: 0;
		}
		.footer .container .col {
			padding: 0;
		}
	}
	.link,.ebs-add-more .ebs-add,
	a,.ebs-register-rows .ebs-remove-attendee,
	.ico-tooltip-info,
	.ebs-cookie-container .btn-close {
		color: var(--link);
	}
	a:hover,.ebs-add-more .ebs-add:hover {
		color: var(--hover);
	}
	.btn {
	background: var(--link);
	color: var(--white);
		border-color: var(--link);
	}
	.btn.bordered {
		background: transparent;
		border: 1px solid var(--link);
		color: var(--link);

	}
	.btn:hover {
	background: var(--hover);
	color: var(--white);
		border-color: var(--hover);
	}
	.main-section.registration-success {
		background: ${event.registration_flow_theme && event.registration_flow_theme.mode === 'light' ? '#fff' : 'transparent'};
	}

	h1,
	h2,
	h3,
	h4,
	h5,
	h5,
	.data-wrapper-table .data-row .col-2,
	.generic-form p,
	.matrix-question-wrapper,
	.MuiInputBase-input,.checkout-sidebar {
		color: var(--lightdark);
	}
	.data-wrapper-table .data-row .col-2 .qty-item,
	.data-wrapper-table .data-row .description-box p,
	.ebs-back-summary,.ebs-attendee-caption {
		color: var(--textcolor);
	}
	.header {
		background: var(--white);
		box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
	}
	.header-section p {
		color: var(--textcolor);
	}
	#loader-wrapper.fixed {
		background: ${event.registration_flow_theme && event.registration_flow_theme.mode === 'light' ? 'rgba(255,255,255, 0.8)' : 'rgba(0,0,0, 0.8)'};
	}
	.header .btn-share {
		color: var(--textcolor);
	}

	.header .btn-menu {
		color: var(--textcolor);
	}

	#collaspe-item address {
		color: var(--textcolor);
	}

	#collaspe-item address a,
	.ebs-cookie-policy,
	.ebs-cookie-policy h1, .ebs-cookie-policy h2, .ebs-cookie-policy h3, .ebs-cookie-policy h4, .ebs-cookie-policy h5,.ebs-cookie-policy .table
	 {
		color: var(--textcolor);
	}

	#collaspe-item p.icon i {
		color: var(--textcolor);
	}

	.wrapper-box {
		background: var(--wrapper);
	}

	.wrapper-box .wrapper-inner-content .header-box .required-field {
		color: var(--textcolor);
	}
	.label-input {
		background: var(--white);
	}
	.ebs-seperator {
		background: ${event.registration_flow_theme && event.registration_flow_theme.mode === 'light' ? '#E4E4E4' : '#B9B9B9'};
	}
	@media (max-width: 768px) {
		.wrapper-box.tab-collapse {
			background: var(--darkcolor);
		}

		.wrapper-box.tab-collapse h3 {
			color: var(--white);
		}
	}

	.wrapper-select {
		color: var(--gray);
	}

	.wrapper-select .label-wrapper-select {
		border: 1px solid var(--border);
		background: var(--white);
	}

	.wrapper-select .icon-right {
		color: var(--border);
	}

	.wrapper-select .btn-wrapper {
		background: var(--white);
		color: var(--gray);
	}

	.wrapper-select .css-2b097c-container {
		background: var(--white);
		box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
	}

	.wrapper-select .css-2b097c-container .css-26l3qy-menu .css-9gakcf-option {
		background-color: var(--lightgray) !important;
		color: #000;
	}

	.wrapper-select .css-2b097c-container .css-26l3qy-menu .css-9gakcf-option:hover {
		background-color: var(--lightgray) !important;
	}

	.wrapper-select .css-2b097c-container .css-26l3qy-menu div[id*="react-select-3-option"] {
		font-size: 13px;
	}

	.wrapper-select .css-2b097c-container .css-26l3qy-menu div[id*="react-select-3-option"]:hover {
		background-color: var(--lightgray) !important;
	}

	.wrapper-select .css-2b097c-container .css-26l3qy-menu .css-1n7v3ny-option {
		background-color: var(--lightgray) !important;
		color: var(--gray);
	}

	.wrapper-select.no-label .btn-wrapper {
		color: var(--black);
	}

	.wrapper-select.isSelected:not(.no-label) .btn-wrapper {
		color: var(--black);
	}

	.wrapper-select.isOpen .label-wrapper-select {
		border-color: var(--darkcolor);
		box-shadow: 0 0 4px var(--darkcolor);
	}

	.wrapper-select.isDisabled .label-wrapper-select {
		border-color: var(--border);
	}

	.wrapper-select.isDisabled .label-wrapper-select .icon-right,
	.ebs-clear-voucher {
		color: var(--darkcolor);
	}

	.css-c8odyh-control,
	.css-1l3nnzh-control {
		border-bottom: 1px solid var(--border) !important;
	}

	.label-input {
		border: 1px solid var(--border);
	}

	.label-input span {
		color: var(--gray);
	}

	.label-input input {
		color: var(--black);
	}

	.label-textarea {
		border: 1px solid var(--border);
		background: var(--white);
	}

	.label-textarea span {
		color: var(--gray);
	}

	.label-textarea textarea {
		color: var(--black);
	}

	.section-add-attendee p {
		color: var(--gray);
	}

	.radio-check-field.inline {
		border: 1px solid var(--border);
		background: var(--white);
	}

	.radio-check-field.inline h5 {
		color: var(--gray);
	}
	.radio-check-field label {
		color: var(--lightdark);
	}
	.radio-check-field.inline label {
		color: #000;
	}
	.radio-check-field label span:before {
		background: var(--white);
		border: 1px solid var(--border);
		color: var(--border);
	}

	.radio-check-field label.checked span:before {
		background: var(--link);
		color: var(--white);
		border-color: var(--link);
	}

	.form-phone-field {
		border: 1px solid var(--border);
		background: var(--white);
	}

	.form-phone-field::before {
		background: var(--border);
	}

	.change-address .address-field {
		color: var(--gray);
	}

	.field-terms-services label {
		color: var(--textcolor);
	}

	.field-terms-services label mark {
		color: var(--lightdark);
	}

	.bottom-button .btn.btn-save-next:disabled {
		background: var(--gray);
		border-color: var(--gray);
	}

	.bottom-button .btn.btn-save-addmore, .bottom-button .btn.btn-cancel {
		background: var(--white);
		border: 1px solid var(--darkcolor);
		color: var(--darkcolor);
		line-height: 24px;
	}

	.bottom-button .btn.btn-save-addmore:hover, .bottom-button .btn.btn-cancel:hover {
		background: var(--hover);
		color: var(--white);
		border-color: var(--hover);
	}

	.bottom-button.bottom-button-panel {
		border-top: 1px solid var(--lightgray);
	}

	.data-wrapper-table .data-row {
		border-top: 1px solid ${event.registration_flow_theme && event.registration_flow_theme.mode === 'light' ? '#E4E4E4' : '#B9B9B9'};
	}

	.data-wrapper-table .data-row.footer-table {
		border-top: 1px solid ${event.registration_flow_theme && event.registration_flow_theme.mode === 'light' ? '#E4E4E4' : '#B9B9B9'};
	}

	.data-wrapper-table .data-row .description-box .btn_checbox {
		border: 1px solid var(--gray);
		background: var(--white);
	}

	.data-wrapper-table .data-row .description-box .btn_checbox.checked {
		background: var(--link);
		border-color: var(--link);
		color: var(--white);
	}

	.data-wrapper-table .data-row .description-box .btn_checbox.sold-out {
		background: var(--gray);
		border-color: var(--gray);
		color: var(--white);
	}

	.data-wrapper-table .data-row .description-box p {
		color: var(--textcolor);
	}

	.data-wrapper-table .data-row .qty-items {
		color: var(--textcolor);
	}

	.data-wrapper-table .inner-table-fields {
		border: 1px solid var(--lightgray);
	}

	@media (max-width: 768px) {
		.data-wrapper-table .data-row .row .theme-counter-items {
			border: 1px solid var(--border);
		}
	}

	.wrapper-date-list .btn-track-detail span {
		color: var(--darkcolor);
	}

	.wrapper-date-list .datelist-wrapper h5 {
		background: var(--grayf5);
	}

	.wrapper-date-list .datelist-wrapper p {
		color: var(--gray);
	}

	.theme-counter-items span {
		color: var(--link);
	}

	.theme-counter-items input {
		border: 1px solid var(--lightgray);
	}

	.network-cateogry-list ul li label span {
		border: 1px solid var(--link);
		color: var(--link);
	}

	.network-cateogry-list ul li label input:checked + span {
		background: var(--link);
		color: var(--white);
	}

	.other-information-sec .wrapper-inner-content .top-intorduction {
		color: var(--textcolor);
	}

	.other-information-sec .wrapper-inner-content .about-yourself input {
		border: 1px solid var(--lightgray);
	}

	.reason-doctor-visisted {
		border: 1px solid var(--lightgray);
	}

	.reason-doctor-visisted h4 {
		border-bottom: 1px solid var(--lightgray);
	}

	.generic-form textarea {
		border: 1px solid var(--border);
		color: var(--gray);
	}

	.custom-label-select .css-spersy-control,
	.custom-label-select .css-spersy-control:hover {
		border-color: var(--border) !important;
	}

	.react-select__menu {
		z-index: 99999;
	}

	.select-date-time .DayPickerInput input {
		border: 1px solid var(--border);
		color: var(--gray);
	}

	.label-radio span:before {
		background: var(--white);
		border: 1px solid var(--border);
		color: var(--border);
	}

	.label-radio input:checked + span:before {
		background: var(--link);
		color: var(--white);
		border-color: var(--link);
	}

	.hotel-booking-section .top-form-booking .hotel-room {
		color: var(--textcolor);
	}

	.hotel-booking-section .top-form-booking .hotel-room span,
	.hotel-booking-section .data-wrapper-table .data-row.header-table .description-box,
	.summry-list-section .summry-row .summry-price {
		color: var(--lightdark);
	}

	.hotel-booking-section .data-wrapper-table .data-row .col-2 span.per-night {
		color: var(--textcolor);
	}

	.summry-list-section .summry-row {
		border-top: 1px solid var(--lightgray);
	}

	.summry-list-section .summry-row .summry-description p {
		color: var(--textcolor);
	}

	.summry-list-section .summry-row .summry-description .icons {
		background: var(--lightgray);
		
	}

	.summry-list-section .summry-row .summry-price .per-night {
		color: var(--textcolor);
	}

	@media (max-width: 768px) {
		.summry-list-section .summry-row .summry-panel .wrapper-panel-box {
			background: var(--white);
			border: 1px solid var(--lightgray);
		}

		.summry-list-section .summry-row .summry-panel .wrapper-panel-box span {
			color: var(--darkcolor);
		}
	}

	.payment-information {
		color: var(--textcolor);
	}

	.order-table-wrapper .order-row {
		border-top: 1px solid var(--lightgray);
		color: var(--lightdark);
	}

	.order-table-wrapper .order-row .data-description p {
		color: var(--textcolor);
	}

	.order-table-wrapper .inner-data-table {
		border: 1px solid var(--lightgray);
	}

	@media (max-width: 768px) {
		.order-table-wrapper .order-row .data-price {
			color: var(--textcolor);
		}

		.order-table-wrapper .order-row .data-price.data-price-total {
			color: var(--lightdark);
		}
	}

	.checkout-sidebar .accept-terms p a {
		color: var(--lightdark);
	}

	.checkout-sidebar .accept-terms .btn_checbox {
		border: 1px solid var(--lightgray);
	}

	.checkout-sidebar .accept-terms .btn_checbox.checked {
		border-color: var(--darkcolor);
		background: var(--darkcolor);
	}

	.checkout-sidebar .accept-terms .btn_checbox.checked i {
		color: var(--white);
	}

	@media (max-width: 768px) {
		.checkout-sidebar .top-checkout-header .btn.active {
			border: 1px solid var(--darkcolor);
			background: var(--white);
			color: var(--darkcolor);
		}
	}

	.checkout-form .form-rows.total-row {
		border-top: 1px solid var(--gray);
		border-bottom: 1px solid var(--gray);
	}

	.registration-success .header-area p {
		color: var(--textcolor);
	}

	.registration-success .inner-container {
		background: ${event.registration_flow_theme ? event.registration_flow_theme.wrapper_color : '#F2F2F2'};
		color: var(--textcolor);
	}

	.ebs-corporate-login .ebs-corporate-fields .btn:hover {
		background: var(--link);
	}

	.ebs-corporate-login .ebs-corporate-fields .ebs-input-field .title em {
		color: var(--link);
	}

	.ebs-corporate-login .ebs-corporate-fields .ebs-input-field input:focus {
		border-color: var(--link);
	}

	.ebs-corporate-login .ebs-corporate-fields .btn {
		background: var(--link);
	}
`;
	return (
		<style dangerouslySetInnerHTML={{ __html: _styles }}></style>
	)
}

export default Theme;