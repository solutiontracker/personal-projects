import React, { Suspense, useEffect, useMemo } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import moment from "moment";
import {
  globalSelector,
  fetchBanner,
  incrementLoadCount,
} from "store/Slices/GlobalSlice";
import { useSelector, useDispatch } from "react-redux";
const in_array = require("in_array");

const loadModule = (theme) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/sortable_banner/Variation1`)
  );
  return Component;
};

const Banner = () => {
  const { event } = useSelector(eventSelector);
  const { banner_sort } = useSelector(globalSelector);
  const dispatch = useDispatch();

  const eventUrl = event.url;
  
  let moduleVariation = event.moduleVariations.filter(function (module, i) {
    return in_array(module.alias, ["top_banner"]);
  });

  const Component = useMemo(
    () => loadModule(event.theme.slug),
    [event]
  );

  const regisrationUrl = useMemo(()=>{
    let url = '';
    if(parseFloat(event.registration_form_id) === 1){
        url = (event.paymentSettings && parseInt(event.paymentSettings.evensite_additional_attendee) === 1) ? `${process.env.NEXT_APP_REGISTRATION_FLOW_URL}/${event.url}/attendee` : `${process.env.NEXT_APP_REGISTRATION_FLOW_URL}/${event.url}/attendee/manage-attendee`;
    }else{
      url = `${process.env.NEXT_APP_EVENTCENTER_URL}/event/${event.url}/detail/${event.eventsiteSettings.payment_type === 0 ? 'free/' : ''}registration`;
    }

    return url;
  },[event]);

  useEffect(() => {
    if (banner_sort === null) {
      dispatch(incrementLoadCount());
      dispatch(fetchBanner(eventUrl));
    }
  }, [dispatch]);
  return (
    <Suspense fallback={<div></div>}>
      {banner_sort && banner_sort?.length > 0 ? <Component regisrationUrl={regisrationUrl} banner={banner_sort} event={event} countdown={null} /> : null}
    </Suspense>
  );
};

export default Banner;
