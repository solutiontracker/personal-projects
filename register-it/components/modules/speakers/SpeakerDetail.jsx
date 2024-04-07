import React, { Suspense, useEffect, useMemo, useState } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import PageLoader from "components/ui-components/PageLoader";
import { speakerDetailSelector, fetchSpeakerDetail, clearState } from "store/Slices/SpeakerDetailSlice";
import { useSelector, useDispatch } from "react-redux";
import { useRouter } from 'next/router';
import Head from "next/head";
import PageHeader from "../PageHeader";
import ActiveLink from "components/atoms/ActiveLink";
const loadModule = (theme, variation) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/speaker/detail/${variation}`)
  );
  return Component;
};

const SpeakerDetail = (props) => {

  const router = useRouter();

  const { id } = router.query;

  const { event } = useSelector(eventSelector);

  const { speaker, labels } = useSelector(speakerDetailSelector);

  const dispatch = useDispatch();

  const eventUrl = event.url;

  const Component = useMemo(
    () => loadModule(event.theme.slug, "Variation1"),
    [event]
  );

  const [breadCrumbs, setbreadCrumbs] = useState([
    {name:event.labels.HOME_PAGE, url:`/${eventUrl}`, type:"link"},
    {name:event.labels.EVENTSITE_SPEAKERS, url:`/${eventUrl}/speakers`, type:"link"},
    {name:event.labels.OVERVIEW_OF_SPEAKER, url:"", type:"name"},
  ]);

  useEffect(() => {
    dispatch(fetchSpeakerDetail(eventUrl, id));
    return () => {
      dispatch(clearState());
    }
  }, [id]);

  return (
    <Suspense fallback={<PageLoader />}>
      {speaker ? (
        <React.Fragment>
          <Head>
            <title>{event.eventsiteModules.speakers}</title>
          </Head>
          <PageHeader label={event.labels.EVENTSITE_SPEAKERS} desc={event.labels.EVENTSITE_SPEAKERS_SUB} showBreadcrumb={event.eventsiteSettings.show_eventsite_breadcrumbs} breadCrumbs={(type)=>{
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
          <Component event={event} speaker={speaker} labels={event.labels} agendaSettings={event.agenda_settings} moduleName={event.eventsiteModules.speakers} siteLabels={event.labels} eventUrl={eventUrl} eventLanguageId={event.language_id} showWorkshop={event.agenda_settings.agenda_collapse_workshop} />
        </React.Fragment>
      ) : <PageLoader />}
    </Suspense>
  );
};

export default SpeakerDetail;
