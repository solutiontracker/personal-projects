import React, { Suspense, useMemo } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector } from "react-redux";
import moment from "moment";
import { getWithExpiry } from "helpers/helper";
const in_array = require("in_array");

const loadModule = (theme, variation) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/event_info/${variation}`)
  );
  return Component;
};

var enumerateDaysBetweenDates = function(startDate, endDate) {
  var dates = [];

  var currDate = moment(startDate).startOf('day');
  var lastDate = moment(endDate).startOf('day');

  while(currDate.add(1, 'days').diff(lastDate) < 0) {
      dates.push(currDate.clone().toDate());
  }

  dates.unshift(startDate);
  dates.push(endDate);

  return dates;
};

const EventInformation = () => {
  const { event } = useSelector(eventSelector);
  let moduleVariation = event.moduleVariations.filter(function (module, i) {
    return in_array(module.alias, ["event_info"]);
  });

  const Component = useMemo(
    () => loadModule(event.theme.slug, moduleVariation[0]["variation_slug"]),
    [event]
  );

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
  

  return (
    <Suspense fallback={''}>
      <Component event={event} moduleVariation={moduleVariation[0]} registerDateEnd={event.registration_end_date_passed === 0 ? true : false} labels={event.labels} regisrationUrl={regisrationUrl} openingHours={event.eventOpeningHours} />
    </Suspense>
  );
};

export default EventInformation;

