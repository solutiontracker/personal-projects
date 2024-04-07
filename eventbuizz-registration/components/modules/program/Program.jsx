import React, { Suspense, useEffect, useState, useMemo, useRef } from "react";
import { eventSelector } from "store/Slices/EventSlice";

import {
  incrementLoadCount,
} from "store/Slices/GlobalSlice";
import { useSelector, useDispatch } from "react-redux";
import { programSelector, fetchPrograms } from "store/Slices/ProgramSlice";

const in_array = require("in_array");

const loadModule = (theme) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/program/Variation1`)
  );
  return Component;
};

const Program = (props) => {

  const { event } = useSelector(eventSelector);
  const dispatch = useDispatch();
  const eventUrl = event.url;
  let moduleVariation = event.moduleVariations.filter(function (module, i) {
    return in_array(module.alias, ["agenda"]);
  });
  const Component = useMemo(
    () => loadModule(event.theme.slug),
    [event]
  );
  const { programs, tracks, labels } = useSelector(programSelector);


  useEffect(() => {
    if (programs === null) {
      dispatch(fetchPrograms(eventUrl));
      dispatch(incrementLoadCount());
    }
  }, [])


  return (
    <Suspense fallback={''}>
      {programs ? (
        <React.Fragment>
          <Component programs={programs} agendaSettings={event.agenda_settings} tracks={tracks} siteLabels={event.labels} eventUrl={eventUrl} language_id={event.language_id} showWorkshop={event.agenda_settings.agenda_collapse_workshop} />
        </React.Fragment>
      ) : null
      }
    </Suspense>
  );
};

export default Program;
