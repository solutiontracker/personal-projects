import React, { Suspense, useEffect, useMemo, useRef, useState } from "react";
import ActiveLink from "components/atoms/ActiveLink";
import { eventSelector } from "store/Slices/EventSlice";
import { exhibitorDetailSelector, fetchExhibitor, clearState } from "store/Slices/ExhibitorDetailSlice";
import {
  incrementLoadCount,
} from "store/Slices/GlobalSlice";
import PageHeader from "../PageHeader";
import PageLoader from "components/ui-components/PageLoader";
import { useSelector, useDispatch } from "react-redux";
import { useRouter } from 'next/router';
import Head from "next/head";

const in_array = require("in_array");

const loadModule = (theme) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/exhibitor/detail/ExhibitorDetail`)
  );
  return Component;
};

const ExhibitorDetail = (props) => {

  const router = useRouter();

  const { id } = router.query;

  const { event } = useSelector(eventSelector);

  const dispatch = useDispatch();

  const eventUrl = event.url;

  const Component = useMemo(
    () => loadModule(event.theme.slug),
    [event]
  );

  const checkModuleStatus = useMemo(()=>(event?.header_data?.top_menu.findIndex((item)=>(item.alias === 'exhibitors'))),[event]);


  const [breadCrumbs, setbreadCrumbs] = useState([
    {name:event.labels.HOME_PAGE_EXHIBIOR, url:`/${eventUrl}`, type:"link"},
    {name:event.labels.EVENTSITE_EXHIBITORS, url:`/${eventUrl}/exhibitors`, type:"link"},
    {name:event.labels.OVERVIEW_OF_EXHIBITORS, url:"", type:"name"},
  ]);
  useEffect(() => {
    //dispatch(incrementLoadCount());
    if(checkModuleStatus < 0){
      router.push(`/${eventUrl}`);
    }
    dispatch(fetchExhibitor(eventUrl, id));
    return () => {
      dispatch(clearState());
    }
  }, []);

  const { exhibitor, labels, documents, loading, error } = useSelector(exhibitorDetailSelector);

  return (
    <Suspense fallback={<PageLoader />}>
      {exhibitor ? (
        <React.Fragment>
          <Head>
            <title>{event.eventsiteModules.exhibitors}</title>
          </Head>
          <PageHeader label={event.labels.EVENTSITE_EXHIBITORS} desc={event.labels.EVENTSITE_EXHIBITORS_SUB} showBreadcrumb={event.eventsiteSettings.show_eventsite_breadcrumbs} breadCrumbs={(type)=>{
            return ( <nav aria-label="breadcrumb" className={`ebs-breadcrumbs ${type !== "background" ? 'ebs-dark': ''}`}>
            <ul className="breadcrumb">
              {breadCrumbs.map((crumb, i) => (
                <li className="breadcrumb-item" key={i}>
                  {crumb.type === "name" ? crumb.name : <ActiveLink href={crumb.url} >{crumb.name}</ActiveLink>}
                </li>
              ))}
            </ul>
            </nav>)
        }} />
          <Component exhibitor={exhibitor} labels={event.labels} documents={documents} moduleName={event.eventsiteModules.exhibitors} eventTimezone={event.timezone.timezone} />
        </React.Fragment>
      ) : <PageLoader />
      }
    </Suspense>
  );

};

export default ExhibitorDetail;
