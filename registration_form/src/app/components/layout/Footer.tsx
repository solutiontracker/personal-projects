import React, { ReactElement, FC, useContext, useEffect, useState } from 'react';
import { EventContext } from "@/src//app/context/event/EventProvider";
import moment from 'moment-timezone';
import { localeMomentEventDates } from '@/src/app/helpers';

type Props = Record<string, never>;

const Footer: FC<Props> = (): any => {

    const { event } = useContext<any>(EventContext);

    const [height, setheight] = useState('');

    useEffect(() => {
        const _footer = document.getElementById('ebs-footer')?.offsetHeight;
        const _style = `.master-container {min-height: calc(100% - ${_footer}px) !important;}`
        setheight(_style);

    }, []);

    if (Number(event?.eventsite_setting?.eventsite_footer) === 1) {
        return (
            <footer id="ebs-footer" className="footer">
                <style dangerouslySetInnerHTML={{ __html: height }}></style>
                <div style={{ paddingLeft: 0, paddingRight: 0, borderRadius: 0, margin: 0 }} className="wrapper-box order-summry">
                    <div className="container">
                        <div className="inner-container ebs-collaspe-item" id="collaspe-item">
                            <h3>{event?.name} </h3>
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
                                </div>
                                {event.event_contact_persons.length > 0 && 
                                    <div className="col">
                                        <h5 className='link'>{event?.labels?.EVENT_SITE_FOOTER_TITLE_THREE}</h5>
                                        {event.event_contact_persons.length > 0 && event.event_contact_persons.map((person:any, i:any) => (
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
            </footer>
        );
    } else {
        return null;
    }

};

export default Footer;