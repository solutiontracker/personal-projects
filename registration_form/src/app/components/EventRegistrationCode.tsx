import React, { ReactElement, FC, useEffect, useState, useRef, useContext, useMemo } from "react";
import { service } from '@/src/app/services/service';
import Event from '@/src/app/components/event/interface/Event';
import Loader from '@/src/app/components/forms/Loader';
import { useHistory } from 'react-router-dom';
import { EventContext } from "@/src//app/context/event/EventProvider";

const EventRegistrationCode: FC<any> = (props: any): ReactElement => {

    const { section } = props;

    const { event, updateValidateCode } = useContext<any>(EventContext);

    const [loading, setLoading] = useState(section === "manage-keywords" ? true : false);

    const [email, setEmail] = useState('');

    const [code, setCode] = useState('');

    const [errors, setErrors] = useState<any>({});

    const history = useHistory();

    const mounted = useRef(false);

    useEffect(() => {
        mounted.current = true;
        return () => {
            mounted.current = false;
        };
    }, []);

    const validate = () => {
        setLoading(true);
        service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/validate-event-registration-code`, { code: code, email: email })
            .then(
                response => {
                    if (mounted.current) {
                        if (response.success) {
                            updateValidateCode(event.id);
                            history.push(`/${event.url}/attendee`);
                        } else {
                            setErrors(response.errors);
                        }
                        setLoading(false);
                    }
                },
                error => {
                    setLoading(false);
                }
            );
    }

    const getKeyValue = function <T, U extends keyof T>(obj: T, key: U) { return obj[key] !== undefined ? obj[key] : '' }

    return (
        <React.Fragment>
            <div className="ebs-corporate-login">
                <div className="ebs-corporate-fields">
                    <div className="ebs-event-logo">
                        {event?.settings.header_logo ? (
                            <img src={`${process.env.REACT_APP_EVENTCENTER_URL}/assets/event/branding/${event.settings.header_logo}`} alt="" />
                        ) : (
                            <img src={`${process.env.REACT_APP_EVENTCENTER_URL}/_mobile_assets/images/logo-header@2x.png`} alt="" />
                        )}
                    </div>
                    <div className="ebs-event-description">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore
                    </div>
                    <div className="ebs-input-field">
                        <input placeholder=" " className={`${email && 'ebs-input-verified'}`} type="text" onChange={(e: any) => {
                            setEmail(e.target.value);
                        }} />
                        <label className="title">{event?.labels?.GENERAL_EMAIL} <em>*</em></label>
                    </div>
                    {getKeyValue(errors, 'email') && <p className="error-message">{getKeyValue(errors, 'email')}</p>}
                    <div className="ebs-input-field">
                        <input placeholder=" " className={`${code && 'ebs-input-verified'}`} type="text" onChange={(e: any) => {
                            setCode(e.target.value);
                        }} />
                        <label className="title">{event?.labels?.EVENTSITE_REGISTRATION_CODE}<em>*</em></label>
                    </div>
                    {getKeyValue(errors, 'code') && <p className="error-message">{getKeyValue(errors, 'code')}</p>}
                    <button className={`btn btn-default ${(!email || !code) ? 'disabled' : ''}`} onClick={() => {
                        if (email && code) {
                            validate();
                        }
                    }}>
                        {loading ? (
                            <>
                                Loading...
                                <i className="material-icons ebs-spinner">autorenew</i>
                            </>
                        ) : (
                            <>
                                {event?.labels?.EVENTSITE_REGISTRATION_SUBMIT}
                                <i className="material-icons">keyboard_arrow_right</i>
                            </>
                        )}
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
};

export default EventRegistrationCode;