import React, { useEffect, useState, useContext } from 'react'
import { EventContext } from "@/src/app/context/event/EventProvider";
import { Link, useLocation, useHistory } from 'react-router-dom';
import { withRouter } from "react-router";
import in_array from "in_array";

const Steps = (props: any) => {

	const { event, routeParams, formBuilderForms } = useContext<any>(EventContext);
	const currenFormIndex = formBuilderForms.findIndex((form:any)=>(form.id == routeParams.form_id));
	console.log(routeParams?.orderAttendee?.status);
	const history = useHistory();

	return (
		<>
			{in_array(routeParams?.page, ['registration-information', 'hotel-booking', 'custom-forms']) && (
				<div className={`ebs-steps-wrapper ${props.toggle && 'ebs-toggle'}`}>
					<div className="container">
						<nav>
							<ul>
								<li onClick={(e: any) => {
									if (!in_array(routeParams?.page, ["order-summary", "payment-information", "add-attendee"])) {
										if (routeParams?.orderAttendee?.status === 'complete' || in_array(routeParams?.page, ["hotel-booking","custom-forms" ])) {
											if (routeParams.order_id !== undefined && routeParams.attendee_id !== undefined) {
												history.push(`/${event.url}/${routeParams?.provider}/manage-attendee/${routeParams.order_id}/${routeParams.attendee_id}`);
											} else if (routeParams.order_id !== undefined) {
												history.push(`/${event.url}/${routeParams?.provider}/manage-attendee/${routeParams.order_id}`);
											} else {
												history.push(`/${event.url}/${routeParams?.provider}/manage-attendee`);
											}
										}
									}
								}} className={`${(routeParams?.orderAttendee?.status === 'complete' || in_array(routeParams?.page, ["order-summary", "payment-information", "hotel-booking", "custom-forms"])) && 'ebs-step-done'} ${in_array(routeParams?.page, ['registration-information', 'add-attendee']) && 'ebs-active'}`}>
									<a style={{ cursor: 'pointer' }}>{event?.labels?.REGISTRATION_FORM_REGISTRATION_INFORMATION}</a>
								</li>
								{Number(routeParams?.form_settings?.show_hotels) === 1 ? (
									<li onClick={(e: any) => {
										if (!in_array(routeParams?.page, ["order-summary", "payment-information", "add-attendee"])) {
											if (routeParams.order_id !== undefined && routeParams.attendee_id !== undefined && routeParams?.orderAttendee?.status === 'complete' || in_array(routeParams?.page, ["custom-forms"])) {
												history.push(`/${event.url}/${routeParams?.provider}/manage-hotel-booking/${routeParams.order_id}/${routeParams.attendee_id}`);
											}
										}
									}} className={`${(routeParams?.orderAttendee?.status === 'complete' || in_array(routeParams?.page, ["order-summary", "payment-information", "custom-forms"])) && 'ebs-step-done'} ${in_array(routeParams?.page, ['hotel-booking']) && 'ebs-active'}`}>
										<a style={{ cursor: 'pointer' }}>{event?.labels?.REGISTRATION_FORM_HOTEL_BOOKING}</a>
									</li>
								) : ''}
								{
									formBuilderForms?.map((form:any, i:any)=>(
										<li key={form.id} onClick={(e: any) => {
												if (routeParams?.orderAttendee?.status === 'complete' || (routeParams.order_id !== undefined && routeParams.attendee_id !== undefined && (currenFormIndex > i))) {
													history.push(`/${event.url}/${routeParams?.provider}/custom-forms/${routeParams.order_id}/${routeParams.attendee_id}/${form.id}`);
												}
										}} className={`${(routeParams?.orderAttendee?.status === 'complete' || (currenFormIndex > i) ) && 'ebs-step-done'} ${(routeParams.form_id == form.id) && 'ebs-active'}`}>
											<a style={{ cursor: 'pointer' }}>{form.title}</a>
										</li>
									))
								}

								<li onClick={(e: any) => {
									if (routeParams.order_id !== undefined && (routeParams?.orderAttendee?.status === 'complete' || in_array(routeParams?.page, ["order-summary", "payment-information"]))) {
										history.push(`/${event.url}/${routeParams?.provider}/order-summary/${routeParams.order_id}`);
									}
								}} className={`${(routeParams?.orderAttendee?.status === 'complete' || in_array(routeParams?.page, ["order-summary", "payment-information"])) && 'ebs-step-done'}`}>
									<a style={{ cursor: 'pointer' }}>{event?.labels?.REGISTRATION_FORM_REGISTRATION_SUMMARY}</a>
								</li>
							</ul>
							<span onClick={props.handleToggle} className='ebs-button-close'><i className="material-icons">{!props.toggle ? 'expand_less' : 'expand_more'}</i></span>
						</nav>
					</div>
				</div>
			)}
		</>
	)
}
export default withRouter(Steps);
