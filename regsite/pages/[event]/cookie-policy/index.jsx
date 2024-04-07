import React, { ReactElement, FC, useContext } from 'react';
import { eventSelector, updateCookie } from "store/Slices/EventSlice";
import { useSelector, useDispatch } from "react-redux";
import MasterLayoutRoute from "components/layout/MasterLayoutRoute";
import MetaInfo from "components/layout/MetaInfo";
import PageLoader from "components/ui-components/PageLoader";
import { metaInfo } from 'helpers/helper';
import { getCookie, setCookie } from 'cookies-next';

const CookiePolicy = (props)=> {

    const { event, cookie } = useSelector(eventSelector);

    return (
        <>
            <MetaInfo metaInfo={props.metaInfo} cookie={props.cookie} />
            {event ? (
                <MasterLayoutRoute event={event}>
                <main role="main" className="main-section ebs-cookie-policy">
                <div className="container" >
                    <div className="eb-content">
                        <h1>{event?.interface_labels?.cookie.COOKIES_EVENTBUIZZ}</h1>
                        <p>{event?.interface_labels?.cookie.COOKIES_SMALL_PRA} <br />
                            {event?.interface_labels?.cookie.COOKIES_POLICY}<br />
                            {event?.interface_labels?.cookie.COOKIES_GENERAL}<br />
                        </p>
                        <h2>{event?.interface_labels?.cookie.COOKIES_OUR_COOKIES} </h2>
                        <ul>
                            <li>{event?.interface_labels?.cookie.COOKIES_LIST1}</li>
                            <li>{event?.interface_labels?.cookie.COOKIES_LIST2} ( https://developers.google.com/analytics/devguides/collection/analyticsjs/cookie-usage )</li>
                            <li>{event?.interface_labels?.cookie.COOKIES_LIST3}</li>
                            <li>{event?.interface_labels?.cookie.COOKIES_LIST4}</li>
                        </ul>
                    </div>
                    {cookie && (
                        <div className="eb-content">
                            <p>
                                <b>{event?.interface_labels?.cookie.COOKIES_CURRENT_STATE}</b>:
                                {cookie === "all" ? "Use all cookies." : "Use necessary cookies only."}
                            </p>
                        </div>
                    )}
                    <div className="eb-content">
                        <h1>{event?.interface_labels?.cookie.COOKIES_NECESSARY} </h1>
                        <p>{event?.interface_labels?.cookie.COOKIES_NECESSARY_COOKIES}<br /></p>
                    </div>
                    <table className="table" cellSpacing="0" width="100%">
                        <tbody>
                            <tr>
                                <th>Cookie</th>
                                <th>Domain</th>
                                <th>{event?.interface_labels?.cookie.COOKIES_TYPE}</th>
                                <th>{event?.interface_labels?.cookie.COOKIES_EXPIRATION}</th>
                                <th>{event?.interface_labels?.cookie.COOKIES_DESCRIPTION}</th>
                            </tr>
                            <tr>
                                <td>testcookie</td>
                                <td>.eventbuizz.com</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_INTERNAL}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_SESSION}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_TEMPORARY}</td>
                            </tr>
                            <tr>
                                <td>eventbuizz_session</td>
                                <td>.eventbuizz.com</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_INTERNAL}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_SESSION}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_TEMPORARY}</td>
                            </tr>
                            <tr>
                                <td>CookieConsent</td>
                                <td>.eventbuizz.com</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_INTERNAL}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_MONTH}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_ACCEPTANCE_CHOICE}</td>
                            </tr>
                            <tr>
                                <td>JSESSIONID</td>
                                <td>.nr-data.net</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_THIRD_PARTY}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_SESSION}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_MONITORING}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div className="eb-content">
                        <h1>{event?.interface_labels?.cookie.COOKIES_STATISTICS}</h1>
                        <p>{event?.interface_labels?.cookie.COOKIES_STATISTICS_COOKIES}<br /></p>
                    </div>
                    <table className="table" width="100%">
                        <tbody>
                            <tr>
                                <th>Cookie</th>
                                <th>Domain</th>
                                <th>{event?.interface_labels?.cookie.COOKIES_TYPE}</th>
                                <th>{event?.interface_labels?.cookie.COOKIES_EXPIRATION}</th>
                                <th>{event?.interface_labels?.cookie.COOKIES_DESCRIPTION}</th>
                            </tr>
                            <tr>
                                <td>__utma</td>
                                <td>.eventbuizz.com</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_THIRD_PARTY}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_SET_UPDATE}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_DISTINGUISH}</td>
                            </tr>
                            <tr>
                                <td>__utmb</td>
                                <td>.eventbuizz.com</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_THIRD_PARTY}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_30_mints}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_DISTINGUISH}</td>
                            </tr>
                            <tr>
                                <td>__utmc</td>
                                <td>.eventbuizz.com</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_THIRD_PARTY}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_BROWSER_SESSION}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_GAJS}</td>
                            </tr>
                            <tr>
                                <td>__utmt</td>
                                <td>.eventbuizz.com</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_THIRD_PARTY}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_10_mints}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_THROTTLE}</td>
                            </tr>
                            <tr>
                                <td>__utmz</td>
                                <td>.eventbuizz.com</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_THIRD_PARTY}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_6_MONTH}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_DISTINGUISH}</td>
                            </tr>
                            <tr>
                                <td>__cfduid</td>
                                <td>Several Hubspot domains</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_THIRD_PARTY}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_29_DAYS}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_CLOUDFLARE}</td>
                            </tr>
                            <tr>
                                <td>_ga</td>
                                <td>.eventbuizz.com</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_THIRD_PARTY}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_2_YEAR}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_USERS_DISTINGUISH}</td>
                            </tr>
                            <tr>
                                <td>_gid</td>
                                <td>.eventbuizz.com</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_THIRD_PARTY}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_24_hours}</td>
                                <td>{event?.interface_labels?.cookie.COOKIES_UNIQUE_ID}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </main>
                </MasterLayoutRoute>
            ) : (
                <PageLoader />
            )}
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