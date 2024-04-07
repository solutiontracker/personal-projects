import React, { Suspense, useEffect, useMemo, useRef, useState } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { sponsorDetailSelector, fetchSponsor, clearState } from "store/Slices/SponsorDetailSlice";
import PageLoader from "components/ui-components/PageLoader";
import { useSelector, useDispatch } from "react-redux";
import { useRouter } from 'next/router';
import Head from "next/head";
import PageHeader from "../PageHeader";
import ActiveLink from "components/atoms/ActiveLink";
const in_array = require("in_array");

const loadModule = (theme) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/sponsor/detail/SponsorDetail`)
  );
  return Component;
};

const SponsorDetail = (props) => {

  const router = useRouter();

  const { id } = router.query;

  const { event } = useSelector(eventSelector);

  const dispatch = useDispatch();

  const eventUrl = event.url;

  const Component = useMemo(
    () => loadModule(event.theme.slug),
    [event]
  );

  const checkModuleStatus = useMemo(()=>(event?.header_data?.top_menu.findIndex((item)=>(item.alias === 'sponsors'))),[event]);

  useEffect(() => {
    if(checkModuleStatus < 0){
      router.push(`/${eventUrl}`);
    }
    dispatch(fetchSponsor(eventUrl, id));
    return () => {
      dispatch(clearState());
    }
  }, []);
  
  const [breadCrumbs, setbreadCrumbs] = useState([
    {name:event.labels.HOME_PAGE_SPONSOR, url:`/${eventUrl}`, type:"link"},
    {name:event.labels.EVENTSITE_SPONSORS, url:`/${eventUrl}/sponsors`, type:"link"},
    {name:event.labels.OVERVIEW_OF_SPONSORS, url:"", type:"name"},
  ]);

  const { sponsor, labels, documents, loading, error } = useSelector(sponsorDetailSelector);

  return (
    <Suspense fallback={<PageLoader />}>
      {sponsor ? (
        <React.Fragment>
          <Head>
          <title>{event.eventsiteModules.sponsors}</title>
          </Head>
          <PageHeader label={event.labels.EVENTSITE_SPONSORS} desc={event.labels.EVENTSITE_SPONSORS_SUB} showBreadcrumb={event.eventsiteSettings.show_eventsite_breadcrumbs} breadCrumbs={(type)=>{
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
          <Component sponsor={sponsor} labels={event.labels} documents={documents} sponsorSettings={event.sponsor_settings} moduleName={event.eventsiteModules.sponsors} eventTimezone={event.timezone.timezone} />
        </React.Fragment>
      ) : <PageLoader />
      }
    </Suspense>
  );
};

export default SponsorDetail;
