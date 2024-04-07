import React, { Suspense, useEffect, useMemo } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector, useDispatch } from "react-redux";
import PageLoader from "components/ui-components/PageLoader";
import { fetchDocuments, documentsSelector } from "store/Slices/DocumentsSlice";
import Head from "next/head";
const loadModule = (theme, ) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/documents/Documents`)
  );
  return Component;
};

const Documents = () => {
  const { event } = useSelector(eventSelector);
  const dispatch = useDispatch();
  const eventUrl = event.url;
  
  const CustomComponent = useMemo(
    () => loadModule(event.theme.slug),
    [event]
  );

  useEffect(() => {
    dispatch(fetchDocuments(eventUrl, event.id));
  }, []);
  
  const { documents } = useSelector(documentsSelector);
  

  return (
    <Suspense fallback={''}>
        <Head>
        <title>{event.eventsiteModules.documents}</title>
        </Head>
       {documents ? <CustomComponent documents={documents} documentPage={true} labels={event.labels} eventTimezone={event.timezone.timezone} /> : <PageLoader/>}
    </Suspense>
  );
};

export default Documents;
