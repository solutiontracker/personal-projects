import React, { createContext, useState, FC, ReactElement, useEffect } from "react";
import EventContextState from '@/src/app/context/event/EventContextState';
import Event from '@/src/app/components/event/interface/Event';
import { service } from '@/src/app/services/service';
import { ltrim } from '@/src/app/helpers';
import ReactGA from 'react-ga';
import { useLocation } from 'react-router-dom';
import Error404 from "../../components/Error404";
import Cookies from "js-cookie";

const contextDefaultValues: EventContextState = {
    event: {},
    updateEvent: () => { },

    order: {},
    updateOrder: () => { },

    validate_code: localStorage.getItem('validate-code') !== null && localStorage.getItem('validate-code') ? localStorage.getItem('validate-code') : null,
    updateValidateCode: () => { },

    waitinglist: localStorage.getItem('waitinglist') !== null && localStorage.getItem('waitinglist') ? localStorage.getItem('waitinglist') : null,
    updateWaitinglist: () => { },

    currentFormPaymentSettings: localStorage.getItem('currentFormPaymentSettings') !== null && localStorage.getItem('currentFormPaymentSettings') ? localStorage.getItem('currentFormPaymentSettings') : null,
    updatecurrentFormPaymentSettings: () => { },

    cookie: '',
    updateCookie: () => { },

    routeParams: {},
    updateRouteParams: () => { },

    formBuilderForms: [],
    updateFormBuilderForms: () => { },
};

export const EventContext = createContext<EventContextState>(
    contextDefaultValues
);

interface MyProps {
    props?: React.ReactNode;
}

const EventProvider: FC<MyProps> = (props): ReactElement => {

    const [processed, setProcessed] = useState<boolean>(false);

    const [event, setEvent] = useState<Event>(contextDefaultValues.event);

    const updateEvent = (event: Event) => setEvent(event);

    const [order, setOrder] = useState<any>(contextDefaultValues.order);

    const updateOrder = (order: any) => setOrder(order);

    const [validate_code, setValidateCode] = useState<any>(contextDefaultValues.validate_code);

    const updateValidateCode = (value: any) => {
        setValidateCode(value);
        localStorage.setItem('validate-code', value);
    };

    const [waitinglist, setWaitinglist] = useState<any>(contextDefaultValues.waitinglist);

    const updateWaitinglist = (value: any) => {
        setWaitinglist(value);
        localStorage.setItem('waitinglist', value);
    };

    const [cookie, setCookieState] = useState<any>(contextDefaultValues.cookie);

    const updateCookie = (value: any) => {
        if (value) {
            setCookieState(value);
            setCookie(`cookie__${event.url}`, value);
        } else {
            setCookieState(value);
            removeCookie(`cookie__${event.url}`);
        }
    };

    const [currentFormPaymentSettings, setcurrentFormPaymentSettings] = useState<any>(contextDefaultValues.currentFormPaymentSettings);

    const updatecurrentFormPaymentSettings = (value: any) => {
        setcurrentFormPaymentSettings(value);
        localStorage.setItem('currentFormPaymentSettings', value);
    };

    const path = ltrim(window.location.pathname, "/");

    const params = path.split("/");

    const location = useLocation();

    const attendee_types = new URLSearchParams(location.search).get("attendee_types");

    useEffect(() => {
        if (params.length > 0) {
            service.get(attendee_types ? `${process.env.REACT_APP_API_URL}/registration/event/${params[0]}/fetch-event?attendee_types=${attendee_types}` : `${process.env.REACT_APP_API_URL}/registration/event/${params[0]}/fetch-event`)
                .then(
                    response => {
                        if (response.success) {

                            setEvent(response.data.event);

                            addAnalytics(response.data.event);

                            // Cookies 
                            try {
                                const cookie = getCookie(`cookie__${response.data.event.url}`) as any;
                                if (cookie !== undefined && cookie !== null && cookie) {
                                    setCookieState(cookie);
                                }
                            } catch (error: any) { }

                        }

                        setProcessed(true);
                    },
                    error => { }
                );
        }

    }, []);

    useEffect(() => {
        if (event) {
            ReactGA.pageview(window.location.pathname + window.location.search);
        }
    }, [location]);

    const [routeParams, setRouteParams] = useState<any>(contextDefaultValues.routeParams);

    const updateRouteParams = (value: any) => {
        setRouteParams(value);
    };

    const [formBuilderForms, setFormBuilderForms] = useState<any>(contextDefaultValues.formBuilderForms);

    const updateFormBuilderForms = (data: any) => {
        const forms = data.forms;
        let formAnswered: any = {};
        if (localStorage.getItem(`FBXXAD${data.order_id}_${data.attendee_id}`) !== null && localStorage.getItem(`FBXXAD${data.order_id}_${data.attendee_id}`) !== undefined && localStorage.getItem(`FBXXAD${data.order_id}_${data.attendee_id}`)) {
            formAnswered = JSON.parse(localStorage.getItem(`FBXXAD${data.order_id}_${data.attendee_id}`) as any);
        }
        setFormBuilderForms(forms.map((form: any) => {
            return { ...form, answered: formAnswered[form.id] !== undefined ? true : false }
        }));
    };

    const addAnalytics = (event: any) => {
        if (process.env.REACT_APP_ENVIRONMENT === "live" || true) {
            if (event.settings.google_analytics) {
                ReactGA.initialize(event.settings.google_analytics);
                ReactGA.pageview(window.location.pathname + window.location.search);
            }
        }
    }

    // Method to set data in cookies which will expire in 7 days
    const setCookie = (name: any, value: any) => {
        Cookies.set(name, value, {
            expires: 7,
            domain: '.eventbuizz.com'
        });
    };

    // Method to get data from cookies
    const getCookie = (value: any) => {
        return Cookies.get(value);
    };

    // Method to remove data from cookies
    const removeCookie = (value: any) => {
        return Cookies.remove(value);
    };

    return (
        event && (
            <EventContext.Provider
                value={{
                    event,
                    updateEvent,

                    order,
                    updateOrder,

                    validate_code,
                    updateValidateCode,

                    waitinglist,
                    updateWaitinglist,

                    cookie,
                    updateCookie,

                    currentFormPaymentSettings,
                    updatecurrentFormPaymentSettings,

                    routeParams,
                    updateRouteParams,

                    formBuilderForms,
                    updateFormBuilderForms,
                }}
            >
                {event?.id ? props.children : (processed && <Error404 />)}
            </EventContext.Provider>
        )
    );
};

export default EventProvider;