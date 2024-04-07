import React, { ReactElement, FC, useEffect, useState, useRef, useContext, useCallback } from "react";
import 'react-day-picker/lib/style.css';
import { service } from '@/src/app/services/service';
import Event from '@/src/app/components/event/interface/Event';
import Loader from '@/src/app/components/forms/Loader';
import { useHistory, useParams } from 'react-router-dom';
import { EventContext } from "@/src//app/context/event/EventProvider";
import CustomForm from "@/src/app/components/customForms/CurrentForm";
type Params = {
  url: any;
  provider: any;
  order_id?: any;
  attendee_id?: any;
};


const CustomForms: FC = (): ReactElement => {

  const params = useParams<any>();

  const { order_id, attendee_id, form_id, provider } = params;

  const { event, updateOrder, updateRouteParams, updateFormBuilderForms } = useContext<any>(EventContext);

  const [width, setWidth] = useState(0);

  const [loading, setLoading] = useState(true);

  const [show, setShow] = useState(true);

  const history = useHistory();

  const mounted = useRef(false);

  const [orderAttendee, setOrderAttendee] = useState<any>(null);

  const [formSettings, setFormSettings] = useState<any>({});

  const [customForms, setCustomForms] = useState<any>([]);

  const [currentForm, setCurrentForm] = useState<any>(null);

  const [order, setOrder] = useState(null);

  const style = {
    control: (base: any) => ({
      ...base,
      boxShadow: 'none'
    })
  };

  useEffect(() => {
    updateWindowDimensions();
    window.addEventListener('resize', updateWindowDimensions);
    return () => {
      window.removeEventListener('resize', updateWindowDimensions);
    };
  }, []);

  function updateWindowDimensions() {
    setWidth(window.innerWidth);
  }

  useEffect(() => {
    mounted.current = true;
    updateRouteParams({ ...params, page: 'custom-forms', orderAttendee: orderAttendee, form_settings: formSettings, });
    return () => {
      mounted.current = false;
    };
  }, [form_id]);

  useEffect(() => {
    updateRouteParams({ ...params, page: 'custom-forms', orderAttendee: orderAttendee, form_settings: formSettings });
  }, [orderAttendee, formSettings]);

  useEffect(() => {
    if (order_id && attendee_id) {
      setCurrentForm(null);
      setLoading(true);
      service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/get-order-attendee-status/${order_id}/${attendee_id}?provider=${provider}`)
        .then(
          response => {
            if (response.success && mounted.current) {
              if (response.data.form_builder_forms.length <= 0) {
                history.push(`/${event.url}/${provider}/order-summary/${order_id}`);
              }
              setOrderAttendee(response?.data?.order_attendee);
              updateOrder(response?.data?.order);
              setFormSettings(response?.data?.form_settings);
              setOrder(response?.data?.order);
              updateFormBuilderForms({ forms: [...response?.data.form_builder_forms], attendee_id, order_id });
              setCustomForms(response.data.form_builder_forms);
              const curform = response.data.form_builder_forms.find((item: any) => (item.id == form_id))
              if (curform !== undefined) {
                setCurrentForm(curform);
              }
              else if (response.data.form_builder_forms.length > 0) {
                history.push(`/${event.url}/${provider}/custom-forms/${order_id}/${attendee_id}/${response.data.form_builder_forms[0].id}`);
              }
              else {
                history.push(`/${event.url}/${provider}/order-summary/${order_id}`);
              }
              setLoading(false);

            }
          },
          error => { }
        );
    }
  }, [order_id, attendee_id, form_id]);

  const submitForm = (event_id: any, registration_form_id: any, data: any) => {

    setLoading(true);

    service.post(`${process.env.REACT_APP_API_URL}/organizer/form-builder/submitForm/${event_id}/${registration_form_id}`, { data: JSON.stringify(data), order_id: order_id, attendee_id: attendee_id, form_id: currentForm.id })
      .then(
        response => {

          if (response.status && mounted.current) {

            setCurrentForm(null);

            let formAnswered = {};

            if (localStorage.getItem(`FBXXAD${order_id}_${attendee_id}`) !== null) {
              formAnswered = JSON.parse(localStorage.getItem(`FBXXAD${order_id}_${attendee_id}`) as any);
            }

            localStorage.setItem(`FBXXAD${order_id}_${attendee_id}`, JSON.stringify({ ...formAnswered, [currentForm.id]: true }));

            const nextform = customForms.findIndex((item: any) => (item.id === currentForm.id)) + 1;

            if (nextform < customForms.length) {
              history.push(`/${event.url}/${provider}/custom-forms/${order_id}/${attendee_id}/${customForms[nextform].id}`);
            } else {
              completedAttendeeIteration(attendee_id);
            }

            setLoading(false);

          }
        }
      );
  }

  const goToNextForm = () => {

    setCurrentForm(null);

    let formAnswered = {};

    if (localStorage.getItem(`FBXXAD${order_id}_${attendee_id}`) !== null) {
      formAnswered = JSON.parse(localStorage.getItem(`FBXXAD${order_id}_${attendee_id}`) as any);
    }

    localStorage.setItem(`FBXXAD${order_id}_${attendee_id}`, JSON.stringify({ ...formAnswered, [currentForm.id]: true }));

    const nextform = customForms.findIndex((item: any) => (item.id === currentForm.id)) + 1;

    if (nextform < customForms.length) {
      history.push(`/${event.url}/${provider}/custom-forms/${order_id}/${attendee_id}/${customForms[nextform].id}`);
    } else {
      completedAttendeeIteration(attendee_id);
    }

  }


  const completedAttendeeIteration = (attendee_id: any) => {
    service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/completed-attendee-iteration`, { attendee_id: attendee_id, order_id: order_id, provider: provider })
      .then(
        response => {
          if (mounted.current) {
            if (response.success) {
              history.push(`/${event.url}/${provider}/order-summary/${order_id}`);
            }
          }
        },
        error => { }
      );
  }


  return (
    <React.Fragment>
      {loading && <Loader className='fixed' />}
      {currentForm !== null && orderAttendee !== null && <CustomForm
        event={event}
        order_id={order_id}
        provider={provider}
        attendee_id={attendee_id}
        orderAttendee={orderAttendee}
        form_id={currentForm.id}
        regFormId={orderAttendee.registration_form_id}
        submitForm={submitForm}
        goToNextForm={goToNextForm}
        order={order}
      />}
    </React.Fragment>
  )
};

export default CustomForms;