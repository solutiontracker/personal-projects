import Head from 'next/head'
import React from "react";
import { useSelector } from "react-redux";
import { eventSelector } from "store/Slices/EventSlice";
import MasterLayoutRoute from "components/layout/MasterLayoutRoute";
import CmsDetail from 'components/modules/cms/CmsDetail';
import { metaInfo } from 'helpers/helper';
import MetaInfo from "components/layout/MetaInfo";
import PageLoader from "components/ui-components/PageLoader";
import { getCookie, setCookie } from 'cookies-next';


const ExhibitorDetail = (props) => {

    const { event } = useSelector(eventSelector);

    return (
        <>
            <Head>
            <title>{props.cmsPage.name && props.cmsPage.name}</title>
            <meta property="og:title" content={props.cmsPage.name && props.cmsPage.name} />
            <meta property="og:type" content="Event" />
            <meta
                property="og:image"
                content={
                  props.cmsPage.image && props.cmsPage.image !== "" 
                        ?  process.env.NEXT_APP_EVENTCENTER_URL +
                        `/assets/additional_info/` +
                        props.cmsPage.image
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
                              props.cmsPage.image && props.cmsPage.image !== "" 
                              ?  process.env.NEXT_APP_EVENTCENTER_URL +
                              `/assets/additional_info/` +
                              props.cmsPage.image
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
                        {(props.metaInfo.settings.fav_icon && props.metaInfo.settings.fav_icon !== "") && <link
                            rel="icon"
                            type="image/x-icon"
                            href={`${process.env.NEXT_APP_EVENTCENTER_URL}/assets/event/branding/${props.metaInfo.settings.fav_icon}`}
                        />}
                        
            </Head>
            {event ? (
                <MasterLayoutRoute event={event}>
                    <CmsDetail moduleName="additional_information" />
                </MasterLayoutRoute>
            ) : (
                <PageLoader />
            )}
        </>
    )
}

export async function getServerSideProps(context) {
    const {req, res} = context;
    const response = await fetch(`${process.env.NEXT_APP_URL}/event/${context.query.event}/additional_information/page/${context.query.id}`);
    const resData = await response.json();
    const eventData = await metaInfo(`${process.env.NEXT_APP_URL}/event/${context.query.event}/meta-info`, '');
    const serverCookie = getCookie(`cookie__${context.query.event}`, { req, res });
    if(serverCookie === null || serverCookie === undefined){
        setCookie(`cookie__${context.query.event}`, 'necessary', { req, res, maxAge: 30*24*60*60, domain: '.eventbuizz.com' })
    }
    return {
        props: {
            metaInfo: eventData,
            cookie : (serverCookie !== null && serverCookie !== undefined) ? serverCookie : 'necessary',
            cmsPage: resData.data,
            url: context.resolvedUrl
        },
    }
}

export default ExhibitorDetail