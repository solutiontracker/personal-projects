import React from 'react';
import Countdown, { zeroPad } from "react-countdown";
import moment from 'moment';
import { setRegistrationEndtime } from '../../../../helpers/helper'
import HeadingElement from 'components/ui-components/HeadingElement'
const Completionist = ({ labels }) =>  
  <div className="col-12">
    <h2>{labels.RESGISTRATION_SITE_THIS_EVENT_IS_GOING_ON ? labels.RESGISTRATION_SITE_THIS_EVENT_IS_GOING_ON : "This event is going on."}</h2>
  </div>
;



const Variation1 = ({ eventSiteSettings, eventTimeZone, registrationFormInfo ,labels, registerDateEnd, checkTickets, waitingList, moduleVariation, registrationUrl}) => {
  const ticket_settings = eventSiteSettings.eventsite_tickets_left === 1 ? true : false;
  const bgStyle = (moduleVariation && moduleVariation.background_color !== "") ? { backgroundColor: moduleVariation.background_color} : {}
  // Renderer callback with condition
  const renderer = ({ days, hours, minutes, seconds, completed }) => {
    if (completed) {
      // Render a complete state
      return <Completionist labels={labels}/>;
    } else {
      // Render a countdown
      return (
        <React.Fragment>
          <div className="ebs-countdown-wrapp countdown-wrapp">
            {Math.floor(days / 30) > 0 && <span className="edgtf-countdown is-countdown">
              <span className="countdown-amount">{zeroPad(Math.floor(days / 30))}</span>
              <span className="countdown-period">Months</span>
            </span>}
            <span className="edgtf-countdown is-countdown">
              <span className="countdown-amount">{zeroPad(Math.floor(days % 30))}</span>
              <span className="countdown-period">Days</span>
            </span>
            <span className="edgtf-countdown is-countdown">
              <span className="countdown-amount">{zeroPad(hours)}</span>
              <span className="countdown-period">Hours</span>
            </span>
            <span className="edgtf-countdown is-countdown">
              <span className="countdown-amount">{zeroPad(minutes)}</span>
              <span className="countdown-period">Minutes</span>
            </span>
            <span className="edgtf-countdown is-countdown">
              <span className="countdown-amount">{zeroPad(seconds)}</span>
              <span className="countdown-period">Seconds</span>
            </span>
          </div>
        </React.Fragment>
      );
    }
  };
  return (
    <div style={bgStyle} className="module-section ebs-default-padding">
        {registerDateEnd &&  (
          <div className="container">
            <HeadingElement dark={false} label={labels.EVENTSITE_REGISTER_NOW} desc={labels.EVENTSITE_TICKETS_ARE_FLYING} align={'center'} />
            <div className="ebs-register-now-sec">
              {(registrationFormInfo.has_multiple_form != true && registrationFormInfo.form_registration_remaining_tickets != '') && <div className="ebs-ticket-remaning">
                <div className="ebs-ticket-counter">{registrationFormInfo.form_registration_remaining_tickets}</div>
                <div className="ebs-ticket-status">{labels.EVENTSITE_TICKETS_LEFT}</div>
              </div>}

              {/* {(eventSiteSettings.eventsite_time_left === 1 && eventSiteSettings.registration_end_date !== "0000-00-00 00:00:00") && <Countdown date={moment(eventSiteSettings.registration_end_date)} renderer={renderer} />} */}
            {(registrationFormInfo.has_multiple_form != true && registrationFormInfo.form_registration_end_date != '') && <Countdown date={setRegistrationEndtime(eventTimeZone, registrationFormInfo.form_registration_end_date)} renderer={renderer} />}
              <div className="row d-flex">
                <div className="col-md-10 offset-md-1">
                  <div className="ebs-caption-box">
                    <div className="ebs-description-area">{labels.EVENTSITE_HOME_REGISTRATION_TEXT}</div>
                    <a href={registrationUrl} rel="noopener" className="edgtf-btn edgtf-btn-medium edgtf-btn-solid"><span className="edgtf-btn-text">{labels.EVENTSITE_REGISTER_NOW2}</span></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )} 
        {!registerDateEnd && (
          <div className="container">
            <div className="alert alert-danger alert-dismissable">{labels.REGISTER_DATE_END}</div>
          </div>
        )}
        
        {/* {(registerDateEnd && (checkTickets.ticketsSet && checkTickets.remainingTickets <= 0) && !waitingList ) && (
          <div className="container">
            <div className="alert alert-danger alert-dismissable">{labels.REGISTER_TICKET_END}</div>
          </div>
        )} */}
        
        {/* {(registerDateEnd && (checkTickets.ticketsSet && checkTickets.remainingTickets <= 0) && waitingList ) && (
          <div className="container">
            {labels.REGISTER_FOR_WAITING_LIST || labels.NO_TICKETS_LEFT_REGISTER_WAITING_LIST && <HeadingElement dark={false} label={labels.REGISTER_FOR_WAITING_LIST} desc={labels.NO_TICKETS_LEFT_REGISTER_WAITING_LIST} align={moduleVariation.text_align} />}
            <div className="ebs-register-now-sec">
            <div className="row d-flex">
                <div className="col-md-10 offset-md-1">
                  <div className="ebs-caption-box">
                    <div className="ebs-description-area">{labels.WAITING_LIST_EVENTSITE_INTRODUCTION_PARA}</div>
                    <a href={registrationUrl} rel="noopener" className="edgtf-btn edgtf-btn-medium edgtf-btn-solid"><span className="edgtf-btn-text">{labels.REGISTER_FOR_WAITING_LIST_BUTTON}</span></a>
                  </div>
                </div>
              </div>
              </div>
          </div>
        )} */}
    </div>
  );
};

export default Variation1;
