import React, { useEffect, useState } from "react";
import 'public/sass/app.scss';
import 'photoswipe/dist/photoswipe.css';
import 'public/sass/404.scss';
import 'public/sass/_packages.scss';
import { fetchEvent, eventSelector, updateCookie, checkVerificationCode, setVerificationids } from "store/Slices/EventSlice";
import { store } from "store/store";
import { Provider } from "react-redux";
import { useRouter } from 'next/router';
import FullPageLoader from "components/ui-components/FullPageLoader";
import Theme from "components/Theme";
import {setWithExpiry, getWithExpiry} from "helpers/helper";
import ErrorBoundary from 'components/ErrorBoundary';
require("moment/min/locales.min");
function MyApp({ Component, pageProps }) {

  const router = useRouter();
  const { event, layout, autoregister, validateAttendee, verification_id } = router.query;
  const [_eventObj, setEventObj] = useState({});

  if(autoregister !== undefined && typeof window !== 'undefined'){
    let autoregister_stored = getWithExpiry(`autoregister_${event}`);
    if(autoregister_stored === null || autoregister_stored !== autoregister){
      setWithExpiry(`autoregister_${event}`, autoregister, 300000);
    }
    router.replace(`/${event}`, undefined, { shallow: true });
  }
  
  if(validateAttendee !== undefined && verification_id !== undefined && typeof window !== 'undefined'){
    store.dispatch(setVerificationids({validateAttendee, verification_id}));
    router.replace(`/${event}/validate-attendee`);
  }

  useEffect(() => {
    if (event) {
      store.dispatch(fetchEvent(event, layout));
      if(typeof window !== 'undefined' && localStorage.getItem(`cookie_${event}`) !== null){
        store.dispatch(updateCookie(localStorage.getItem(`cookie_${event}`), event));
      }
    }
  }, [store, event]);

  useEffect(() => {
    store.subscribe(() => {
      if (Object.keys(_eventObj)?.length === 0) {
        setEventObj(store.getState().event);
      }
    });
  }, [_eventObj]);

  return (
    <React.Fragment>
      {_eventObj.loading && <FullPageLoader className="fixed" />}
      <div style={{ transform: 'none' }} id="App">
        <Provider store={store}>
          {_eventObj.event && (
            <>
              <Theme data={_eventObj.event} />
            </>
          )}
           <ErrorBoundary>
              <Component {...pageProps} />
           </ErrorBoundary>
        </Provider>
      </div>
    </React.Fragment>
  );
}

export default MyApp
