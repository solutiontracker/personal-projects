import Event from '@/src/app/components/event/interface/Event';

type EventContextState = {
    event: Event;
    updateEvent: (event: Event) => void;

    order: Event;
    updateOrder: (order: any) => void;

    validate_code: any;
    updateValidateCode: (value: any) => void;

    waitinglist: any;
    updateWaitinglist: (value: any) => void;
    
    cookie: any;
    updateCookie: (value: any) => void;
    
    currentFormPaymentSettings: any;
    updatecurrentFormPaymentSettings: (value: any) => void;

    routeParams: any;
    updateRouteParams: (payload: any) => void;
    
    formBuilderForms: any;
    updateFormBuilderForms: (payload: any) => void;
};

export default EventContextState;