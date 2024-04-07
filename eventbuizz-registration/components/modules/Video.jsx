import React, { Suspense, useEffect, useState, useMemo, useRef } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { fetchVideos, videoSelector, clearState } from "store/Slices/VideoSlice";
import {
  incrementLoadCount,
} from "store/Slices/GlobalSlice";
import { useSelector, useDispatch } from "react-redux";
import PageLoader from "components/ui-components/PageLoader";
import LoadMoreButton from 'components/ui-components/LoadMoreButton';
import Head from "next/head";
import PageHeader from "./PageHeader";
const in_array = require("in_array");

const loadModule = (theme, variation) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/video/${variation}`)
  );
  return Component;
};

const Video = (props) => {
  const initialMount = useRef(true);
  const { event } = useSelector(eventSelector);
  const { videos, totalPages, loading, labels } = useSelector(videoSelector);
  const dispatch = useDispatch();
  const eventUrl = event.url;

  let moduleVariation = event.moduleVariations.filter(function (module, i) {
    return in_array(module.alias, ["video"]);
  });
  const home = props.homePage ? props.homePage : false;
  const Component = useMemo(
    () => loadModule(event.theme.slug, moduleVariation[0]["variation_slug"]),
    [event]
  );
  const checkVariation = [
    'Variation1',
    'Variation2',
    'Variation3',
    'Variation4',
    'Variation6',
  ];
  const limit = props.homePage
    ?  (in_array(moduleVariation[0]["variation_slug"], checkVariation) ? 8 : 6 )
    : 50;
  
  const [page, setPage] = useState(1);

  useEffect(() => {
    dispatch(fetchVideos(eventUrl, page, limit, home ));
    if(home){
      dispatch(incrementLoadCount());
    }
    return () => {
      dispatch(clearState());
    }
  }, [page, limit])

  const onPageChange = (page) => {
    if (page > 0) {
      if (page <= totalPages) {
        setPage(page);
      }
    }
  };

  return (
    <Suspense fallback={<PageLoader/>}>
      {(home && videos &&  videos.length > 0 ) || (!home && videos) ? (
        <React.Fragment>
          {!home && <Head>
              <title>{event.eventsiteModules.videos}</title>
          </Head>}
          <div>
            {!home && <PageHeader label={event.labels.EVENTSITE_VIDEOS} />}
            <Component settings={moduleVariation[0]} siteLabels={event.labels} videos={videos} home={home} totalPages={totalPages} eventUrl={eventUrl}
              loadMore={() => {
                if(page < totalPages){
                  return <LoadMoreButton loadingLabel={event.labels.GENERAL_LOAD_MORE} page={page} loading={loading} onPageChange={(data)=> onPageChange(data)} />
                }
            }}
            />
          </div>
        </React.Fragment>
      ) : (!home ? <PageLoader/> : null )}
    </Suspense>
  );
};

export default Video;
