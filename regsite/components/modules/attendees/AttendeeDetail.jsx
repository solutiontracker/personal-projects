import React, { Suspense, useEffect, useMemo, useState } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { attendeeDetailSelector, fetchAttendeeDetail, clearState } from "store/Slices/AttendeeDetailSlice";
import PageLoader from "components/ui-components/PageLoader";
import { useSelector, useDispatch } from "react-redux";
import { useRouter } from 'next/router';
import Head from "next/head";
import PageHeader from "../PageHeader";
import ActiveLink from "components/atoms/ActiveLink";
const loadModule = (theme, variation) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/attendee/detail/${variation}`)
  );
  return Component;
};


const AttendeeDetail = (props) => {

  const router = useRouter();

  const { id } = router.query;

  const { event } = useSelector(eventSelector);

  const { attendee, labels } = useSelector(attendeeDetailSelector);

  const dispatch = useDispatch();

  const eventUrl = event.url;

  const Component = useMemo(
    () => loadModule(event.theme.slug, "Variation1"),
    [event]
  );

  const [breadCrumbs, setbreadCrumbs] = useState([
    {name:event.labels.HOME_PAGE_ATTENDEE, url:`/${eventUrl}`, type:"link"},
    {name:event.labels.EVENTSITE_ATTENDEES, url:`/${eventUrl}/attendees`, type:"link"},
    {name:event.labels.OVERVIEW_OF_ATTENDEES, url:"", type:"name"},
  ]);

  useEffect(() => {
    dispatch(fetchAttendeeDetail(eventUrl, id));
    return () => {
      dispatch(clearState());
    }
  }, []);

  return (
    <Suspense fallback={<PageLoader />}>
      {attendee ? (
        <React.Fragment>
          <Head>
            <title>{event.eventsiteModules.attendees}</title>
          </Head>
          <PageHeader label={event.labels.EVENTSITE_ATTENDEES} desc={event.labels.EVENTSITE_ATTENDEES_SUB} showBreadcrumb={event.eventsiteSettings.show_eventsite_breadcrumbs} breadCrumbs={(type)=>{
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
          <Component attendee={attendee} labels={labels} />
        </React.Fragment>
      ) : <PageLoader />}
    </Suspense>
  );

};

export default AttendeeDetail;
