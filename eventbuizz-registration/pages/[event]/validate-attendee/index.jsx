import Head from 'next/head'
import React, { useEffect, useState} from "react";
import { useSelector } from "react-redux";
import { eventSelector } from "store/Slices/EventSlice";
import MasterLayoutRoute from "components/layout/MasterLayoutRoute";
import { metaInfo } from 'helpers/helper';
import MetaInfo from "components/layout/MetaInfo";
import PageLoader from "components/ui-components/PageLoader";
import { getCookie, setCookie } from 'cookies-next';
import { useRouter } from 'next/router';
import axios from "axios";
import {
     setShowLogin,
  } from "store/Slices/GlobalSlice";
import { useDispatch } from 'react-redux';

const Index = (props) => {

    const { event, verification_id, validateAttendee } = useSelector(eventSelector);
    
    const router = useRouter();

    const [loading, setLoading] = useState(true);

    const [message, setMessage] = useState();

    const dispatch = useDispatch()

    const onConfirm = async () => {
        if(verification_id !== null && validateAttendee !== null){
            const response = await axios.post(`${process.env.NEXT_APP_URL}/event/${event.url}/validate-attendee/${verification_id}/${validateAttendee}`);
            console.log(response);
            setMessage(response.data.message);
            setLoading(false);
        }else{
            router.push(`/${event.url}`);
            // setLoading(false);
        }
    }

    const onLoginClick = (bool) => {
        dispatch(setShowLogin(bool));
    }

    useEffect(() => {
        if(event){
            onConfirm();
        }
    
    }, [event])
    

  return (
        <>
            <MetaInfo metaInfo={props.metaInfo} cookie={props.cookie} />
            {event && !loading? (
                <MasterLayoutRoute event={event}>
                    <div style={{height:"90vh"}}>
                        <div className="not-attending-popup">
                            <div className="ebs-not-attending-fields">
                                <div className="ebs-not-attending-heading text-center">
                                    { message }
                                </div>
                                {(event.header_data.my_account_sub_menu.length > 0) && (event.header_data.my_account_sub_menu.findIndex((item)=>item.alias == 'login') > -1) && <div className='btn-container justify-content-center'>
                                    <button className="btn btn-default" onClick={()=>{onLoginClick(true)}}>
                                        {event.labels.EVENTSITE_LOGIN !== undefined ? event.labels.EVENTSITE_LOGIN : "Login" }
                                    </button>
                                </div>}
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

export default Index