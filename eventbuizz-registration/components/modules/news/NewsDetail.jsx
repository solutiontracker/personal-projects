import React, { useState, useEffect, useMemo, Suspense } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { newsDetailSelector, fetchNewsDetail, clearState } from "store/Slices/NewsDetailSlice";
import { useSelector, useDispatch } from "react-redux";
import PageLoader from "components/ui-components/PageLoader";
import { useRouter } from 'next/router';
import Head from 'next/head'

const loadModule = (theme, variation) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/news/detail/${variation}`)
  );
  return Component;
};

const NewsDetail = (props) => {

  const router = useRouter();

  const { id } = router.query;

  const { event } = useSelector(eventSelector);

  const { news, labels } = useSelector(newsDetailSelector);

  const dispatch = useDispatch();

  const eventUrl = event.url;

  const Component = useMemo(
    () => loadModule(event.theme.slug, "Variation1"),
    [event]
  ); 

  const [sidebar, setSidebar] = useState(false);
  useEffect(() => {
    dispatch(fetchNewsDetail(eventUrl, id));
    return () => {
      dispatch(clearState());
    }
  }, []);

  return (
    <Suspense fallback={<PageLoader/>}>
      {news ? (
        <React.Fragment>
          {/* <Head>
            <title>{news.title}</title>
            <meta property="og:title" content={news.title} />
            <meta property="og:type" content="Event" />
            <meta
                property="og:image"
                content={
                        news.image
                        ? process.env.NEXT_APP_EVENTCENTER_URL +
                        "/assets/eventsite_news/" +
                        news.image
                        : event.settings.header_logo &&
                            event.settings.header_logo !== ""
                            ? process.env.NEXT_APP_EVENTCENTER_URL +
                            "/assets/event/branding/" +
                            event.settings.header_logo
                            : process.env.NEXT_APP_EVENTCENTER_URL +
                            "/_eventsite_assets/images/eventbuizz_logo-1.png"
                }
            />
                        <meta
                            property="twitter:image"
                            content={
                                  news.image
                                  ? process.env.NEXT_APP_EVENTCENTER_URL +
                                  "/assets/eventsite_news/" +
                                  news.image
                                  : event.settings.header_logo &&
                                      event.settings.header_logo !== ""
                                      ? process.env.NEXT_APP_EVENTCENTER_URL +
                                      "/assets/event/branding/" +
                                      event.settings.header_logo
                                      : process.env.NEXT_APP_EVENTCENTER_URL +
                                      "/_eventsite_assets/images/eventbuizz_logo-1.png"
                            }
                        />
                        <meta property="twitter:card" content="summary_large_image" />
                        <meta httpEquiv="X-UA-Compatible" content="IE=edge" />
                        <meta name="msapplication-config" content="none" />
                        <link
                            rel="icon"
                            type="image/x-icon"
                            href={
                                event.settings.fav_icon && event.settings.fav_icon !== ""
                                    ? process.env.NEXT_APP_EVENTCENTER_URL +
                                    "/assets/event/branding/" +
                                    event.settings.fav_icon
                                    : require("public/img/square.jpg")
                            }
                        />
                        
          </Head> */}
          <Component  news={news} event={event} sidebar={sidebar} newsSettings={event.news_settings} />
        </React.Fragment>
      ) : <PageLoader/>}
    </Suspense>
  );
};

export default NewsDetail;
