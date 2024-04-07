import React, { ReactElement, FC, useContext, useState } from 'react';
import { eventSelector, updateCookie } from "store/Slices/EventSlice";
import { useSelector, useDispatch } from "react-redux";
import MasterLayoutRoute from "components/layout/MasterLayoutRoute";
import MetaInfo from "components/layout/MetaInfo";
import PageLoader from "components/ui-components/PageLoader";
import { metaInfo } from 'helpers/helper';
import { getCookie, setCookie } from 'cookies-next';
import LoginScreen from 'components/myAccount/login/LoginScreen';
import { useRouter } from "next/router";

const CookiePolicy = (props)=> {
    const router = useRouter();

    const { event, cookie } = useSelector(eventSelector);
    const [userExist, setUserExist] = useState(typeof window !== 'undefined' && localStorage.getItem(`event${props.metaInfo.id}User`) ? true : false);

    if(userExist){
        router.push(`/${props.metaInfo.url}`);
    }

    return (
        <>
            <>
            <MetaInfo metaInfo={props.metaInfo} cookie={props.cookie} />
            {event ? (
                <MasterLayoutRoute event={event}>
                    <div style={{ minHeight:'100vh'}}>
                    </div>
                     <LoginScreen />
                </MasterLayoutRoute>
            ) : (
                <PageLoader />
            )}
        </>
        </>

        
    );
};


export async function getServerSideProps(context) {
    const {req, res} = context;
    const eventData = await metaInfo(`${process.env.NEXT_APP_URL}/event/${context.query.event}/meta-info`, '');
    const serverCookie = getCookie(`cookie__${context.query.event}`, { req, res });
    if(serverCookie === null || serverCookie === undefined){
        setCookie(`cookie__${context.query.event}`, 'necessary', { req, res, maxAge: 30*24*60*60, domain: '.eventbuizz.com' })
    }
    return {
        props: {
            metaInfo: eventData,
            cookie : (serverCookie !== null && serverCookie !== undefined) ? serverCookie : 'necessary',
            url: context.resolvedUrl 
        },
    }
}

export default CookiePolicy;