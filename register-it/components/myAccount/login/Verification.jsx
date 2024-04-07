import React, { useState } from "react";
import Countdown from "react-countdown";
import ReactCodeInput from 'react-verification-code-input';
import AlertMessage from "./AlertMessage";
import Image from 'next/image'

const Verification = ({ onCancel, setStep, ms, verification, event, authenticationId, provider, error, loading }) => {
  const [code, setCode] = useState()
  return (
    <div className="ebs-login-wrapp-inner">
      <span onClick={() => onCancel()} className="btn-inner-close">
        <Image objectFit='contain' layout="fill" src={require("public/img/remove-icon-x2.png")} alt="" />
      </span>
      <h2 className="ebs-login-title">{event.labels.EVENTSITE_AUTHENTICATION_CODE_REQUIRED}</h2>
      <p className="ebs-login-desc">
        {event.labels.EVENTSITE_AUTHENTICATION_EMAIL_CODE_SEND_MSG}
      </p>
      {error && <AlertMessage message={error} />}
      <div className="ebs-login-from">
        <label className="ebs-label-input">
          {/* <span className="ebs-label-title">Enter code</span> */}
          {/* <div className="ebs-verfication-code">
            <input className="ebs-input" type="text" autoComplete="false" />
            <input className="ebs-input" type="text" autoComplete="false" />
            <input className="ebs-input" type="text" autoComplete="false" />
            <input className="ebs-input" type="text" autoComplete="false" />
            <input className="ebs-input" type="text" autoComplete="false" />
            <input className="ebs-input" type="text" autoComplete="false" />
          </div> */}
          <ReactCodeInput className="ebs-verfication-code" type='number' fields={6} onChange={(code) => { setCode(code) }} fieldHeight={50} />
        </label>
        <div style={{ padding: 5 }} className="ebs-label-input">
          <span className="ebs-label-title">{event.labels.EVENTSITE_TIME_LEFT}</span>
          <div className="ebs-verfication-timer">
            {ms && <Countdown
              date={Date.now() + Number(ms)}
              renderer={({ hours, minutes, seconds, completed }) => {
                if (completed) {
                  return (
                    <span>
                      Code Expired...
                    </span>
                  );
                } else {
                  return (
                    <strong>
                      {minutes} : {seconds}
                    </strong>
                  );
                }
              }}
            />}
          </div>
        </div>
        <div
          style={{ paddingTop: 10, paddingBottom: 20 }}
          className="ebs-btn-wrapp"
        >
          <div onClick={() => { verification(event.id, "choose-provider", provider, null, event.url, authenticationId) }} style={{ paddingBottom: 10 }} className="ebs-forgot-password">
            <span >{event.labels.GENERAL_RESEND ? event.labels.GENERAL_RESEND : 'Resend'}</span>
          </div>
          <button disabled={(code && code.length === 6 && !loading) ? false : true} onClick={() => verification(event.id, "verification", provider, code, event.url, authenticationId)} className="btn btn-default">
            {event.labels.GENERAL_SUBMIT}
          </button>
        </div>
      </div>
    </div>
  );
};

export default Verification;
