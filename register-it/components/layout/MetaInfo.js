import React, { useEffect } from 'react';
import Head from 'next/head';
import Script from 'next/script';
import { useRouter } from 'next/router';

const pageview = (GA_MEASUREMENT_ID, url) => {
    if (window !== undefined) {
        window.gtag("config", GA_MEASUREMENT_ID, {
            page_path: url,
        });
    }

};


const MetaInfo = (props) => {
    const router = useRouter();
    useEffect(() => {
        const handleRouteChange = (url) => {
            if (props.metaInfo.settings.google_analytics) {
                pageview(props.metaInfo.settings.google_analytics, url);
            }
        };
        router.events.on("routeChangeComplete", handleRouteChange);

        return () => {
            router.events.off("routeChangeComplete", handleRouteChange);
        };
    }, [router.events]);

    return (
        <>
            <Head>
                {!props.metaInfo !== undefined && (

                    <>
                        <title>{props.metaInfo.name}</title>
                        <meta property="og:title" content={props.metaInfo.name} />
                        <meta property="og:type" content="Event" />
                        {props.metaInfo.eventsiteSettings && props.metaInfo.eventsiteSettings.search_engine_visibility == 0 &&
                            <meta name="robots" content="noindex"></meta>
                        }
                        <meta
                            property="og:url"
                            content={`${process.env.NEXT_APP_BASE_URL}/${props.metaInfo.url}`}
                        />
                        <meta
                            property="og:image"
                            content={
                                props.metaInfo.settings.social_media_logo &&
                                    props.metaInfo.settings.social_media_logo !== ""
                                    ? process.env.NEXT_APP_EVENTCENTER_URL +
                                    "/assets/event/social_media/" +
                                    props.metaInfo.settings.social_media_logo
                                    : props.metaInfo.settings.header_logo &&
                                        props.metaInfo.settings.header_logo !== ""
                                        ? process.env.NEXT_APP_EVENTCENTER_URL +
                                        "/assets/event/branding/" +
                                        props.metaInfo.settings.header_logo
                                        : process.env.NEXT_APP_EVENTCENTER_URL +
                                        "/_eventsite_assets/images/eventbuizz_logo-1.png"
                            }
                        />
                        <meta
                            property="twitter:image"
                            content={
                                props.metaInfo.settings.social_media_logo &&
                                    props.metaInfo.settings.social_media_logo !== ""
                                    ? process.env.NEXT_APP_EVENTCENTER_URL +
                                    "/assets/event/social_media/" +
                                    props.metaInfo.settings.social_media_logo
                                    : props.metaInfo.settings.header_logo &&
                                        props.metaInfo.settings.header_logo !== ""
                                        ? process.env.NEXT_APP_EVENTCENTER_URL +
                                        "/assets/event/branding/" +
                                        props.metaInfo.settings.header_logo
                                        : process.env.NEXT_APP_EVENTCENTER_URL +
                                        "/_eventsite_assets/images/eventbuizz_logo-1.png"
                            }
                        />
                        <meta property="twitter:card" content="summary_large_image" />
                        <meta httpEquiv="X-UA-Compatible" content="IE=edge" />
                        <meta name="msapplication-config" content="none" />
                        <meta
                            property="og:description"
                            content={
                                props.metaInfo.description && props.metaInfo.description.info
                                    ? props.metaInfo.description.info.description
                                    : props.metaInfo.name
                            }
                        />
                        {(props.metaInfo.settings.fav_icon && props.metaInfo.settings.fav_icon !== "") && <link
                            rel="icon"
                            type="image/x-icon"
                            href={`${process.env.NEXT_APP_EVENTCENTER_URL}/assets/event/branding/${props.metaInfo.settings.fav_icon}`}
                        />}
                    </>
                )}

                {props.metaInfo.settings.google_analytics && props.cookie !== null && props.cookie == "all" && (
                    <>
                        <script id='google-analytics-1' src={`https://www.googletagmanager.com/gtag/js?id=${props.metaInfo.settings.google_analytics}`} />
                        <script id='google-analytics-2' dangerouslySetInnerHTML={{
                            __html: `
                        window.dataLayer = window.dataLayer || [];
                        function gtag(){dataLayer.push(arguments);}
                        gtag('js', new Date());
                        gtag('config', '${props.metaInfo.settings.google_analytics}', {
                        page_path: window.location.pathname,
                        });
                    `}} />
                    </>

                )}

                {props.metaInfo.settings?.google_analytics_id !== undefined && props.metaInfo.settings?.google_analytics_id && props.cookie !== null && props.cookie == "all" && (
                    <>
                        <script id='thirdyparty-google-analytics-1' src={`https://www.googletagmanager.com/gtag/js?id=${props.metaInfo.settings?.google_analytics_id}`} />
                        <script id='thirdyparty-google-analytics-2' dangerouslySetInnerHTML={{
                            __html: `
                                    window.dataLayer = window.dataLayer || [];
                                    function gtag(){dataLayer.push(arguments);}
                                    gtag('js', new Date());
                                    gtag('config', '${props.metaInfo.settings?.google_analytics_id}', {
                                    page_path: window.location.pathname,
                                    });
                                `}} />
                    </>
                )}

                {props.metaInfo.settings?.linkedin_partner_id !== undefined && props.metaInfo.settings?.linkedin_partner_id && props.cookie !== null && props.cookie == "all" && (
                    <React.Fragment>
                        <script id='linkedin-analytics' dangerouslySetInnerHTML={{
                            __html: `_linkedin_partner_id = '${props.metaInfo.settings?.linkedin_partner_id}';
                                            window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
                                            window._linkedin_data_partner_ids.push(_linkedin_partner_id);
                                            (function(l) {
                                                if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
                                                window.lintrk.q=[]}
                                                var s = document.getElementsByTagName("script")[0];
                                                var b = document.createElement("script");
                                                b.type = "text/javascript";b.async = true;
                                                b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
                                                s.parentNode.insertBefore(b, s);
                                            })(window.lintrk);`
                                        }}
                        />
                        <noscript dangerouslySetInnerHTML={{
                            __html: `<img height="1" width="1" style="display:none" src="https://px.ads.linkedin.com/collect/?pid=${props.metaInfo.settings?.linkedin_partner_id}&fmt=gif" />`
                        }}
                        />
                    </React.Fragment>
                )}

                {props.metaInfo.settings?.facebook_pixel_id !== undefined && props.metaInfo.settings?.facebook_pixel_id && props.cookie !== null && props.cookie == "all" && (
                    <React.Fragment>
                        <script id='facebook-analytics' dangerouslySetInnerHTML={{
                            __html: `!function(f,b,e,v,n,t,s)
                                    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                                    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                                    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                                    n.queue=[];t=b.createElement(e);t.async=!0;
                                    t.src=v;s=b.getElementsByTagName(e)[0];
                                    s.parentNode.insertBefore(t,s)}(window, document,'script',
                                    'https://connect.facebook.net/en_US/fbevents.js');
                                    fbq('init', '${props.metaInfo.settings?.facebook_pixel_id}');
                                    fbq('track', '${(props.metaInfo.settings?.analytics_page_view_event_name !== undefined && props.metaInfo.settings?.analytics_page_view_event_name ? props.metaInfo.settings?.analytics_page_view_event_name : 'PageView')}');`
                                }}
                        />
                        <noscript dangerouslySetInnerHTML={{
                            __html: `<img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=${props.metaInfo.settings?.facebook_pixel_id}&ev=${(props.metaInfo.settings?.analytics_page_view_event_name !== undefined && props.metaInfo.settings?.analytics_page_view_event_name ? props.metaInfo.settings?.analytics_page_view_event_name : 'PageView')}&noscript=1" />`
                        }}
                        />
                    </React.Fragment>
                )}

                {props.metaInfo.settings?.matomo_domain_id !== undefined && props.metaInfo.settings?.matomo_domain_id && props.cookie !== null && props.cookie == "all" && (
                    <React.Fragment>
                        <script id='matomo-analytics' dangerouslySetInnerHTML={{
                            __html: `var _mtm = window._mtm = window._mtm || [];
                            _mtm.push({ 'mtm.startTime': (new Date().getTime()), 'event': 'mtm.Start' });
                            (function () {
                                var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
                                g.async = true; g.src = 'https://cdn.matomo.cloud/${props.metaInfo.settings?.matomo_domain_id}/container_ynlL2iw5.js'; s.parentNode.insertBefore(g, s);
                            })();`
                                }}
                        />
                    </React.Fragment>
                )}
            </Head>
        </>
    )
}

export default MetaInfo