import React, { ReactElement, FC, useContext, useState } from "react";
import { EventContext } from "@/src//app/context/event/EventProvider";
import { useLocation } from "react-router-dom";


const WaitingListAlert: FC<any> = (): ReactElement => {

    const { event, order } = useContext<any>(EventContext);

    const search = useLocation().search;

    const [security_key, setSecurityKey] = useState(new URLSearchParams(search).get("security_key"));

    if ((order?.is_waitinglist === undefined || Number(order?.is_waitinglist) === 0 || (security_key !== null && security_key !== undefined)) || window.location.pathname.includes('waiting-link-expired') || window.location.pathname.includes('no-order-found') || window.location.pathname.includes('registration-success') || window.location.pathname.includes('cancel-waitinglist-order'))
        return <></>
    else
        return (
            <React.Fragment>
                <div className="container ebs-alert-warning">
                    <div className="alert">
                        <i className="material-icons">info</i>{event?.labels?.REGISTERING_FOR_WAITING_LIST || 'You are registering for waiting list'}
                    </div>
                </div>
            </React.Fragment>
        );
};

export default WaitingListAlert;