import React, {useState, useEffect} from "react";
import moment from "moment";
import {localeMomentEventDates, localeMomentOpeningHours} from "helpers/helper";

const Variation2 = ({event, siteLabels}) => {

    const [height, setheight] = useState('');
    useEffect(() => {
      const _footer = document.getElementById('ebs-footer')?.offsetHeight;
      const _style  = `.master-container {min-height: calc(100% - ${_footer}px) !important;}`
      setheight(_style);
     
    }, []);

  return (
    <>
        {(event.eventsiteSettings.use_reg_form_footer == 0  && event.eventsiteSettings.reg_site_footer_image !== "") && 
          <img src={`${process.env.NEXT_APP_EVENTCENTER_URL + '/assets/event_site/upload_images/'}${event.eventsiteSettings.reg_site_footer_image}`} alt=""  style={{width:"100%"}}/>
        }
        {event.eventsiteSettings.use_reg_form_footer === 1 && 
            <>
            <footer id="ebs-footer" className="footer ebs-variation-dark">
                <style dangerouslySetInnerHTML={{ __html: height }}></style>
                <div style={{paddingLeft: 0, paddingRight:0, borderRadius: 0,margin: 0}} className="wrapper-box order-summry">
                    <div className="container">
                        <div className="inner-container ebs-collaspe-item" id="collaspe-item">
                            <h3>{event?.name} </h3>
                            <div className="row">
                                {event.eventOpeningHours.length > 0 && <div className="col-3">
                                    <h5 className='link'>{event?.labels?.EVENT_SITE_FOOTER_TITLE_ONE}</h5>
                                    {event.eventOpeningHours.map((item, i)=>(
                                        <div style={{marginBottom:"10px"}} key={i}>
                                            <p className='icon d-flex'>
                                                <i className='material-icons'>date_range</i>
                                                <time dateTime="2019-31-12" >{`${localeMomentEventDates(item.date, event.language_id)}`}</time>
                                            </p>
                                            <p className="icon d-flex" >
                                                <i className='material-icons'>access_time</i>
                                               {`${moment(item?.date + ' ' + item?.start_time).format('HH:mm')} - ${moment(item?.date + ' ' + item?.end_time).format('HH:mm')}`}
                                            </p>
                                        </div>
                                    ))}

                                   {event.eventsiteSettings.calender_show == 1 && <a href={`${process.env.NEXT_APP_EVENTCENTER_URL}/event/${event.url}/detail/addToCalender`} style={{textDecoration: 'underline'}} className="link">{event.labels.EVENTSITE_ADD_TO_CALENDAR_LABEL !== undefined ? event.labels.EVENTSITE_ADD_TO_CALENDAR_LABEL : "Add to Calendar"}</a>}
                                </div>}
                                <div className="col-3">
                                    <h5 className='link'>{event?.labels?.EVENT_SITE_FOOTER_TITLE_TWO}</h5>
                                    <address style={{paddingRight: '20px'}} className="d-flex icon">
                                        <i className="material-icons">room</i>
                                        {event?.info?.location_name && (
                                            <React.Fragment>
                                                {event?.info?.location_name}<br />
                                            </React.Fragment>
                                        )}
                                        {event?.info?.location_address && (
                                            <React.Fragment>
                                                {event?.info?.location_address}<br />
                                                </React.Fragment>
                                        )}
                                        
                                        {event?.country && (
                                            <React.Fragment>
                                                {event?.country}<br />
                                            </React.Fragment>
                                        )}
                                       
                                    </address>
                                </div>
                                {event.eventContactPersons.length > 0 &&
                                    <div className="col-3">
                                        <h5 className='link'>{event?.labels?.EVENT_SITE_FOOTER_TITLE_THREE}</h5>
                                        {event.eventContactPersons.length > 0 && event.eventContactPersons.map((person, i)=>(
                                            <div style={{marginBottom:"10px"}}  key={i}>
                                            {(person.first_name !== '' || person.first_name !== '') && <p style={{margin:"0px"}}>{person.first_name} {" "} {person.last_name}</p>}
                                            {person.email !== '' && <p>{event?.labels?.REGISTRATION_FORM_EMAIL}: <a href={`mailto:${person.email}`}>{person.email}</a></p>}
                                            {person.phone !== '' && <p>{event?.labels?.GENERAL_PHONE}: {person.phone}</p>}
                                            </div>
                                        ))}
                                    </div>
                                }
                                <div className="col-3">
                                    <h5 className='link'>{event?.labels?.EVENT_SITE_FOOTER_TITLE_FOUR}</h5>
                                    <p>{event?.organizer_name}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            </>
        }

    </>
  );
};

export default Variation2;
