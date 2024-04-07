import React, { ReactElement, FC, useContext, useRef, useEffect, useState } from "react";
import { useParams } from 'react-router-dom';
import Event from '@/src/app/components/event/interface/Event';
import { EventContext } from "@/src//app/context/event/EventProvider";
import { service } from '@/src/app/services/service';
import Loader from '@/src/app/components/forms/Loader';

type Params = {
    url: any;
    order_id: any;
    provider: any;
};

const CancelWaitingListOrder: FC<any> = (): ReactElement => {

    const { event } = useContext<any>(EventContext);

    const mounted = useRef(false);

    const [loading, setLoading] = useState(true);

    const [message, setMessage] = useState(true);

    const { order_id, provider } = useParams<Params>();

    useEffect(() => {
        mounted.current = true;
        return () => { mounted.current = false; };
    }, []);

    useEffect(() => {
        cancelOrder(event, order_id);
    }, []);

    function cancelOrder(event: any, order_id: any) {
        service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/cancel-waitinglist-order/${order_id}`, { provider: provider })
            .then(
                response => {
                    if (response.success && mounted.current) {
                        setLoading(false);
                        setMessage(response.message)
                    }
                },
                error => {
                    setLoading(false);
                }
            );
    }

    return (
        <React.Fragment>
            {loading ? (
                <Loader className='fixed' />
            ) : (
                <div className="wrapper-box">
                    <div className="registration-success">
                        <div className="header-area">
                            <img src={require('@/src/img/ico-warning.svg')} alt="" />
                            <h3>{'Cancel order'}</h3>
                            <p>{message}</p>
                        </div>
                    </div>
                </div>
            )}
        </React.Fragment>
    );
};

export default CancelWaitingListOrder;