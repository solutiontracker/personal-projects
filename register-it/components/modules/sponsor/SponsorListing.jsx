import React, { Suspense, useEffect, useMemo, useRef } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { sponsorListingSelector, fetchSponsors, clearState } from "store/Slices/SponsorListingSlice";
import {
  incrementFetchLoadCount
} from "store/Slices/GlobalSlice";
import PageLoader from "components/ui-components/PageLoader";
import PageHeader from "../PageHeader";
import { useRouter } from "next/router";

import { useSelector, useDispatch } from "react-redux";
import Head from "next/head";
const in_array = require("in_array");

const loadModule = (theme) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/sponsor/listing/SponsorListing`)
  );
  return Component;
};

const SponsorListing = (props) => {
  const { event } = useSelector(eventSelector);
  const dispatch = useDispatch();
  const router = useRouter();
  const eventUrl = event.url;

  const Component = useMemo(
    () => loadModule(event.theme.slug),
    [event]
  );

  const checkModuleStatus = useMemo(()=>(event?.header_data?.top_menu.findIndex((item)=>(item.alias === 'sponsors'))),[event]);

  const { sponsors, labels, sponsorCategories, loading, error} = useSelector(sponsorListingSelector);

    useEffect(() => {
      if(checkModuleStatus < 0){
        router.push(`/${eventUrl}`);
      }
      if(sponsors === null || sponsorCategories === null) {
        dispatch(fetchSponsors(eventUrl));
      }else{
        dispatch(incrementFetchLoadCount());
      }
      return () => {
        dispatch(clearState());
      }
    }, []);
  return (
    <Suspense fallback={<PageLoader/>}>
      {sponsors ? (
        <React.Fragment>
          <Head>
          <title>{event.eventsiteModules.sponsors}</title>
          </Head>
          <PageHeader label={event.labels.EVENTSITE_SPONSORS} desc={event.labels.EVENTSITE_SPONSORS_SUB}/>
          <Component sponsors={sponsors} labels = {labels} sponsorCategories={sponsorCategories} eventUrl={eventUrl} siteLabels={event.labels} />
        </React.Fragment>
      ) : <PageLoader/> 
      }
    </Suspense>
  );
};

export default SponsorListing;
