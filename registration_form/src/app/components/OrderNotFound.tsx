import React, { ReactElement, FC, useContext } from "react";
import Event from '@/src/app/components/event/interface/Event';
import { EventContext } from "@/src//app/context/event/EventProvider";

const OrderNotFound: FC<any> = (): ReactElement => {

    const { event } = useContext<any>(EventContext);

    return (
        <React.Fragment>
            <div className="wrapper-box">
                <div className="registration-success">
                    <div className="header-area">
                        <img src={require('@/src/img/ico-warning.svg')} alt="" />
                        <h3>{'Not found'}</h3>
                        <p>{event?.labels?.WAITING_LIST_ORDER_NOT_FOUND}</p>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
};

export default OrderNotFound;