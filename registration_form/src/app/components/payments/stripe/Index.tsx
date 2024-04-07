import React, { ReactElement, FC, useState, useEffect, useContext, useRef, useMemo } from "react";
import { useParams, useHistory, Link } from 'react-router-dom';
import Event from '@/src/app/components/event/interface/Event';
import { EventContext } from "@/src/app/context/event/EventProvider";
import Loader from '@/src/app/components/forms/Loader';
import { loadStripe } from '@stripe/stripe-js';
import { useStripe, useElements, CardElement, Elements } from "@stripe/react-stripe-js";
import '@/src/app/components/payments/stripe/style.css';
import useResponsiveFontSize from '@/src/app/components/payments/stripe/useResponsiveFontSize';
import { service } from '@/src/app/services/service';
import in_array from "in_array";

const useOptions = () => {
  const fontSize = useResponsiveFontSize();
  const options = useMemo(
    () => ({
      style: {
        base: {
          fontSize,
          color: "#424770",
          letterSpacing: "0.025em",
          fontFamily: "Source Code Pro, monospace",
          "::placeholder": {
            color: "#aab7c4"
          }
        },
        invalid: {
          color: "#9e2146"
        }
      }
    }),
    [fontSize]
  );

  return options;
};

const CardForm: FC<any> = (props: any): ReactElement => {

  const [clientSecret, setClientSecret] = useState("");

  const [error, setError] = useState('');

  const [succeeded, setSucceeded] = useState(false);

  const [processing, setProcessing] = useState(false);

  const stripe: any = useStripe();

  const elements = useElements();

  const options = useOptions();

  const mounted = useRef(false);

  const history = useHistory();

  useEffect(() => {
    mounted.current = true;
    return () => { mounted.current = false; };
  }, []);

  useEffect(() => {
    if (props?.order?.order_id) {
      service.post(`${process.env.REACT_APP_API_URL}/registration/event/${props?.event.url}/registration/create-stripe-payment-intent/${props?.order?.order_id}`, { amount: props?.order?.order_detail?.order?.grand_total, currency: props?.order?.currency, provider: props?.provider })
        .then(
          response => {
            if (response.success && mounted.current) {
              setClientSecret(response?.data?.client_secret);
            } else {
              setError(response?.message)
            }
          },
          error => {

          }
        );
    }
  }, []);

  const handleSubmit = async (ev: any) => {
    ev.preventDefault();
    setProcessing(true);

    // Step 3: Use clientSecret from PaymentIntent and the CardElement
    // to confirm payment with stripe.confirmCardPayment()
    const payload = await stripe?.confirmCardPayment(clientSecret, {
      payment_method: {
        card: elements?.getElement(CardElement),
        billing_details: {
          name: ev.target.name.value
        }
      }
    });

    if (payload.error) {
      setError(`Payment failed: ${payload?.error?.message}`);
      setProcessing(false);
      console.log("[error]", payload.error);
    } else {
      setError('');
      setSucceeded(true);
      setProcessing(false);
      console.log("[PaymentIntent]", payload.paymentIntent);
      history.push(`/${props?.event?.url}/${props?.provider}/registration-success/${props?.order?.order_id}`);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <div>
        <h1>
          {props?.order?.order_detail?.grand_total_display}
        </h1>
        <CardElement
          options={options}
          onReady={() => {
            console.log("CardElement [ready]");
          }}
          onChange={event => {
            console.log("CardElement [change]", event);
          }}
          onBlur={() => {
            console.log("CardElement [blur]");
          }}
          onFocus={() => {
            console.log("CardElement [focus]");
          }}
        />
      </div>
      {error && <p className="error-message">{error}</p>}
      <button type="submit" disabled={!stripe}>
        {processing ? "Processing…" : "Pay"}
      </button>
    </form>
  );
};

type Params = {
  url: any;
  provider: any;
  order_id: any;
};

const Stripe: FC<any> = (): ReactElement => {

  const { event, updateOrder } = useContext<any>(EventContext);

  const { order_id, provider } = useParams<Params>();

  const [loading, setLoading] = useState(true);

  const [action, setAction] = useState("");

  const history = useHistory();

  const mounted = useRef(false);

  const [order, setOrder] = useState<any>({});

  const stripePromise = loadStripe(event?.payment_setting?.stripe_api_key);

  useEffect(() => {
    loadSummary(event, order_id);
  }, []);

  function loadSummary(event: any, order_id: any) {
    service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/online-payment/${order_id}/stripe?provider=${provider}`)
      .then(
        response => {
          if (response.success && mounted.current) {
            if (response?.data?.order?.status !== "completed" || (response?.data?.order?.status === "completed" && (Number(response?.data?.order?.is_waitinglist) === 1 || in_array(provider, ['sale', 'admin'])))) {
              setOrder(response.data.order);
              updateOrder(response?.data?.order);
              setLoading(false);
            } else {
              history.push(`/${event.url}/${provider}`);
            }
          }
        },
        error => {
          setLoading(false);
        }
      );
  }

  useEffect(() => {
    mounted.current = true;
    return () => { mounted.current = false; };
  }, []);

  return (
    <React.Fragment>
      <div className="row d-flex ebs-title-box align-items-center">
        <div className="col-6"><h2 className="section-title">{event?.name}</h2></div>
      </div>
      <div className="wrapper-box hotel-booking-section stripe">
        {loading && <Loader className='fixed' />}
        <header className="header-section">
          {order?.order_id && (
            <Elements stripe={stripePromise}>
              <CardForm order={order} event={event} provider={provider} />
            </Elements>
          )}
        </header>

      </div>
    </React.Fragment>
  );
};

export default Stripe;