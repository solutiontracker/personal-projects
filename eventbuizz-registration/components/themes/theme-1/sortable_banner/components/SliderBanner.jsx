import React, {useEffect} from 'react';
import Slider from "react-slick";
import Countdown, { zeroPad } from "react-countdown";
const Completionist = ({ labels }) =>  
  <div className="col-12">
    <h2>{labels.RESGISTRATION_SITE_THIS_EVENT_IS_GOING_ON ? labels.RESGISTRATION_SITE_THIS_EVENT_IS_GOING_ON : "This event is going on."}</h2>
  </div>
;

const SliderBanner = (props) => {
    var settings = {
        dots: true,
        fade: true,
        autoplay: true,
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        draggable: false,
        adaptiveHeight: true,
        responsive: [
        {
          breakpoint: 1200,
          settings: {
            arrows: false
          }
        }  
        ]
      };
  // Renderer callback with condition
  const renderer = ({ days, hours, minutes, seconds, completed }) => {
    if (completed) {
      // Render a complete state
      return <Completionist labels={this.props.labels}/>;
    } else {
      // Render a countdown
      return (
        <React.Fragment>
          <div className="col-md-7">
            <div style={{ margin: '0 -15px' }} className="countdown-wrapp d-flex">
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
          </div>
          <div className="col-md-5"><h2>Countdown to Conference </h2></div>
        </React.Fragment>
      );
    }
  };

      // useEffect(() => {
      //   window.addEventListener("scroll", function (e) {
      //       var scrolled = window.pageYOffset;
      //       const background = document.querySelectorAll(".parallax-backgroud");
      //       for (let i = 0; i < background.length; i++) {
      //         const element = background[i];
      //         element.style.backgroundPosition = `50%  ${(scrolled * 0.2)}px`;
              
      //       }
      //     });
      // }, [])
    return (
        <div className={`banner-wrapper ${props.countdown && 'countdown'} ${props.fullscreen && 'slider-fullscreen'}`}>
        <Slider {...settings}>
          {props.children}
        </Slider>
        {props.countdown && (
          <div className="timer-wrapper">
            <div className="container">
              <div className="row d-flex align-items-center">
                <Countdown date={props.countdown} renderer={renderer} />
              </div>
            </div>
          </div>
        )}
      </div>
    )
}

export default SliderBanner
