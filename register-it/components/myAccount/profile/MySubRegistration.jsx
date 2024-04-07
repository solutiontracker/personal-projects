import React, {useEffect, useState} from 'react'
import MySubRegForm from './MySubRegForm';
import {
    fetchSubRegistrationData,
    mySubRegistrationSelector,
    updateSubRegistrationData,
  } from "store/Slices/myAccount/mysubRegistrationSlice";
  import { eventSelector } from "store/Slices/EventSlice";
  import { useSelector, useDispatch } from "react-redux";
import PageLoader from 'components/ui-components/PageLoader';
const MySubRegistration = () => {
    const { event } = useSelector(eventSelector);
    const dispatch = useDispatch();
    useEffect(() => {
      dispatch(fetchSubRegistrationData(event.id, event.url));
    }, []);
    const { subRegistration, loading, updating, alert, error, } = useSelector(mySubRegistrationSelector);

    if(loading){
      return <PageLoader/>;
    }
    return (
      <div className="edgtf-container ebs-my-profile-area pb-5">
        <div className="edgtf-container-inner container">
          <div className="ebs-header">
            <h2>{event.labels.EVENTSITE_QUESTIONAIRS_MAIN}</h2>
          </div>
          <div className="wrapper-inner-content network-category-sec">
            {subRegistration !== null ? <MySubRegForm subRegistration={subRegistration} event={event} updating={updating} alert={alert} error={error}  /> : 
              <div>
               {event.labels.GENERAL_NO_RECORD ? event.labels.GENERAL_NO_RECORD : " You have no answers yet..."}
              </div>
             }
          </div>
        </div>
      </div>

  )
}

export default MySubRegistration