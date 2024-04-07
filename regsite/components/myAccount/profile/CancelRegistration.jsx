import React, { useEffect, useMemo, useState } from 'react';
import ActiveLink from "components/atoms/ActiveLink";
import { fetchProfileData, profileSelector, fetchInvoiceData, cancelRegistrationRequest } from 'store/Slices/myAccount/profileSlice';
import { userSelector } from 'store/Slices/myAccount/userSlice';
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector, useDispatch } from "react-redux";
import PageLoader from 'components/ui-components/PageLoader';
import moment from 'moment';
import Image from 'next/image'
import { useRouter } from 'next/router';


const CancelRegistration = () => {
  const { event } = useSelector(eventSelector);
  const { loggedout } = useSelector(userSelector);
  const dispatch = useDispatch();
  const router = useRouter();
  const [comment, setComment] = useState("");
  const [cancelling, setCancelling] = useState(false);
  const enable_cancel = JSON.parse(localStorage.getItem(`EI${event.url}EC`));
  const order_attendee_count = JSON.parse(localStorage.getItem(`EI${event.url}EC_COUNT`));
  const [cancelOption, setCancelOption] = useState(order_attendee_count > 1 ? "whole_order" :"registration_only");
  
  if(enable_cancel != true){
    router.push(`/${event.url}`);
  }
  
  const cancellationDatePassed = useMemo(()=>{
    if(event.eventsiteSettings.cancellation_date === "0000-00-00 00:00:00"){
      return 0;
    }
    let dateToday = moment();
    let cancelationEndDate = moment(`${moment(event.eventsiteSettings.cancellation_date).format("YYYY-MM-DD")} ${event.eventsiteSettings.cancellation_end_time}`);
    let passed = cancelationEndDate.diff(dateToday);
    return passed > 0 ? 0 : 1;
  },[event]);

  if(cancellationDatePassed !== 0){
    router.push(`/${event.url}`);
  }

  const cancel = async () => {
    setCancelling(true);
   dispatch(cancelRegistrationRequest(event.id, event.url, {comment:comment, cancelOption:cancelOption})) 
  }

  if(loggedout){
    if (event?.eventsiteSettings?.third_party_redirect_url && Number(event?.eventsiteSettings?.third_party_redirect) === 1) {
      window.location = event?.eventsiteSettings?.third_party_redirect_url;
    }
    else {
        router.push(
            `/${event.url}`
        );
    }
   }

  return (
    <React.Fragment>
     {(cancellationDatePassed !== undefined && cancellationDatePassed === 0) &&  (enable_cancel == true)? <div className="edgtf-container ebs-my-profile-area pb-5">
        <div className="edgtf-container-inner container">
        <div className="ebs-cancel-registaration">
          <div className="ebs-header" style={{display:'block'}}>
            <h2><i className="material-icons">highlight_off</i>{event.labels.CANCEL_REGISTRATION_ORDER_HEADING}</h2>
            {/* <span style={{marginLeft:'6px'}}>{event.labels.REGISTRATION_CANCEL_MESSAGE}</span> */}
          </div>
            <div className="generic-form">
            {order_attendee_count > 1 && <div className='mb-3'>
                <div class="mb-3" style={{textAlign:'left', fontSize:'16px', color:'#000'}}>
                        {event.labels.REGISTRATION_CANCEL_COMPLETE_ORDER !== undefined ? event.labels.REGISTRATION_CANCEL_COMPLETE_ORDER : 'Cancel complete order:'}
                </div>
                <div className='form-check mb-0 form-check-inline me-5'>
                   
                    <input
                      className='form-check-input mt-1'
                      type="radio"
                      name="canceloption" 
                      id="whole_order" 
                      value="whole_order"
                      checked={cancelOption === "whole_order" ? true : false }
                      onChange={(e)=>{setCancelOption(e.target.value)}}
                    />
                     <label for="whole_order" className="form-check-label text-dark">
                      {event.labels.GENERAL_YES !== undefined ? event.labels.GENERAL_YES : 'Yes'}
                    </label>
                </div>
                  <div className='form-check-inline mb-0 form-check'>
                    <input
                      className='form-check-input mt-1'
                      type="radio"
                      name="canceloption" 
                      id="registration_only" 
                      value="registration_only"
                      checked={cancelOption === "registration_only" ? true : false }
                      onChange={(e)=>{setCancelOption(e.target.value)}}
                    />
                    <label for="registration_only" className="form-check-label text-dark">
                      {event.labels.GENERAL_NO !== undefined ? event.labels.GENERAL_NO : 'No'}
                    </label>
                  </div>
            </div>}
              <p class="mb-2 mt-0" style={{textAlign:'left', fontSize:'16px', color:'#000'}}>{event.labels.REGISTRATION_CANCEL_COMMENT_LABEL}</p>
              <textarea
                placeholder={event.labels.REGISTRATION_CANCEL_COMMENT_LABEL}
                cols={30}
                rows={5}
                value={comment}
                onChange={(e)=> setComment(e.currentTarget.value)}
              ></textarea>
              <div className='pt-3'>
                <button disabled={cancelling} className="btn px-3 btn-save-next btn-loader btn-danger rounded-1" onClick={()=>{ cancel() }} > {event.labels.REGISTRATION_CANCEL_BUTTON_LABEL} </button>
              </div>

            </div>

        </div>
        </div>
      </div> : <PageLoader/>}
    </React.Fragment>
  )
}

export default CancelRegistration;