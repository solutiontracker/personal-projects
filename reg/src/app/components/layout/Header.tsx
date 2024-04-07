import React, { ReactElement, FC, useContext, useState, useEffect } from "react";
import { useParams } from "react-router-dom";
import { ReactSVG } from 'react-svg';
import { EventContext } from "@/src//app/context/event/EventProvider";
import moment from 'moment-timezone';
import { useHistory, Link } from "react-router-dom";
import Popup from '@/src/app/components/forms/Popup';
import { Helmet, HelmetProvider, HelmetData } from 'react-helmet-async';
import { facebookPixel, ltrim, linkedinPixel, googleTagManager } from '@/src/app/helpers';
import { localeMomentEventDates } from '@/src/app/helpers';
import in_array from "in_array";

type Params = {
	url: any;
	order_id: any;
};

const Header: FC<any> = (): ReactElement => {

	const { event, cookie } = useContext<any>(EventContext);

	const [popup, setPopup] = useState(false);

	const helmetData = new HelmetData({});

	const _domain = window.location.href;

	const [event_url, provider] = ltrim(window.location.pathname, "/").split("/");

	useEffect(() => {

		const faviconUpdate = async () => {
			const selector: any = document.querySelector("link[rel*='icon']");
			if (event.settings.fav_icon && event.settings.fav_icon !== "") {
				selector.href = process.env.REACT_APP_EVENTCENTER_URL + "/assets/event/branding/" + event.settings.fav_icon;
			}
		};

		faviconUpdate();

	}, []);

	useEffect(() => {

		if (!window.location.toString().includes("/registration-success") && cookie === "all" && in_array(provider, ['attendee', 'embed'])) {
			if (event.settings?.facebook_pixel_id !== undefined && event.settings?.facebook_pixel_id) {
				facebookPixel(event.settings?.facebook_pixel_id, true, (event.settings?.analytics_page_view_event_name !== undefined && event.settings?.analytics_page_view_event_name ? event.settings?.analytics_page_view_event_name : 'PageView'));
			}

			if (event.settings?.linkedin_partner_id !== undefined && event.settings?.linkedin_partner_id && event.settings?.linkedin_conversion_id !== undefined && event.settings?.linkedin_conversion_id) {
				linkedinPixel(event.settings?.linkedin_partner_id, event.settings?.linkedin_conversion_id, true);
			}

			if (event.settings?.google_analytics_id !== undefined && event.settings?.google_analytics_id) {
				googleTagManager(event.settings?.google_analytics_id, true, (event.settings?.analytics_page_view_event_name !== undefined && event.settings?.analytics_page_view_event_name ? event.settings?.analytics_page_view_event_name : 'PageView'));
			}

			if (event.settings?.matomo_domain_id !== undefined && event.settings?.matomo_domain_id) {
				var _mtm = window._mtm = window._mtm || [];
				_mtm.push({ 'mtm.startTime': (new Date().getTime()), 'event': 'mtm.Start' });
				(function () {
					var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
					g.async = true; g.src = 'https://cdn.matomo.cloud/' + event.settings?.matomo_domain_id + '/container_ynlL2iw5.js'; s.parentNode.insertBefore(g, s);
				})();
			}
		}

	}, [cookie]);

	return (
		<React.Fragment>
			{event && (
				<React.Fragment>
					<Helmet helmetData={helmetData} prioritizeSeoTags>
						<title>{event.name}</title>
						<meta property="og:title" content={event.name} />
						<meta property="og:type" content="Event" />
						<meta
							property="og:url"
							content={`${window.location.origin.toString()}/${event_url}/${provider}`}
						/>
						<meta
							property="og:image"
							content={
								event.settings.social_media_logo &&
									event.settings.social_media_logo !== ""
									? process.env.REACT_APP_EVENTCENTER_URL +
									"/assets/event/social_media/" +
									event.settings.social_media_logo
									: event.settings.header_logo &&
										event.settings.header_logo !== ""
										? process.env.REACT_APP_EVENTCENTER_URL +
										"/assets/event/branding/" +
										event.settings.header_logo
										: process.env.REACT_APP_EVENTCENTER_URL +
										"/_eventsite_assets/images/eventbuizz_logo-1.png"
							}
						/>
						<meta
							property="twitter:image"
							content={
								event.settings.social_media_logo &&
									event.settings.social_media_logo !== ""
									? process.env.REACT_APP_EVENTCENTER_URL +
									"/assets/event/social_media/" +
									event.settings.social_media_logo
									: event.settings.header_logo &&
										event.settings.header_logo !== ""
										? process.env.REACT_APP_EVENTCENTER_URL +
										"/assets/event/branding/" +
										event.settings.header_logo
										: process.env.REACT_APP_EVENTCENTER_URL +
										"/_eventsite_assets/images/eventbuizz_logo-1.png"
							}
						/>
						<meta property="twitter:card" content="summary_large_image" />
						<meta httpEquiv="X-UA-Compatible" content="IE=edge" />
						<meta name="msapplication-config" content="none" />
						<meta
							property="og:description"
							content={
								event.description && event.description.info
									? event.description.info.description
									: event.name
							}
						/>
					</Helmet>
				</React.Fragment>
			)}
			<header className='header fixed-top'>
				<div className="container">
					<div className="row">
						<div className="col d-flex align-items-center">
							{!window.location.toString().includes("/registration-success") &&
								<button className="btn-menu collapsed" type="button" data-toggle="collapse" data-target="#collaspe-item-header" aria-expanded="false" aria-controls="collaspe-item-header">
									<i className='collapsed material-icons'>dehaze</i>
									<i className='collapsed-alt material-icons'>close</i>
								</button>
							}
							{event?.eventsite_setting?.third_party_redirect_url && Number(event?.eventsite_setting?.third_party_redirect) === 1 ? (
								<a className="ebs-logo" href={event?.eventsite_setting?.third_party_redirect_url}>
									{event?.settings.header_logo ? (
										<img src={`${process.env.REACT_APP_EVENTCENTER_URL}/assets/event/branding/${event.settings.header_logo}`} alt="" />
									) : (
										<img src={`${process.env.REACT_APP_EVENTCENTER_URL}/_mobile_assets/images/logo-header@2x.png`} alt="" />
									)}
								</a>
							) : (
								<a className="ebs-logo" href={`${process.env.REACT_APP_REG_SITE_URL}/${event.url}`}>
									{event?.settings.header_logo ? (
										<img src={`${process.env.REACT_APP_EVENTCENTER_URL}/assets/event/branding/${event.settings.header_logo}`} alt="" />
									) : (
										<img src={`${process.env.REACT_APP_EVENTCENTER_URL}/_mobile_assets/images/logo-header@2x.png`} alt="" />
									)}
								</a>
							)}
						</div>
						{event?.social_media?.length > 0 && (
							<div className="col d-flex justify-content-end align-items-center">
								<div className="ebs-nav-social">
									<a href="#!" className="btn-share">{event?.labels?.REGISTRATION_FORM_SHARE} <i className='material-icons'>keyboard_arrow_down</i></a>
									<div className="ebs-social-dropdown">
										{event?.social_media?.map((row: any, key: any) =>
											<React.Fragment key={key}>
												{
													(() => {
														if (row?.alias === "Facebook")
															return <a target="_blank" key={key} href={`https://www.facebook.com/sharer.php?u=${_domain}`} rel="noreferrer"><ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-facebook.svg')} /></a>
														else if (row?.alias === "Twitter")
															return <a target="_blank" key={key} href={`https://twitter.com/intent/tweet?url=${_domain}`} rel="noreferrer"><ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-twitter.svg')} /></a>
														else if (row?.alias === "Linkedin")
															return <a target="_blank" key={key} href={`https://www.linkedin.com/shareArticle?mini=true&url=${_domain}`} rel="noreferrer"><ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-linkedin.svg')} /></a>
														else if (row?.alias === "Pinterest")
															return <a target="_blank" key={key} href={`https://pinterest.com/pin/create/button/?url=${_domain}`} rel="noreferrer"><ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-pinterest.svg')} /></a>
														else if (row?.alias === "Email")
															return <a key={key} href={`mailto:?subject=${'Eventbuizz'}&body=${_domain}`}><ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-email.svg')} /></a>
													})()
												}
											</React.Fragment>
										)}
									</div>
								</div>
							</div>
						)}

					</div>
				</div>
				<div className="collapse ebs-collaspe-item" id="collaspe-item-header">
					<div className="ebs-master-header-wrapper">
						<div className="container">
							<h3>{event?.name}</h3>
							<div className="row">
								<div className="col">
									{event?.event_opening_hours?.length > 0 && (
										<>
											<h5 className='link'>{event?.labels?.EVENT_SITE_FOOTER_TITLE_ONE}</h5>
											{event.event_opening_hours.map((item: any, i: any) => (
												<div style={{ marginBottom: "10px" }} key={i}>
													<p className='icon d-flex'>
														<i className='material-icons'>date_range</i>
														<time>{`${localeMomentEventDates(item.date, event.language_id, event.timezone.timezone)}`}</time>
													</p>
													<p className="icon d-flex" >
														<i className='material-icons'>access_time</i>
														{`${moment(item?.date + ' ' + item?.start_time).format('HH:mm')} - ${moment(item?.date + ' ' + item?.end_time).format('HH:mm')}`}
													</p>
												</div>
											))}
										</>
									)}
									{Number(event?.eventsite_setting?.calender_show) === 1 ? <a href={`${process.env.REACT_APP_EVENTCENTER_URL}/event/${event.url}/detail/addToCalender`} style={{ textDecoration: 'underline' }} className="link">  {event.labels.EVENTSITE_ADD_TO_CALENDAR_LABEL !== undefined ? event.labels.EVENTSITE_ADD_TO_CALENDAR_LABEL : "Add to Calendar"}</a> : null}
								</div>
								<div className="col">
									<h5 className='link'>{event?.labels?.EVENT_SITE_FOOTER_TITLE_TWO}</h5>
									<address style={{ paddingRight: '20px' }} className="d-flex icon">
										<i className="material-icons">room</i>
										{event?.detail?.location_name && (
											<React.Fragment>
												{event?.detail?.location_name}<br />
											</React.Fragment>
										)}
										{event?.detail?.location_address && (
											<React.Fragment>
												{event?.detail?.location_address}<br />
											</React.Fragment>
										)}
										{event?.country && (
											<React.Fragment>
												{event?.country}<br />
											</React.Fragment>
										)}
									</address>
									{event?.map?.detail?.url && (
										<a onClick={() => setPopup(true)} className="link">View map</a>
									)}
								</div>
								{event.event_contact_persons.length > 0 &&
									<div className="col">
										<h5 className='link'>{event?.labels?.EVENT_SITE_FOOTER_TITLE_THREE}</h5>
										{event.event_contact_persons.length > 0 && event.event_contact_persons.map((person: any, i: any) => (
											<div style={{ marginBottom: '10px' }} key={i}>
												{(person.first_name !== '' || person.first_name !== '') && <p style={{margin:"0px"}}>{person.first_name} {" "} {person.last_name}</p>}
                                                {person.email !== '' && <p>{event?.labels?.REGISTRATION_FORM_EMAIL}: <a href={`mailto:${person.email}`}>{person.email}</a></p>}
                                                {person.phone !== '' && <p>{event?.labels?.GENERAL_PHONE}: {person.phone}</p>}
											</div>
										))}
									</div>
								}
								<div className="col">
									<h5 className='link'>{event?.labels?.EVENT_SITE_FOOTER_TITLE_FOUR}</h5>
									<p>{event?.organizer_name}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</header>

			{popup &&
				<Popup
					onClick={() => setPopup(false)}
					title="Map"
					width="80%"
				>
					<div className="ebs-popup-content">
						<div className="ebs-popup-buttons text-center">
							<div className="iframe">
								<iframe style={{ height: '75vh', border: 'none' }} title='iframe' src={event?.map?.detail?.url} width="100%"></iframe>
							</div>
						</div>
					</div>
				</Popup>
			}
		</React.Fragment>
	);
};

export default Header;