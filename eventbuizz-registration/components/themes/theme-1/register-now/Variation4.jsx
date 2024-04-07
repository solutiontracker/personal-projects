import React from 'react';
import Countdown, { zeroPad } from "react-countdown";
import moment from 'moment';
import HeadingElement from "components/ui-components/HeadingElement";
import { setRegistrationEndtime } from '../../../../helpers/helper'
const Completionist = ({ labels }) =>
  <div className="col-12">
    <h2>{labels.RESGISTRATION_SITE_THIS_EVENT_IS_GOING_ON ? labels.RESGISTRATION_SITE_THIS_EVENT_IS_GOING_ON : "This event is going on."}</h2>
  </div>
  ;

// Renderer callback with condition


const Variation4 = ({ eventSiteSettings, eventTimeZone, registrationFormInfo, labels, registerDateEnd, checkTickets, waitingList, moduleVariation, registrationUrl }) => {
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
  const WrapperLayout = (props) => {

    const _parallax = React.useRef(null);
    React.useEffect(() => {
      window.addEventListener("scroll",scollEffect);
      return () => {
        window.removeEventListener("scroll",scollEffect);
      }
    }, [])
    
     function scollEffect () {
      const scrolled = window.pageYOffset;
      const itemOffset = _parallax.current.offsetTop;
      const itemHeight = _parallax.current.getBoundingClientRect();
      if (scrolled < (itemOffset - window.innerHeight) || scrolled > (itemOffset + itemHeight.height)) return false;
      const _scroll = (scrolled - itemOffset) + itemHeight.height;
      _parallax.current.style.backgroundPosition = `50%  -${(_scroll * 0.1)}px`;
    };

    if (props.moduleVariation.background_image !== '') {
      return (
        <div ref={_parallax} style={{ backgroundImage: `url(${process.env.NEXT_APP_EVENTCENTER_URL + '/assets/variation_background/' + props.moduleVariation.background_image}`, backgroundPosition: "center top", backgroundSize: 'cover', }} className="edgtf-parallax-section-holder ebs-bg-holder ebs-default-padding">
          {props.children}
        </div>
      );
    } else {
      return (
        <div ref={_parallax} style={{ backgroundPosition: "center top", backgroundSize: 'cover', }} className="edgtf-parallax-section-holder ebs-bg-holder ebs-default-padding">
          {props.children}
        </div>
      );
    }

  }
  const ticket_settings = eventSiteSettings.eventsite_tickets_left === 1 ? true : false;
  return (
    <div className="module-section">
      <WrapperLayout
        moduleVariation={moduleVariation}
      >
        {(registerDateEnd && (!checkTickets.ticketsSet || checkTickets.remainingTickets > 0)) && (
          <div className="container">
            <HeadingElement dark={true} label={labels.EVENTSITE_REGISTER_NOW} desc={labels.EVENTSITE_TICKETS_ARE_FLYING} align={'left'} />
            <div className="ebs-register-now-sec ebs-register-v2 ebs-register-v3">
              <div className="row d-flex align-items-center flex-row-reverse">
                {(registrationFormInfo.has_multiple_form != true && registrationFormInfo.form_registration_remaining_tickets != '') && <div className="col-md-3">
                   <div className="ebs-ticket-remaning">
                    <div style={{ color: '#fff' }} className="ebs-ticket-counter">{registrationFormInfo.form_registration_remaining_tickets}</div>
                    <div style={{ color: '#fff' }} className="ebs-ticket-status">{labels.EVENTSITE_TICKETS_LEFT}</div>
                  </div>
                </div>}
                {/* <div className={ticket_settings ? 'col-md-9' : 'col-md-12'}> */}
                <div className={'col-md-12'}>
                  <div className="ebs-caption-box" style={{marginBottom: 20}}>
                    <div style={{ color: '#fff' }} className="ebs-description-area">{labels.EVENTSITE_HOME_REGISTRATION_TEXT}</div>
                  </div>
                </div>
              </div>
              {/* {(eventSiteSettings.eventsite_time_left === 1 && eventSiteSettings.registration_end_date !== "0000-00-00 00:00:00") && <Countdown date={moment(eventSiteSettings.registration_end_date)} renderer={renderer} />} */}
              {(registrationFormInfo.has_multiple_form != true && registrationFormInfo.form_registration_end_date != '') && <Countdown date={setRegistrationEndtime(eventTimeZone,registrationFormInfo.form_registration_end_date)} renderer={renderer} />}

              <div className="text-center">
                <a style={{ border: '2px solid #fff', color: '#fff' }} href={registrationUrl} rel="noopener" className="edgtf-btn edgtf-btn-huge edgtf-btn-custom-border-hover edgtf-btn-custom-hover-bg edgtf-btn-custom-hover-color">{labels.EVENTSITE_REGISTER_NOW2}</a>
              </div>
            </div>
          </div>
        )}

        {!registerDateEnd && (
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
            <HeadingElement dark={true} label={labels.REGISTER_FOR_WAITING_LIST} desc={labels.NO_TICKETS_LEFT_REGISTER_WAITING_LIST} align={moduleVariation.text_align} />
            <div className="ebs-register-now-sec">
              <div className="row d-flex">
                <div className="col-md-10 offset-md-1">
                  <div className="ebs-caption-box" style={{marginBottom: 20}}>
                    <div className="ebs-description-area" style={{ color: '#fff' }} >{labels.WAITING_LIST_EVENTSITE_INTRODUCTION_PARA}</div>
                    <a style={{ border: '2px solid #fff', color: '#fff' }} href={registrationUrl} rel="noopener" className="edgtf-btn edgtf-btn-huge edgtf-btn-custom-border-hover edgtf-btn-custom-hover-bg edgtf-btn-custom-hover-color">{labels.REGISTER_FOR_WAITING_LIST_BUTTON}</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )} */}
      </WrapperLayout>
    </div>
  );
};

export default Variation4;
