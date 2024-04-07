import React, { ReactElement, FC, useEffect, useState, useContext, useRef } from "react";
import { useParams } from "react-router-dom";
import { service } from '@/src/app/services/service';
import { EventContext } from "@/src//app/context/event/EventProvider";
import Loader from '@/src/app/components/forms/Loader';
import { Link } from "react-router-dom";
import { facebookPixel, linkedinPixel, googleTagManager } from "../helpers";
import in_array from "in_array";

type Params = {
  url: any;
  provider: any;
  order_id: any;
};

const OrderSummary: FC<any> = (): ReactElement => {

  const [width, setWidth] = useState(0);

  const { event, cookie, updateOrder } = useContext<any>(EventContext);

  const { order_id, provider } = useParams<Params>();

  const [order, setOrder] = useState<any>({});

  const [loading, setLoading] = useState(true);

  const mounted = useRef(false);

  useEffect(() => {

    updateWindowDimensions();

    window.addEventListener('resize', updateWindowDimensions);

    document.body.addEventListener('click', removePopup);

    return () => {
      window.removeEventListener('resize', updateWindowDimensions);
    };

  }, []);

  useEffect(() => {

    mounted.current = true;

    return () => { mounted.current = false; };

  }, []);

  useEffect(() => {

    loadSummary(event, order_id);

  }, [cookie]);

  function loadSummary(event: any, order_id: any) {
    service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/order-summary/${order_id}?provider=${provider}`)
      .then(
        response => {
          if (response.success && mounted.current) {

            setOrder(response.data.order);

            updateOrder(response.data.order.order_detail.order);

            if (cookie === "all") {
              if (event.settings?.facebook_pixel_id !== undefined && event.settings?.facebook_pixel_id) {
                facebookPixel(event.settings?.facebook_pixel_id, false, (event.settings?.analytics_purchase_event_name !== undefined && event.settings?.analytics_purchase_event_name ? event.settings?.analytics_purchase_event_name : 'Purchase'), {
                  'value': response.data.order.order_detail.order.id,
                  'order_total': response.data.order.order_detail.grand_total_display,
                  'order_number': response.data.order.order_detail.order.order_number,
                  'attendee_id': response.data.order.order_detail.order.attendee_id,
                  'event_id': response.data.order.order_detail.order.event_id,
                  'order_id': response.data.order.order_detail.order.id,
                  'currency': response.data.order.currency
                });
              }

              if (event.settings?.linkedin_partner_id !== undefined && event.settings?.linkedin_partner_id && event.settings?.linkedin_conversion_id !== undefined && event.settings?.linkedin_conversion_id) {
                linkedinPixel(event.settings?.linkedin_partner_id, event.settings?.linkedin_conversion_id, false);
              }

              if (event.settings?.google_analytics_id !== undefined && event.settings?.google_analytics_id) {
                googleTagManager(event.settings?.google_analytics_id, false, (event.settings?.analytics_purchase_event_name !== undefined && event.settings?.analytics_purchase_event_name ? event.settings?.analytics_purchase_event_name : 'Purchase'), {
                  'value': response.data.order.order_detail.order.id,
                  'order_total': response.data.order.order_detail.grand_total_display,
                  'order_number': response.data.order.order_detail.order.order_number,
                  'attendee_id': response.data.order.order_detail.order.attendee_id,
                  'event_id': response.data.order.order_detail.order.event_id,
                  'order_id': response.data.order.order_detail.order.id,
                  'currency': response.data.order.currency
                });
              }

              if (event.settings?.matomo_domain_id !== undefined && event.settings?.matomo_domain_id) {
                var _mtm = window._mtm = window._mtm || [];
                _mtm.push({ 'mtm.startTime': (new Date().getTime()), 'event': 'mtm.Start' });
                (function () {
                  var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
                  g.async = true; g.src = 'https://cdn.matomo.cloud/' + event.settings?.matomo_domain_id + '/container_ynlL2iw5.js'; s.parentNode.insertBefore(g, s);
                })();
              }
              
            }

            setLoading(false);
          }
        },

        error => {
          setLoading(false);
        }
      );

  }

  const removePopup = (e: any) => {

    if (e.target.className !== 'btn_click icons') {
      const items = document.querySelectorAll(".summry-panel .btn_click");
      for (let i = 0; i < items.length; i++) {
        const element = items[i];
        element.classList.remove("active");
      }
    }

  }

  function updateWindowDimensions() {

    setWidth(window.innerWidth);

  }

  useEffect(() => {

    if (event?.eventsite_setting?.third_party_redirect_url && Number(event?.eventsite_setting?.third_party_redirect) === 1) {
      setTimeout(function () {
        window.location = event?.eventsite_setting?.third_party_redirect_url;
      }, 5000);
    }

  }, []);

  return (
    <React.Fragment>
      {loading ? (
        <Loader className='fixed' />
      ) : (
        <div className="wrapper-box">
          <div className="registration-success">
            <div className="header-area">
              <img src={require('@/src/img/ico-success.svg')} alt="" />
              <h3>{event?.labels?.EVENTSITE_REGISTRATION_SUCCESSFUL || 'Registration successful'}</h3>
              {Number(event?.attendee_setting?.attendee_reg_verification) === 0 ? (
                <>
                  {Number(event?.eventsite_setting?.new_message_temp) === 0 ? (
                    <>
                      {Number(order.order_detail?.order?.is_waitinglist) === 1 ? (
                        <p>{event?.labels?.WAITING_LIST_SUCCESS_REGISTRATION}</p>
                      ) : (
                        <p>{event?.labels?.REGISTRATION_SUCCESS_MESSAGE}</p>
                      )}</>
                  ) : (
                    <p>{event?.labels?.REGISTRATION_SUCCESS_MESSAGE}</p>
                  )}
                </>
              ) : (
                <p>{event?.labels?.EVENTSITE_QUESTIONAIR_SUBMIT_MESSAGE}</p>
              )}
            </div>
          </div>
          <div className="bottom-button text-center">
            {Number(event?.eventsite_setting?.new_message_temp) === 1 && (
              <>
                {Number(event?.eventsite_setting?.registration_after_login) === 1 && Number(event?.eventsite_setting?.go_to_account) === 1 && (
                  <Link className="btn btn-save-addmore btn-loader" to={`/${event.url}/${provider}/manage-attendee`}>
                    {event?.labels?.EVENTSITE_GO_TO_YOURACOUNT || 'Go TO YOUR ACCOUNT'}
                  </Link>
                )}
                <>
                  {Number(event?.eventsite_setting?.go_to_home_page) === 1 && (
                    event?.eventsite_setting?.third_party_redirect_url && Number(event?.eventsite_setting?.third_party_redirect) ? (
                      <a href={event?.eventsite_setting?.third_party_redirect_url} className="btn btn-save-addmore btn-loader">
                        {event?.labels?.EVENTSITE_GO_TO_HOMEPAGE || 'GO TO HOMEPAGE'}
                      </a>
                    ) : (
                      <>
                        {in_array(provider, ['attendee']) && (
                          <a className="btn btn-save-addmore btn-loader" href={`${process.env.REACT_APP_REG_SITE_URL}/${event.url}`}>
                            {event?.labels?.EVENTSITE_GO_TO_HOMEPAGE || 'GO TO HOMEPAGE'}
                          </a>
                        )}
                      </>
                    )
                  )}
                </>
              </>
            )}
            {Number(event?.eventsite_setting?.eventsite_add_calender) === 1 && (
              <a href={`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/add-to-calender/${order_id}`} style={{ minWidth: '1px' }} className="btn btn-save-next btn-loader">
                <i style={{ marginRight: 8, fontSize: 24, position: 'relative', top: -1 }} className="material-icons">calendar_month</i>
                {event?.labels?.REGISTRATION_FORM_ADD_TO_CALENER}
              </a>
            )}
          </div>
        </div>
      )}
    </React.Fragment>
  );
};

export default OrderSummary;