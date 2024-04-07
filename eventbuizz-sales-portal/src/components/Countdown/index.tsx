"use client";
import React from 'react'
import Countdown, { zeroPad } from "react-countdown";


const Completionist = () =>  
  <div className="col-12">
    <h2>This event is going on.</h2>
  </div>
;

// Renderer callback with condition
const renderer = ({ months,days,hours, minutes, seconds, completed }:any) => {
    if (completed) {
      // Render a complete state
      return <Completionist />;
    } else {
      // Render a countdown
      return (
        <React.Fragment>
           <div className="col-md-7">
            <div style={{margin: '0 -15px'}} className="countdown-wrapp d-flex">
              {Math.floor(days/30) > 0 &&<span className="edgtf-countdown is-countdown">
                <span className="countdown-amount">{zeroPad(Math.floor(days/30))}</span>
                <span className="countdown-period">Months</span>
              </span>}
              <span className="edgtf-countdown is-countdown">
                <span className="countdown-amount">{zeroPad(Math.floor(days%30))}</span>
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

const Index = ({date}:any) => {
  return (
    <Countdown date={date} renderer={renderer} />
  )
}

export default Index