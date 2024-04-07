import React, { Suspense, useEffect, useMemo, useRef } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { cmsDetailSelector, fetchCmsPage, clearState } from "store/Slices/CmsDetailSlice";
import PageLoader from "components/ui-components/PageLoader";
import { useSelector, useDispatch } from "react-redux";
import { useRouter } from 'next/router';
import Head from 'next/head'

const in_array = require("in_array");

const loadModule = (theme) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/cms/CmsDetail`)
  );
  return Component;
};

const CmsDetail = (props) => {

  const router = useRouter();

  const { id } = router.query;

  const { event } = useSelector(eventSelector);

  const dispatch = useDispatch();

  const eventUrl = event.url;

  const Component = useMemo(
    () => loadModule(event.theme.slug),
    [event]
  );

  useEffect(() => {
    dispatch(fetchCmsPage(eventUrl, props.moduleName, id));
    return () => {
      dispatch(clearState());
    }
  }, [props.moduleName, id]);

  const informationModules = {
    additional_information: "additional_info_menu",
    general_information: "general_info_menu",
    practicalinformation: "practical_info_menu",
  };

  const informationModulesImage = {
    additional_information: "additional_info",
    general_information: "general_info",
    practicalinformation: "event_info",
  };

  const { cmsPage, labels, loading, error } = useSelector(cmsDetailSelector);

  return (
    <Suspense fallback={<PageLoader />}>
      {cmsPage ? (
        <React.Fragment>
          <Component detail={cmsPage} labels={labels} moduleName={props.moduleName} eventUrl={event.url} eventSiteModuleName={event.eventsiteModules[props.moduleName]} breadCrumbData={event.header_data[informationModules[props.moduleName]]} eventsiteSettings={event.eventsiteSettings} />
        </React.Fragment>
      ) : <PageLoader />
      }
    </Suspense>
  );
};

export default CmsDetail;
