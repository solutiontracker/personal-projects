import React, { Suspense, useEffect, useState, useMemo, useRef } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { speakerSelector, fetchSpeakers } from "store/Slices/SpeakerSlice";
import {
  incrementLoadCount,
} from "store/Slices/GlobalSlice";
import PageLoader from "components/ui-components/PageLoader";
import LoadMoreButton from 'components/ui-components/LoadMoreButton';
import SearchBar from "components/ui-components/SearchBar";

import { useSelector, useDispatch } from "react-redux";
import Head from "next/head";
const in_array = require("in_array");

const loadModule = (theme, variation) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/speaker/${variation}`)
  );
  return Component;
};

const Speaker = (props) => {
  const initialMount = useRef(true);
  const { event } = useSelector(eventSelector);
  const { speakers, totalPages, labels, loading } = useSelector(speakerSelector);
  const dispatch = useDispatch();
  const eventUrl = event.url;

  let moduleVariation = event.moduleVariations.filter(function (module, i) {
    return in_array(module.alias, ["speaker"]);
  });

  const limit = props.homePage
    ? event.speaker_settings.registration_site_limit
    : 12;
  
  const home = props.homePage ? props.homePage : false;
  const Component = useMemo(
    () => loadModule(event.theme.slug, moduleVariation[0]["variation_slug"]),
    [event]
  );

  const [page, setPage] = useState(1);
  const [search, setSearch] = useState("");
  const [value, setValue] = useState("");

  useEffect(() => {
    dispatch(fetchSpeakers(eventUrl, page, limit, search, initialMount.current, home));
  }, [page, limit, search])



  useEffect(() => {
    if (initialMount.current) {
        dispatch(incrementLoadCount());
        initialMount.current = false;
        return;
    }
    const handler = setTimeout(() => {
      setSearch(value);
      setPage(1);
    }, 500);

    return () => {
      clearTimeout(handler);
    };
  }, [value]);

  const onPageChange = (page) => {
    if (page > 0) {
      if (page <= totalPages) {
        setPage(page);
      }
    }
  };

  return (
    <Suspense fallback={<PageLoader/>}>
      {speakers && ((speakers.length > 0 && home) || !home) ? (
        <React.Fragment>  
          {!home && <Head>
            <title>{event.eventsiteModules.speakers}</title>
          </Head>}
          <Component speakers={speakers} siteLabels={event.labels} labels={labels} settings={moduleVariation[0]} listing={!home} history={props.history} event={event} searchBar={()=>{
           return <SearchBar searchLabel={event.labels.EVENTSITE_GENERAL_SEARCH !== undefined ? event.labels.EVENTSITE_GENERAL_SEARCH : "Search..."} loading={loading} setText={(text)=>setValue(text)}  />;
          }}
          
          loadMore={()=>{
            if(page < totalPages){
              return <LoadMoreButton loadingLabel={event.labels.GENERAL_LOAD_MORE} page={page} loading={loading} onPageChange={(data)=> onPageChange(data)} />
            }
          }}
          />
         
        </React.Fragment>
      ) : (!home ? <PageLoader/> : null ) }
    </Suspense>
  );
};

export default Speaker;
