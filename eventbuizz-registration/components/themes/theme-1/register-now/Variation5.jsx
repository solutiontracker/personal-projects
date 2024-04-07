import React from 'react';
import Countdown, { zeroPad } from "react-countdown";
import moment from 'moment';
import HeadingElement from 'components/ui-components/HeadingElement';
import { setRegistrationEndtime } from '../../../../helpers/helper'

const Completionist = ({ labels }) =>
  <div className="col-12">
    <h2>{labels.RESGISTRATION_SITE_THIS_EVENT_IS_GOING_ON ? labels.RESGISTRATION_SITE_THIS_EVENT_IS_GOING_ON : "This event is going on."}</h2>
  </div>
  ;


const Variation5 = ({ eventSiteSettings, eventTimeZone,registrationFormInfo, labels, registerDateEnd, checkTickets, waitingList, moduleVariation, registrationUrl }) => {
  const ticket_settings = eventSiteSettings.eventsite_tickets_left === 1 ? true : false;
  const bgStyle = (moduleVariation && moduleVariation.background_color !== "") ? { backgroundColor: moduleVariation.background_color} : {}
  const renderer = ({ days, hours, minutes, seconds, completed }) => {
    if (completed) {
      // Render a complete state
      return <Completionist labels={labels}/>;
    } else {
      // Render a countdown
      return (
        <React.Fragment>
          <div className={`ebs-countdown-wrapp countdown-wrapp ${Math.floor(days / 30) > 0 ? 'ebs-count-down-small' : ''}`}>
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
    <div style={bgStyle} className="module-section ebs-register-now-v5 ebs-default-padding">
      {registerDateEnd && (
        <div className="container">
          <div className="row d-flex align-items-center">
            <div className="col-lg-4">
              <div className="edgtf-title-section-holder">
                <h2
                  style={{ color: '#313131', marginTop: 0 }}
                  className="edgtf-title-with-dots edgtf-appeared"
                >
                  {labels.EVENTSITE_REGISTER_NOW}
                </h2>
                <span className="edge-title-separator edge-enable-separator"></span>
                <div className="edgtf-title-section-holder">
                  <h6 style={{ color: '#888', marginTop: 0 }} className="edgtf-section-subtitle">{labels.EVENTSITE_TICKETS_ARE_FLYING}</h6>
                </div>
              </div>
            </div>
            <div className="col-lg-8">
              <div style={{paddingBottom: '40px'}} className="ebs-caption-box">
                <div className="ebs-description-area">{labels.EVENTSITE_HOME_REGISTRATION_TEXT}</div>
              </div>
            </div>
          </div>
          <div className="ebs-register-now-sec">
            <div className="row d-flex align-items-center">
            {(registrationFormInfo.has_multiple_form != true && registrationFormInfo.form_registration_remaining_tickets != '') && <div className="col-lg-4 ">
                <div className="ebs-ticket-remaning d-flex align-items-center">
                  <div style={{ paddingRight: 20 }} className="ebs-ticket-status">{labels.EVENTSITE_TICKETS_LEFT}</div>
                  <div className="ebs-ticket-counter">{registrationFormInfo.form_registration_remaining_tickets}</div>
                </div>
              </div>}
              {/* <div className={`d-flex d-block-responsive align-items-center ${ticket_settings ? 'col-lg-8' : 'col-lg-12'}`}> */}
              <div className={`d-flex d-block-responsive align-items-center ${'col-lg-12'}`}>
                {/* {(eventSiteSettings.eventsite_time_left === 1 && eventSiteSettings.registration_end_date !== "0000-00-00 00:00:00") && <Countdown date={moment(eventSiteSettings.registration_end_date)} renderer={renderer} />} */}
                {(registrationFormInfo.has_multiple_form != true && registrationFormInfo.form_registration_end_date != '') && <Countdown date={setRegistrationEndtime(eventTimeZone, registrationFormInfo.form_registration_end_date)} renderer={renderer} />}
                <a href={registrationUrl} rel="noopener" className="edgtf-btn edgtf-btn-medium edgtf-btn-solid"><span className="edgtf-btn-text">{labels.EVENTSITE_REGISTER_NOW2}</span></a>
              </div>
            </div>
          </div>
        </div>
      )}
      {!registerDateEnd  && (
        <div className="container">
          <div className="alert alert-danger alert-dismissable">{labels.REGISTER_DATE_END}</div>
        </div>
      )}

      {/* {(registerDateEnd && (checkTickets.ticketsSet && checkTickets.remainingTickets <= 0) && !waitingList) && (
        <div className="container">
          <div className="alert alert-danger alert-dismissable">{labels.REGISTER_TICKET_END}</div>
        </div>
      )} */}

      {/* {(registerDateEnd && (checkTickets.ticketsSet && checkTickets.remainingTickets <= 0) && waitingList) && (
        <div className="container">
          <HeadingElement dark={false} label={labels.REGISTER_FOR_WAITING_LIST} desc={labels.NO_TICKETS_LEFT_REGISTER_WAITING_LIST} align={moduleVariation.text_align} />
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

export default Variation5;