"use client";
import { useTranslations } from 'next-intl';
import React from 'react'
import Countdown, { zeroPad } from "react-countdown";




// Renderer callback with condition
const Renderer = ({ months,days,hours, minutes, seconds, completed, completeText }:any) => {
    if (completed) {
      // Render a complete state
      return (<div className="col-12">
      <p>{completeText}</p>
    </div>);
    } else {
      // Render a countdown
      return (
        <React.Fragment>
              {Math.floor(days/30) > 0 && zeroPad(Math.floor(days/30))+':' }{zeroPad(Math.floor(days%30))}:{zeroPad(hours)}:{zeroPad(minutes)}:{zeroPad(seconds)}
        </React.Fragment>
      );
    }
  };

const Index = ({date}:any) => {
  const t = useTranslations('manage-orders-page');

  return (
    <Countdown date={date} renderer={props=> <Renderer {...props} completeText={t('date_ended')} />} />
  )
}

export default Index