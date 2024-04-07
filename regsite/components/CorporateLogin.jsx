import React, {useState} from 'react';
import { useDispatch, useSelector } from 'react-redux';
import {
  postCorporateLogin
} from "store/Slices/GlobalSlice";
import {
  eventSelector
} from "store/Slices/EventSlice";
import { useRouter } from 'next/router';

const CorporateLogin = () => {
    const dispatch = useDispatch();
    const {event} = useSelector(eventSelector);
    const [email, setEmail] = useState('');
    const [registrationCode, setRegistrationCode] = useState('');
    const router = useRouter();
    const handleSubmit = (e) =>{
      if(registrationCode === event.eventsiteSettings.registration_code){
        dispatch(postCorporateLogin());
        localStorage.setItem(`event${event.id}UserCorporateLogin`, JSON.stringify(true));
        router.push(`/${event.url}`);
      }
    }
    return (
      <div className="ebs-corporate-login">
        <div className="ebs-corporate-fields">
          <div className="ebs-event-logo">
            <img src="https://dev.eventbuizz.com/assets/event/branding/114603_image_1671841641642074350.png" alt="" />
          </div>
          <div className="ebs-event-description">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore
          </div>
          <form onSubmit={(e)=>{ e.preventDefault(); handleSubmit();}}>
          <div className="ebs-input-field">
            <input type="email" placeholder=' ' value={email} required onChange={(e)=>{setEmail(e.currentTarget.value)}} />
            <label className="title">Email <em>*</em></label>
          </div>
          <div className="ebs-input-field">
            <input type="text" placeholder=' ' value={registrationCode} required onChange={(e)=>{setRegistrationCode(e.currentTarget.value)}} />
            <label className="title">Enter registration code</label>
          </div>
          <button className="btn btn-default">Access Site</button>
          </form>
        </div>
      </div>
    );
  }


export default CorporateLogin;
