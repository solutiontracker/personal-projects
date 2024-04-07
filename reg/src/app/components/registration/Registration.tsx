import React, { ReactElement, FC, useContext, useEffect, useRef, useState } from "react";
import { useParams, useHistory, Link } from 'react-router-dom';
import ManageAttendee from '@/src/app/components/registration/ManageAttendee';
import ManageItem from '@/src/app/components/registration/ManageItem';
import ManageKeyword from '@/src/app/components/registration/ManageKeyword';
import ManageSubRegistration from '@/src/app/components/registration/ManageSubRegistration';
import { EventContext } from "@/src//app/context/event/EventProvider";
import { service } from '@/src/app/services/service';
import { postMessage } from '@/src/app/helpers';
import Documents from "../documents/Documents";
interface Props {
  event: Event;
  section: any;
}

type Params = {
  url: any;
  provider: any;
  order_id?: any;
  attendee_id?: any;
  registration_form_id?: any;
  ids?: any;
  section: string;
};

const Registration: FC<any> = (props: Props): ReactElement => {

  const { section, order_id, attendee_id, ids, registration_form_id, provider,  } = useParams<Params>();

  const history = useHistory();

  const [orderAttendee, setOrderAttendee] = useState<any>({});

  const [formSettings, setFormSettings] = useState<any>({});

  const params = useParams<Params>();

  const { event, validate_code, updatecurrentFormPaymentSettings, updateRouteParams, updateFormBuilderForms, updateOrder } = useContext<any>(EventContext);

  const goToSection = (section: any, o_id: any, a_id: any, f_id:any=0) => {
    if (section === "manage-items") {
      history.push(`/${event.url}/${provider}/manage-items/${o_id}/${a_id}`);
    } else if (section === "manage-keywords") {
      history.push(`/${event.url}/${provider}/manage-keywords/${o_id}/${a_id}`);
    } else if (section === "manage-sub-registrations") {
      history.push(`/${event.url}/${provider}/manage-sub-registrations/${o_id}/${a_id}`);
    } else if (section === "manage-hotel-booking" && o_id && a_id) {
      history.push(`/${event.url}/${provider}/manage-hotel-booking/${o_id}/${a_id}`);
    } else if (section === "manage-hotel-booking") {
      history.push(`/${event.url}/${provider}/manage-hotel-booking/${order_id}/${attendee_id}`);
    } else if (section === "custom-forms") {
      history.push(`/${event.url}/${provider}/custom-forms/${o_id}/${a_id}/${f_id}`);
    } else if (section === "manage-documents") {
      history.push(`/${event.url}/${provider}/manage-documents/${o_id}/${a_id}`);
    }
  }


  const mounted = useRef(false);

  useEffect(() => {
    if (order_id && attendee_id) {
      service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/get-order-attendee-status/${order_id}/${attendee_id}?provider=${provider}`)
        .then(
          response => {
            if (response.success && mounted.current) {
              setOrderAttendee(response?.data?.order_attendee);
              updateOrder(response?.data?.order);
              setFormSettings(response?.data?.form_settings);
              updatecurrentFormPaymentSettings(response?.data?.form_settings);
              //Update event info
              updateFormBuilderForms({forms:[...response?.data?.form_builder_forms
              ], attendee_id, order_id});
            }
          },
          error => { }
        );
    }
  }, [order_id, attendee_id]);

  const loadFormSeting = (setting: any, reload = false) => {
    if (!attendee_id || reload) {
      setFormSettings(setting);
      updatecurrentFormPaymentSettings(setting);
    }
  }

  useEffect(() => {
    if (Number(event?.eventsite_setting?.eventsite_public) === 1 && !validate_code) {
      history.push(`/${event.url}/event-registration-code`);
    } else if (Number(event?.eventsite_setting?.eventsite_public) === 1 && validate_code && Number(validate_code) !== Number(event.id)) {
      history.push(`/${event.url}/event-registration-code`);
    }
  }, [event]);

  useEffect(() => {
    mounted.current = true;
    updateRouteParams({ ...params, page: 'registration-information', orderAttendee: orderAttendee, form_settings: formSettings });
    return () => { mounted.current = false; };
  }, []);

  useEffect(() => {
    postMessage({ page: section });
  }, [section]);

  useEffect(() => {
    updateRouteParams({ ...params, page: 'registration-information', orderAttendee: orderAttendee, form_settings: formSettings });
  }, [orderAttendee, formSettings]);

  return (
    <React.Fragment>
      {event?.id && (

        <React.Fragment>

          <div className="row d-flex ebs-title-box align-items-center">
            <div className="col-6">
              <h2 className="section-title">{event?.labels?.REGISTRATION_FORM_THE_REGISTRATION}</h2>
            </div>
            {Number(event?.order?.order_detail?.order?.edit_mode) === 1 && (
              <div className="col-6 text-right">
                <Link to={`/${event.url}/${provider}/order-summary/${order_id}`} className="ebs-back-summary"><i className="material-icons">keyboard_backspace</i>{event?.labels?.REGISTRATION_FORM_BACK_TO_SUMMARY}</Link>
              </div>
            )}
          </div>

          <div className="row">
            <div className="col-12">
              <p className="ebs-attendee-caption">{event?.labels?.REGISTRATION_FORM_STEP_1_DESCRIPTION}</p>
            </div>
          </div>

          <ManageAttendee
            event={event}
            section={section}
            order_id={order_id}
            provider={provider}
            attendee_id={attendee_id}
            ids={ids}
            registration_form_id={registration_form_id}
            goToSection={goToSection}
            orderAttendee={orderAttendee}
            formSettings={formSettings}
            loadFormSeting={loadFormSeting}
          />
          
          {((Number(formSettings?.show_items) === 1 && Number(event?.eventsite_setting?.payment_type) === 0) || (Number(formSettings?.skip_items_step) === 0 && Number(event?.eventsite_setting?.payment_type) === 1)) && (
            <ManageItem
              event={event}
              section={section}
              order_id={order_id}
              provider={provider}
              attendee_id={attendee_id}
              goToSection={goToSection}
              orderAttendee={orderAttendee}
              formSettings={formSettings}
            />
          )}

          {Number(formSettings?.show_business_dating) === 1 ? (
            <ManageKeyword
              event={event}
              section={section}
              order_id={order_id}
              provider={provider}
              attendee_id={attendee_id}
              goToSection={goToSection}
              orderAttendee={orderAttendee}
              formSettings={formSettings}
            />
          ) : ''}

          {Number(formSettings?.show_subregistration) === 1 ? (
            <ManageSubRegistration
              event={event}
              section={section}
              order_id={order_id}
              provider={provider}
              attendee_id={attendee_id}
              goToSection={goToSection}
              orderAttendee={orderAttendee}
              formSettings={formSettings}
            />
          ) : ''}

          {Number(formSettings?.show_required_documents) === 1 ? <Documents
            event={event}
            section={section}
            order_id={order_id}
            provider={provider}
            attendee_id={attendee_id}
            ids={ids}
            registration_form_id={registration_form_id}
            goToSection={goToSection}
            orderAttendee={orderAttendee}
            formSettings={formSettings}
            loadFormSeting={loadFormSeting}
          /> : null}

        </React.Fragment>

      )}
    </React.Fragment>
  );
};

export default Registration;