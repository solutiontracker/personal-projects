import React, { Suspense, useMemo } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import moment from 'moment';
import { getWithExpiry } from "helpers/helper";
// import {
//   incrementLoadCount,
// } from "store/Slices/GlobalSlice";
import { useSelector } from "react-redux";
const in_array = require("in_array");

const loadModule = (theme, variation) => {
  console.log(variation);
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/register-now/${variation}`)
  );
  return Component;
};

const RegisterNow = () => {
  const { event } = useSelector(eventSelector);
  // const dispatch = useDispatch();
  // const eventUrl = event.url;
  let moduleVariation = event.moduleVariations.filter(function (module, i) {
    return in_array(module.alias, ["register_now"]);
  });
  const Component = useMemo(
    () => loadModule(event.theme.slug, moduleVariation[0]["variation_slug"]),
    [event]
  );

  const checkTickets = useMemo(()=>{
    let ticketsSet = false;
    if(parseFloat(event.eventsiteSettings.ticket_left) > 0){
        ticketsSet = true;
    }
    let remainingTickets =  event.eventsiteSettings.ticket_left - event.totalAttendees;

    return { ticketsSet, remainingTickets };
  },[event]);
  
  const regisrationUrl = useMemo(()=>{
    let url = '';
    if(parseFloat(event.registration_form_id) === 1){
        url = (event.paymentSettings && parseInt(event.paymentSettings.evensite_additional_attendee) === 1) ? `${process.env.NEXT_APP_REGISTRATION_FLOW_URL}/${event.url}/attendee` : `${process.env.NEXT_APP_REGISTRATION_FLOW_URL}/${event.url}/attendee/manage-attendee`;
    }else{
      url = `${process.env.NEXT_APP_EVENTCENTER_URL}/event/${event.url}/detail/${event.eventsiteSettings.payment_type === 0 ? 'free/' : ''}registration`;
    }

    if(event.eventsiteSettings.manage_package === 1){
       url = `/${event.url}/registration_packages`;
    }
    
    let autoregister = getWithExpiry(`autoregister_${event.url}`);
    if(autoregister !== null){
        url = `${process.env.NEXT_APP_REGISTRATION_FLOW_URL}/${event.url}/attendee/autoregister/${autoregister}`;
    }

    return url;
  },[event]);


  const waitingList = useMemo(()=>{
    if(event.waitinglistSettings){
        return event.waitinglistSettings.status;
    } 
    return 0;
  },[event]);

  return (
    <Suspense fallback={''}>
      <Component eventSiteSettings={event.eventsiteSettings} eventTimeZone={event.timezone.timezone} registrationFormInfo={event.registration_form_info} registrationUrl={regisrationUrl} labels={event.labels} registerDateEnd={event.registration_end_date_passed === 0 ? true : false} checkTickets={checkTickets}  waitingList={waitingList} moduleVariation={moduleVariation[0]} /> 
    </Suspense>
  );
};

export default RegisterNow;
