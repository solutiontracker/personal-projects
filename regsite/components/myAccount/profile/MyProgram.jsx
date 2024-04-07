import React, { Suspense, useEffect, useState, useMemo, useRef } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import PageLoader from "components/ui-components/PageLoader";
import { useSelector, useDispatch } from "react-redux";
import { myProgramListingSelector, fetchMyPrograms ,clearState } from "store/Slices/myAccount/MyProgramListingSlice";

const in_array = require("in_array");

const loadModule = (theme) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/program/listing/Variation1`)
  );
  return Component;
};

const MyProgram = () => {
  const { event } = useSelector(eventSelector);
  const dispatch = useDispatch();
  const eventUrl = event.url;
  const Component = useMemo(
    () => loadModule(event.theme.slug),
    [event]
  );
  const  {myPrograms, tracks, totalPages, labels, loading } = useSelector(myProgramListingSelector);
  useEffect(() => {
      dispatch(fetchMyPrograms(eventUrl, event.id));
    return ()=>{
      clearState();
    }
  }, []);


  return (
    <Suspense fallback={<PageLoader/>}>
      {myPrograms && myPrograms.length > 0 ? (
        <React.Fragment>
          <Component programs={myPrograms} eventUrl={eventUrl} tracks={tracks} filters={false} showWorkshop={event.agenda_settings.agenda_collapse_workshop} siteLabels={event.labels} agendaSettings={event.agenda_settings} eventLanguageId={event.language_id} />
        </React.Fragment>
      ) : loading && <PageLoader/> }
      {(!loading && (!myPrograms || myPrograms.length <= 0) ) && <h4 style={{textAlign:"center"}}>No programs found</h4> }
    </Suspense>
  );
};

export default MyProgram;
