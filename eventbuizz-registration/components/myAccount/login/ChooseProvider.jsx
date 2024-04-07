import React, { useState, useEffect } from "react";
import AlertMessage from "./AlertMessage";
import Image from 'next/image'

const ChooseProvider = ({
  onCancel,
  getAttendee,
  verification,
  authenticationId,
  attendee,
  event,
  error,
  provider,
  loading
}) => {
  const [providerloc, setProviderLoc] = useState(provider);
  useEffect(() => {
    getAttendee(authenticationId);
  }, []);

  return (
    <div className="ebs-login-wrapp-inner">
      <span onClick={() => onCancel()} className="btn-inner-close">
        <Image objectFit='contain' layout="fill" src={require("public/img/remove-icon-x2.png")} alt="" />
      </span>
      <h2 className="ebs-login-title">{event.labels.EVENTSITE_TWO_FACTOR_AUTHENTICATION}</h2>
      <p className="ebs-login-desc">{event.labels.EVENTSITE_AUTHENTICATION_CONTACT_METHOD}</p>
      {error && <AlertMessage message={error}/>}
      {attendee && (
        <React.Fragment>
          <div className="ebs-form-accept">
            <label className="ebs-label-accept">
              <input
                type="radio"
                name="auth"
                value='email'
                defaultChecked="true"
                onChange={(e) => {
                  setProviderLoc(e.target.value);
                }}
              />
              <span className="ebs-accept-text">{attendee.email}</span>
            </label>
          </div>
          <div className="ebs-form-accept">
            <label className="ebs-label-accept">
              <input
                type="radio"
                name="auth"
                value='sms'
                onChange={(e) => {
                  setProviderLoc(e.target.value);
                }}
              />
              <span className="ebs-accept-text">{attendee.phone}</span>
            </label>
          </div>
        </React.Fragment>
      )}
      <div className="ebs-btn-wrapp">
        <button className="btn btn-default" type="submit" disabled={(attendee && !loading) ? false : true} onClick={()=>{verification(event.id, "choose-provider", providerloc, null, event.url, authenticationId)}}>
        {event.labels.GENERAL_SUBMIT}
        </button>
      </div>
    </div>
  );
};

export default ChooseProvider;
