import Head from 'next/head'
import React, { useEffect, useState } from "react";
import { useSelector } from "react-redux";
import { eventSelector } from "store/Slices/EventSlice";
import MasterLayoutRoute from "components/layout/MasterLayoutRoute";
import { metaInfo } from 'helpers/helper';
import MetaInfo from "components/layout/MetaInfo";
import PageLoader from "components/ui-components/PageLoader";
import { getCookie, setCookie } from 'cookies-next';
import { useRouter } from 'next/router';
import axios from "axios";

const Index = (props) => {

    const { event } = useSelector(eventSelector);

    const router = useRouter();

    const { id, event_id, email } = router.query;

    const [error, setError] = useState('');

    const onConfirm = async () => {
        const response = await axios.post(
            `${process.env.NEXT_APP_URL}/event/${event?.url}/unsubscribe-attendee`,
            { id, event_id, email }
        );
        if (response.data.success) {
            if (event?.eventsiteSettings?.third_party_redirect_url && Number(event?.eventsiteSettings?.third_party_redirect) === 1) {
                window.location = event?.eventsiteSettings?.third_party_redirect_url;
            }
            else {
                router.push(
                    `/${event.url}`
                );
            }
        }
    }

    useEffect(async () => {
        const response = await axios.get(
            `${process.env.NEXT_APP_URL}/event/${event?.url}/unsubscribe-attendee?id=${id}&event_id=${event_id}&email=${email}`
        );
        if (response.data.success) {
            router.push(`/${event.url}`);
        } else {
            setError(response.data.message)
        }
    }, []);

    const onCancel = () => {
        if (event?.eventsiteSettings?.third_party_redirect_url && Number(event?.eventsiteSettings?.third_party_redirect) === 1) {
            window.location = event?.eventsiteSettings?.third_party_redirect_url;
        }
        else {
            router.push(
                `/${event.url}`
            );
        }
    }

    return (
        <>
            <MetaInfo metaInfo={props.metaInfo} cookie={props.cookie} />
            {event ? (
                <MasterLayoutRoute event={event}>
                    <div style={{ height: "90vh" }}>
                        <div className="not-attending-popup">
                            <div className="ebs-not-attending-fields">
                                <div className="ebs-not-attending-heading">
                                    {error ? (
                                        <>{event.labels.REGISTRATION_SITE_HEADER_ALERT !== undefined ? event.labels.REGISTRATION_SITE_HEADER_ALERT : "Error"}</>
                                    ) : (
                                        <>{event.labels.EVENTSITE_ATTENDEE_CANCELLATION_CONFIRMATION !== undefined ? event.labels.EVENTSITE_ATTENDEE_CANCELLATION_CONFIRMATION : "Confirmation"}</>
                                    )}
                                </div>
                                <div className="ebs-event-description">
                                    {error ? error : (
                                        <>
                                            {event.labels.EVENTSITE_ATTENDEE_NOT_ATTENDING_CONFIRMATION_MSG !== undefined ? event.labels.EVENTSITE_ATTENDEE_NOT_ATTENDING_CONFIRMATION_MSG : "Are you sure you want cancel coming to the event."}
                                        </>
                                    )}
                                </div>
                                {!error && (
                                    <div className='btn-container' >
                                        <button className="btn btn-default" onClick={(e) => { onConfirm(); }}>
                                            {event.labels.EVENTSITE_NOT_ATTENDING_CONFIRM_BTN !== undefined ? event.labels.EVENTSITE_NOT_ATTENDING_CONFIRM_BTN : "Confirm"}
                                        </button>
                                        <button className="btn btn-default" onClick={(e) => { onCancel(); }}>
                                            {event.labels.GENERAL_CANCEL !== undefined ? event.labels.GENERAL_CANCEL : "Cancel"}
                                        </button>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </MasterLayoutRoute>
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
        setCookie(`cookie__${context.query.event}`, 'necessary', { req, res, maxAge: 30 * 24 * 60 * 60, domain: '.eventbuizz.com' })
    }

    return {
        props: {
            metaInfo: eventData,
            cookie: (serverCookie !== null && serverCookie !== undefined) ? serverCookie : 'necessary',
            url: context.resolvedUrl
        },
    }
}

export default Index