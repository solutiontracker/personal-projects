import React, { Suspense, useEffect, useMemo, useRef } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { exhibitorListingSelector, fetchExhibitors, clearState } from "store/Slices/ExhibitorListingSlice";
import {
  incrementFetchLoadCount
} from "store/Slices/GlobalSlice";
import PageLoader from "components/ui-components/PageLoader";
import PageHeader from "../PageHeader";
import { useSelector, useDispatch } from "react-redux";
import Head from "next/head";
const in_array = require("in_array");
import { useRouter } from "next/router";

const loadModule = (theme) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/exhibitor/listing/ExhibitorListing`)
  );
  return Component;
};

const ExhibitorListing = (props) => {
  const { event } = useSelector(eventSelector);
  const dispatch = useDispatch();
  const router = useRouter();
  const eventUrl = event.url;

  const Component = useMemo(
    () => loadModule(event.theme.slug),
    [event]
  );

  const checkModuleStatus = useMemo(()=>(event?.header_data?.top_menu.findIndex((item)=>(item.alias === 'exhibitors'))),[event]);

  const { exhibitors, labels, exhibitorCategories, loading, error} = useSelector(exhibitorListingSelector);

    useEffect(() => {
      if(checkModuleStatus < 0){
        router.push(`/${eventUrl}`);
      }
      if(exhibitors === null){
        dispatch(fetchExhibitors(eventUrl));
      }else{
        dispatch(incrementFetchLoadCount());
      }

      return () => {
        dispatch(clearState());
      }

    }, []);

  return (
    <Suspense fallback={<PageLoader/>}>
      {exhibitors ? (
        <React.Fragment>
          <Head>
            <title>{event.eventsiteModules.exhibitors}</title>
          </Head>
          <PageHeader label={event.labels.EVENTSITE_EXHIBITORS} desc={event.labels.EVENTSITE_EXHIBITORS_SUB} />
          <Component exhibitors={exhibitors} labels = {labels} exhibitorCategories={exhibitorCategories} eventUrl={eventUrl} siteLabels={event.labels} />
        </React.Fragment>
      ) : <PageLoader/> 
      }
    </Suspense>
  );
};

export default ExhibitorListing;
