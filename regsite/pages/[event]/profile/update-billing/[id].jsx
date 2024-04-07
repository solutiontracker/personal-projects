import Head from 'next/head'
import React from "react";
import { useSelector } from "react-redux";
import { eventSelector } from "store/Slices/EventSlice";
import MasterLayoutMyAccount from "components/layout/MasterLayoutMyAccount";
import UpdateBillingPage from "components/myAccount/profile/UpdateBilling";
import { metaInfo } from 'helpers/helper';
import MetaInfo from "components/layout/MetaInfo";
import PageLoader from "components/ui-components/PageLoader";
import { getCookie, setCookie } from 'cookies-next';
import { useRouter } from 'next/router';

const UpdateBilling = (props) => {

    const { event } = useSelector(eventSelector);

    const router = useRouter();

    const { id } = router.query;

    return (
        <>
            <MetaInfo metaInfo={props.metaInfo} cookie={props.cookie} />

            {event ? (
                <MasterLayoutMyAccount>
                    <UpdateBillingPage id={id} />
                </MasterLayoutMyAccount>
            ) : (
                <PageLoader />
            )}
            
        </>
    )

}

export async function getServerSideProps(context) {

    const { req, res } = context;

    const eventData = await metaInfo(`${process.env.NEXT_APP_URL}/event/${context.query.event}/meta-info`, '');

    const serverCookie = getCookie(`cookie__${context.query.event}`, { req, res });

    if (serverCookie === null || serverCookie === undefined) {
        setCookie(`cookie__${context.query.event}`, 'necessary', { req, res, maxAge: 30 * 24 * 60 * 60 })
    }

    return {
        props: {
            metaInfo: eventData,
            cookie: (serverCookie !== null && serverCookie !== undefined) ? serverCookie : 'necessary',
            url: context.resolvedUrl
        },
    }
}

export default UpdateBilling