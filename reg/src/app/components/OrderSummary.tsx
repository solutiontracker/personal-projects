import React, { ReactElement, FC, useEffect, useState, MouseEvent, useContext, useRef } from "react";
import Input from "@/src/app/components/forms/Input";
import { Link, useParams, useHistory, useLocation } from "react-router-dom";
import { service } from '@/src/app/services/service';
import Event from '@/src/app/components/event/interface/Event';
import DropDown from '@/src/app/components/forms/DropDown';
import { EventContext } from "@/src//app/context/event/EventProvider";
import Loader from '@/src/app/components/forms/Loader';
import Popup from '@/src/app/components/forms/Popup';
import socketIOClient from "socket.io-client";
import { confirmAlert } from 'react-confirm-alert';
import toast, { Toaster } from 'react-hot-toast';
import { Scrollbars } from 'react-custom-scrollbars';
import 'react-confirm-alert/src/react-confirm-alert.css';
import in_array from "in_array";
import { postMessage as postMessageScript } from "@/src/app/helpers";
import { ReactSVG } from "react-svg";
import moment from "moment-timezone";

type Params = {
  url: any;
  provider: any;
  order_id: any;
  is_waiting?: any;
};

const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

const OrderSummary: FC<any> = (): ReactElement => {

  const search = useLocation().search;

  const [security_key, setSecurityKey] = useState(new URLSearchParams(search).get("security_key"));

  const [voucher, setVoucher] = useState('');

  const [width, setWidth] = useState(0);

  const [toggle, setToggle] = useState(false);

  const { event, updateWaitinglist, waitinglist, updateRouteParams, updateOrder } = useContext<any>(EventContext);

  const { order_id, provider, is_waiting } = useParams<Params>();

  const [order, setOrder] = useState<any>({});

  const [currency, setCurrency] = useState('');

  const [loading, setLoading] = useState(true);

  const [action, setAction] = useState("");

  const history = useHistory();

  const [popup, setPopup] = useState(false);

  const [bamboraPayment, setBamboraPaymentUrl] = useState("");

  const [convergePayment, setConvergePaymentUrl] = useState("");

  const [netsPayment, setNetsPaymentUrl] = useState("");

  const [tos, setTos] = useState<any>({});

  const [sale_types, setSaleTypes] = useState<any>([]);

  const [sale_type, setSaleType] = useState<number>();

  const [errors, setErrors] = useState<any>({});

  const [success_message, setSuccessMessage] = useState<string>('');

  const mounted = useRef(false);

  const initialRender = useRef(false);

  const params = useParams<Params>();

  useEffect(() => {
    updateWindowDimensions();
    window.addEventListener('resize', updateWindowDimensions);
    document.body.addEventListener('click', removePopup);
    postMessageScript({ page: 'payment-information' });
    return () => {
      window.removeEventListener('resize', updateWindowDimensions);
    };
  }, []);

  useEffect(() => {
    mounted.current = true;
    updateRouteParams({ ...params, page: 'order-summary' });
    return () => { mounted.current = false; };
  }, []);

  useEffect(() => {
    loadSummary(event, order_id);
  }, []);

  useEffect(() => {
    if (is_waiting) {
      updateWaitinglist(order_id);
    }
  }, [is_waiting, order_id]);

  useEffect(() => {
    if (security_key) {
      localStorage.setItem('security_key', security_key);
    }
  }, [security_key]);

  function loadSummary(event: any, order_id: any) {
    service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/order-summary/${order_id}?provider=${provider}`)
      .then(
        response => {
          if (response.success && mounted.current) {
            if (response.data.order?.order_detail?.order?.status !== "completed" || (response.data.order?.order_detail?.order?.status === "completed" && (Number(response.data.order?.order_detail?.order?.is_waitinglist) === 1 || in_array(provider, ['sale', 'admin'])))) {
              setOrder(response.data.order);
              updateOrder(response.data.order?.order_detail?.order);
              setTos({ ...response.data.tos, active: in_array(provider, ["attendee", "embed"]) && Number(event?.event_disclaimer_setting?.reg_site) === 1 && response?.data?.tos?.purchase_policy && Number(event?.eventsite_setting?.payment_type) === 1 && response.data.order?.order_detail?.order?.grand_total > 0 ? false : true });
              setCurrency(response.data.order.currency);
              setVoucher(response.data.order?.order_detail?.order?.code);
              setSaleTypes(response.data.sale_types);
              setSaleType(response.data.order?.order_detail?.order?.sale_type);
              setLoading(false);
            } else {
              history.push(`/${event.url}/${provider}`);
            }
            setAction("");
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

  const handleClick = (e: MouseEvent) => {
    e.stopPropagation();
    const items = document.querySelectorAll(".summry-panel .btn_click");
    for (let i = 0; i < items.length; i++) {
      const element = items[i];
      if (element.classList === e.currentTarget.classList) {
        e.currentTarget.classList.toggle("active");
      } else {
        element.classList.remove("active");
      }
    }
  }

  const openLink = (link: any) => () => {
    history.push(link);
  }

  const handleVoucher = (e: any) => {
    if (e.target.value !== undefined) {
      initialRender.current = true;
      setVoucher(e.target.value);
    }
  }

  useEffect(() => {
    const timeoutId = setTimeout(() => {
      if (voucher && initialRender.current) {
        submitVoucher(voucher);
      }
    }, 1000);
    return () => clearTimeout(timeoutId);
  }, [voucher]);

  const submitVoucher = (voucher_code: any) => {
    if (!action) {
      setAction("submit-voucher");
      service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/apply-voucher/${order_id}`, { voucher_code: voucher_code, provider: provider })
        .then(
          response => {
            if (mounted.current) {
              if (response.success) {
                setErrors({});
                toast.success(response.message, {
                  position: "bottom-center"
                })
                loadSummary(event, order_id);
              } else {
                setAction("");
                setErrors(response.errors);
              }
            }
          },
          error => {
            setAction("");
          }
        );
    }
  }

  const removeVoucher = (evt: any) => {
    evt.preventDefault();
    if (!action) {
      setAction("submit-voucher");
      service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/remove-voucher/${order_id}`, { voucher_code: voucher, provider: provider })
        .then(
          response => {
            if (mounted.current) {
              if (response.success) {
                setErrors({});
                toast.success(response.message, {
                  position: "bottom-center"
                })
                setVoucher('');
                loadSummary(event, order_id);
              } else {
                setAction("");
                setErrors(response.errors);
              }
            }
          },
          error => {
            setAction("");
          }
        );
    }
  }

  const deleteAttendee = (attendee_id: any) => (evt: any) => {
    evt.preventDefault();
    confirmAlert({
      customUI: ({ onClose }) => {
        return (
          <div id="loader-wrapper" className="fixed ebs-popup-container">
            <div className="ebs-popup-wrapper" style={{ maxWidth: '550px' }}>
              <span onClick={() => {
                onClose();
              }} className="ebs-close link"><i className="material-icons">close</i></span>
              <header className="ebs-header">
                <h3 className='link'>{event?.labels?.REGISTRATION_FORM_DELETE_ATTENDEE}</h3>
              </header>
              <div className="ebs-popup-content">
                <div>
                  {event?.labels?.REGISTRATION_FORM_ARE_YOU_SURE_TO_DELETE_ATTENDEE}
                </div>
              </div>
              <div className="ebs-popup-buttons text-center" style={{ marginBottom: '25px' }}>
                <div className="btn bordered" onClick={() => {
                  onClose();
                }}>{event?.labels?.GENERAL_CANCEL}</div>
                <div className="btn btn-primary" onClick={() => {
                  setLoading(true);
                  service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/attendee/delete/${order_id}/${attendee_id}?provider=${provider}`)
                    .then(
                      response => {
                        if (mounted.current) {
                          if (response.success) {
                            loadSummary(event, order_id);
                          } else {
                            toast.error(response.message, {
                              position: "bottom-center"
                            })
                          }
                          setLoading(false);
                          onClose();
                        }
                      },
                      error => { }
                    );

                }}>{event?.labels?.GENERAL_DELETE}</div>
              </div>
            </div>
          </div>
        );
      },
    });
  }

  const deleteHotel = (order_hotel_id: any) => (evt: any) => {
    evt.preventDefault();
    confirmAlert({
      customUI: ({ onClose }) => {
        return (
          <div id="loader-wrapper" className="fixed ebs-popup-container">
            <div className="ebs-popup-wrapper" style={{ maxWidth: '550px' }}>
              <span onClick={() => {
                onClose();
              }} className="ebs-close link"><i className="material-icons">close</i></span>
              <header className="ebs-header">
                <h3 className='link'>{event?.labels?.REGISTRATION_FORM_DELETE_HOTEL}</h3>
              </header>
              <div className="ebs-popup-content">
                <div>
                  {event?.labels?.REGISTRATION_FORM_ARE_YOU_SURE_WANT_TO_DELETE}
                </div>
              </div>
              <div className="ebs-popup-buttons text-center" style={{ marginBottom: '25px' }}>
                <div className="btn bordered" onClick={() => {
                  onClose();
                }}>{event?.labels?.GENERAL_CANCEL}</div>
                <div className="btn btn-primary" onClick={() => {
                  setLoading(true);
                  setLoading(true);
                  service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/hotel/delete/${order_id}/${order_hotel_id}`, { order_hotel_id: order_hotel_id, provider: provider })
                    .then(
                      response => {
                        if (mounted.current) {
                          if (response.success) {
                            loadSummary(event, order_id);
                          } else {
                            setLoading(false);
                            toast.error(response.message, {
                              position: "bottom-center"
                            })
                          }
                          onClose();
                        }
                      },
                      error => { }
                    );
                }}>{event?.labels?.GENERAL_DELETE}</div>
              </div>
            </div>
          </div>
        );
      },
    });
  }

  const submitOrder = (evt: any) => {
    evt.preventDefault();
    if (!action) {
      if ((tos?.active !== undefined && tos?.active) || !in_array(provider, ['attendee', 'embed'])) {
        if (!order?.order_detail?.order_billing_detail?.billing_ean && order?.order_detail?.order?.grand_total > 0 && order?.order_detail?.order_billing_detail?.billing_company_type === "private" && (Number(order?.order_detail?.order?.is_waitinglist) === 0 || Number(waitinglist) === Number(order_id)) && !in_array(provider, ['admin', 'sale'])) {
          if (order?.order_detail?.order?.status === "completed") {
            history.push(`/${event.url}/${provider}/registration-success/${order_id}`);
          } else {
            if (Number(event?.payment_setting?.billing_merchant_type) === 7) {
              history.push(`/${event.url}/${provider}/payment/stripe/${order_id}`);
            } else if (Number(event?.payment_setting?.billing_merchant_type) === 9) {
              setAction("submit-order");
              createPayment('create-nets-payment-intent');
            } else if (Number(event?.payment_setting?.billing_merchant_type) === 5) {
              setAction("submit-order");
              createPayment('create-quickpay-payment-intent');
            } else if (Number(event?.payment_setting?.billing_merchant_type) === 8) {
              setAction("submit-order");
              createPayment('create-bambora-payment-intent');
            } else if (Number(event?.payment_setting?.billing_merchant_type) === 10) {
              setAction("submit-order");
              createPayment('create-converge-payment-intent');
            }
          }
        } else {
          if (order?.order_detail?.order?.platform === 'eventsite') {
            sendOrderRequest(1);
          } else if (in_array(provider, ['admin']) && Number(event?.payment_setting?.hide_credit_note_confirmation_popup) === 0) {
            confirmAlert({
              customUI: ({ onClose }) => {
                return (
                  <div id="loader-wrapper" className="fixed ebs-popup-container">
                    <div className="ebs-popup-wrapper" style={{ maxWidth: '550px' }}>
                      <span onClick={() => {
                        onClose();
                      }} className="ebs-close link"><i className="material-icons">close</i></span>
                      <header className="ebs-header">
                        {Number(event?.eventsite_setting?.payment_type) === 1 ? (
                          <>
                            {(in_array(Number(event?.payment_setting?.only_create_or_create_send_credit_note), [1, 3]) ? (
                              <h3 className='link'>{event?.interface_labels?.order?.EVENTSITE_CREATE_AND_SEND_CREDIT_NOTE}</h3>
                            ) : (
                              <h3 className='link'>{event?.interface_labels?.order?.EVENTSITE_CREATE_A_CREDIT_NOTE_ONLY}</h3>
                            ))}
                          </>
                        ) : (
                          <h3 className='link'>{event?.interface_labels?.order?.EVENTSITE_UPDATE_ORDER}</h3>
                        )}
                      </header>
                      <div className="ebs-popup-content">
                        <div>
                          {Number(event?.eventsite_setting?.payment_type) === 1 ? (
                            <>
                              {(in_array(Number(event?.payment_setting?.only_create_or_create_send_credit_note), [1, 3]) ? (
                                <>
                                  {Number(event?.payment_setting?.only_create_or_create_send_credit_note) === 3 ? (
                                    <>
                                      <div className="radio-check-field style-radio">
                                        <label style={{ margin: 0, height: '14px' }} className='label-radio' htmlFor="credit_note_send">
                                          <input type="radio" id="credit_note_send" name="send_credit" checked />
                                          <span>{event?.interface_labels?.order?.EVENTSITE_CREDIT_NOTE_SEND_OPTION_DETAIL}</span>
                                        </label>
                                      </div>
                                      <br></br>
                                      <div className="radio-check-field style-radio">
                                        <label style={{ margin: 0, height: '14px' }} className='label-radio' htmlFor="credit_only">
                                          <input type="radio" id="credit_only" name="send_credit" />
                                          <span>{event?.interface_labels?.order?.EVENTSITE_CREDIT_NOTE_ONLY_OPTION_DETAIL}</span>
                                        </label>
                                      </div>
                                    </>
                                  ) : (
                                    <h3 className='link'>{event?.interface_labels?.order?.EVENTSITE_CREDIT_NOTE_SEND_OPTION_DETAIL}</h3>
                                  )}
                                </>
                              ) : (
                                <h3 className='link'>{event?.interface_labels?.order?.EVENTSITE_CREDIT_NOTE_ONLY_OPTION_DETAIL}</h3>
                              ))}
                            </>
                          ) : (
                            <h3 className='link'>{event?.interface_labels?.order?.EVENTSITE_UPDATE_ORDER_CONFIRMATION_TITLE}</h3>
                          )}
                        </div>
                      </div>
                      <div className="ebs-popup-buttons text-center" style={{ marginBottom: '25px' }}>
                        <div className="btn bordered" onClick={() => {
                          onClose();
                        }}>{event?.interface_labels?.order?.EVENTSITE_UPDATE_ORDER_CANCEL}</div>
                        <div className="btn btn-primary" onClick={() => {
                          if (Number(event?.eventsite_setting?.payment_type) === 1 && Number(event?.payment_setting?.only_create_or_create_send_credit_note) === 3) {
                            const myElement: any = document.getElementById('credit_note_send');
                            if (myElement && myElement.checked) {
                              sendOrderRequest(1);
                            } else {
                              sendOrderRequest(0);
                            }
                          } else {
                            sendOrderRequest(Number(event?.eventsite_setting?.payment_type) === 1 && in_array(Number(event?.payment_setting?.only_create_or_create_send_credit_note), [1]) ? 1 : 0);
                          }
                          onClose();
                        }}>{event?.interface_labels?.order?.EVENTSITE_UPDATE_ORDER_SUBMIT}</div>
                      </div>
                    </div>
                  </div>
                );
              },
            });
          } else {
            sendOrderRequest(0);
          }
        }
      }
    }
  }

  const sendOrderRequest = (credit_note: number) => {
    setAction("submit-order");
    setSuccessMessage('');
    const url = `${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/submit-order/${order_id}?security_key=${localStorage.getItem('security_key')!}`;
    service.post(url, { provider: provider, credit_note: credit_note, sale_type: sale_type })
      .then(
        response => {
          if (mounted.current) {
            if (response.success) {
              setErrors({});
              if (!in_array(provider, ['attendee', 'embed'])) {
                postMessage(order_id);
                toast.success(response.message, {
                  position: "top-center"
                });
                setSuccessMessage(response.message);
              } else {
                history.push(`/${event.url}/${provider}/registration-success/${order_id}`);
              }
            } else {
              setErrors(response.errors);
            }
            setAction("");
          }
        },
        error => {
          setAction("");
        }
      );
  }

  const createPayment = (alias: any) => {
    service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/${alias}/${order_id}`, { amount: order?.order_detail?.order?.grand_total, currency: order?.currency, provider: provider })
      .then(
        response => {
          if (mounted.current) {
            if (response.success) {
              if (alias === 'create-quickpay-payment-intent') {
                window.open(response?.link);
              } else if (alias === 'create-nets-payment-intent') {
                setNetsPaymentUrl(response?.link);
              } else if (alias === 'create-bambora-payment-intent') {
                setBamboraPaymentUrl(response?.link);
              } else if (alias === 'create-converge-payment-intent') {
                setConvergePaymentUrl(response?.link);
              }
              setErrors({});
            } else {
              if (response.message) {
                toast.success(response.message, {
                  position: "bottom-center"
                })
              } else {
                setErrors(response.errors);
              }
            }
            setAction("");
          }
        },
        error => {
          setAction("");
        }
      );
  }

  useEffect(() => {
    socket.on(`event-buizz:registration-order-${order_id}`, (data: any) => {
      const json = JSON.parse(data.info);
      if (json.payment === "accepted") {
        history.push(`/${event.url}/${provider}/registration-success/${order_id}`);
      } else if (json.payment === "decline") {
        setAction("");
        setConvergePaymentUrl("");
        setNetsPaymentUrl("");
        setBamboraPaymentUrl("");
        toast.error("Payment decline", {
          position: "bottom-center"
        })
      }
    });

    return () => {
      //destroy socket
      socket.off(`event-buizz:registration-order-${order_id}`);
    };

  });

  const postMessage = (order_id: number) => {
    if (window && window.parent) {
      window.parent.postMessage({ order_id: order_id }, '*');
    }
  }

  const handleChange = (input: any, type?: any) => (e: any) => {
    if (type === 'select') {
      if (input === 'sale_type') {
        setSaleType(e.value);
        service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/update-sale-type/${order_id}?provider=${provider}`, { provider: provider, sale_type: e.value })
          .then(
            response => { }
          );
      }
    }
  }

  const getKeyValue = function <T, U extends keyof T>(obj: T, key: U) { return obj && obj[key] !== undefined ? obj[key] : '' }

  const getSelectedLabel = (item: any, id: any) => {
    if (item && item.length > 0 && id) {
      const obj = item.find((o: any) => o.id.toString() === id.toString());
      return (obj ? obj.name : '');
    }
  }

  return (
    <React.Fragment>
      <div className="row d-flex">
        {loading ? (
          <Loader className='fixed' />
        ) : (
          <React.Fragment>
            <div className="col-9">
              <header className="header-review">
                <h2 className="section-title">{event?.labels?.REGISTRATION_FORM_REVIEW_ORDER}</h2>
                {(order?.order_detail?.order?.status !== "completed" || (order?.order_detail?.order?.status === "completed" && Number(order?.order_detail?.order?.is_waitinglist) === 0)) && Number(event?.payment_setting?.evensite_additional_attendee) === 1 && (
                  <div className="top-buttons">
                    <button className="btn" onClick={openLink(`/${event.url}/${provider}/manage-attendee/${order_id}`)}><img src={require('@/src/img/ico-attendee.svg')} alt="" /> {event?.labels?.REGISTRATION_FORM_ADD_ATTENDEE}</button>
                  </div>
                )}
              </header>
              <div className="wrapper-box order-summry">
                <header className="header-section">
                  <h3 style={{ cursor: 'auto' }}>{event?.labels?.REGISTRATION_FORM_ORDER_SUMMARY}</h3>
                </header>
                {event?.event_description?.detail?.last_description && (
                  <p className="ebs-attendee-caption" dangerouslySetInnerHTML={{ __html: event?.event_description?.detail?.last_description }}></p>
                )}
                {order && order.order_detail && order.order_detail.attendee_summary_detail.length > 0 && (
                  <div className="summry-list-section">
                    <h4>{order.order_detail.attendee_summary_detail.length} {event?.labels?.REGISTRATION_FORM_ATTENDEES}</h4>
                    {order.order_detail.attendee_summary_detail.map((attendee: any, attendeeKey: any) =>
                      <div className="summry-row" key={attendeeKey}>
                        <div className="summry-description">
                          <span className="icons">
                            <img src={require("@/src/img/ico-person.svg")} alt="" />
                          </span>
                          <h5>{`${attendee.attendee_info.first_name} ${attendee.attendee_info.last_name}`}</h5>
                          <p>{attendee.attendee_info.email}</p>
                          {attendee.order_attendee.status !== 'complete' && (
                            <div className="ebs-error">
                              <i className="material-icons">info</i> <em>{event?.labels?.REGISTRATION_FORM_PLEASE_COMPLETE_INFORMATION}</em>
                            </div>
                          )}
                        </div>
                        {Number(event?.eventsite_setting?.payment_type) === 1 && (
                          <div className="summry-price">
                            <div className="item-price">{attendee.sub_total}</div>
                          </div>
                        )}
                        {(order?.order_detail?.order?.status !== "completed" || (order?.order_detail?.order?.status === "completed" && Number(order?.order_detail?.order?.is_waitinglist) === 0)) && (
                          <div className="summry-panel">
                            {width <= 768 && (
                              <React.Fragment>
                                <span onClick={handleClick} className="btn_click icons">
                                  <img src={require("@/src/img/ico-dots.svg")} alt="" />
                                </span>
                              </React.Fragment>
                            )}
                            <div className="wrapper-panel-box">
                              {Number(attendee?.payment_form_setting?.show_required_documents) === 1 && attendee.order_attendee.status === 'complete' && <span style={{ fontSize: "12px" }} onClick={openLink(`/${event.url}/${provider}/manage-documents/${order_id}/${attendee.attendee_info.id}`)}>
                                {attendee.documents.length} {" "}
                                <i className="icons">
                                  <img src={require("@/src/img/ico-paperclip.svg")} alt="" />
                                </i>
                              </span>}
                              {Number(attendee?.payment_form_setting?.show_hotels) === 1 && attendee.order_attendee.status === 'complete' && (
                                <span onClick={openLink(`/${event.url}/${provider}/manage-hotel-booking/${order_id}/${attendee.attendee_info.id}`)}>
                                  <i className="icons">
                                    <img src={require("@/src/img/ico-hotel.svg")} alt="" />
                                  </i>
                                  {width <= 768 && 'Book hotels'}
                                </span>
                              )}
                              <span onClick={openLink(`/${event.url}/${provider}/manage-attendee/${order_id}/${attendee.attendee_info.id}`)}>
                                <i className="icons">
                                  <img src={require("@/src/img/ico-edit.svg")} alt="" />
                                </i>
                                {width <= 768 && (event?.labels?.REGISTRATION_FORM_EDIT_LABEL || 'Edit')}
                              </span>
                              {order?.order_detail?.attendee_summary_detail?.length > 1 && order?.order_detail?.order?.platform !== 'eventsite' && (
                                <span onClick={deleteAttendee(attendee.attendee_info.id)}>
                                  <i className="icons">
                                    <img src={require("@/src/img/ico-delete.svg")} alt="" />
                                  </i>
                                  {width <= 768 && (event?.labels?.REGISTRATION_FORM_DELETE_LABEL || 'Delete')}
                                </span>
                              )}
                            </div>
                          </div>
                        )}
                      </div>
                    )}
                  </div>
                )}
                {order && order.order_detail && order.order_detail.hotel.length > 0 && (
                  <div className="summry-list-section">
                    <h4>{event?.labels?.REGISTRATION_FORM_HOTEL_BOOKING_HEADING} - {order.order_detail.hotel.length} {event?.labels?.REGISTRATION_FORM_HOTELS}</h4>
                    {order.order_detail.hotel.map((hotel: any, hotelKey: any) =>
                      <div className="summry-row" key={hotelKey}>
                        <div className="summry-description">
                          <span className="icons">
                            <img src={require("@/src/img/ico-hotel-bed.svg")} alt="" />
                          </span>
                          <h5>{hotel.name}</h5>
                          {hotel?.persons?.length > 0 && hotel?.persons.map((person: any, PersonKey: any) => (
                            <p key={PersonKey}>{`${person.first_name} ${person.last_name}`}</p>
                          ))}
                          <p>{hotel.date_range_display}</p>
                        </div>
                        <div className="summry-price">
                          {Number(event?.payment_setting?.show_hotel_prices) === 1 && (
                            <>
                              <div className="item-price">{hotel.sub_total_display}</div>
                              <div className="per-night">{hotel.nights} {event?.labels?.REGISTRATION_FORM_HOTEL_NIGHTS_LABEL}, {hotel.rooms} {event?.labels?.REGISTRATION_FORM_HOTEL_ROOMS_LABEL}</div>
                            </>
                          )}
                        </div>
                        {(order?.order_detail?.order?.status !== "completed" || (order?.order_detail?.order?.status === "completed" && Number(order?.order_detail?.order?.is_waitinglist) === 0)) && (
                          <div className="summry-panel">
                            {width <= 768 && (
                              <React.Fragment>
                                <span onClick={handleClick} className="btn_click icons">
                                  <img src={require("@/src/img/ico-dots.svg")} alt="" />
                                </span>
                              </React.Fragment>
                            )}
                            <div className="wrapper-panel-box">
                              <span onClick={deleteHotel(hotel.id)}>
                                <i className="icons">
                                  <img src={require("@/src/img/ico-delete.svg")} alt="" />
                                </i>
                                {width <= 768 && (event?.labels?.REGISTRATION_FORM_DELETE_LABEL || 'Delete')}
                              </span>
                            </div>
                          </div>
                        )}
                      </div>
                    )}
                  </div>
                )}
              </div>

              {width <= 768 && (
                <div className="wrapper-box checkout-sidebar">
                  {toggle && <div id="checkout-sidebar-detail">
                    <header className="header-section">
                      <h3 onClick={() => setToggle(!toggle)}> <i className="material-icons">close</i> {event?.labels?.REGISTRATION_FORM_CHECKOUT}</h3>
                    </header>

                    {(Number(order?.order_detail?.order?.is_free) !== 1 || order?.order_detail?.order?.grand_total > 0) &&
                      <div className="checkout-form">
                        <div className="form-rows">
                          <div className="box1">{event?.labels?.EVENTSITE_BILLING_SUBTOTAL}</div>
                          <div className="box2">{order?.order_detail.sub_total_without_discount_display}</div>
                        </div>
                        {order?.order_detail?.order?.discount_amount > 0 && (
                          <div className="form-rows">
                            <div className="box1">{event?.labels?.EVENTSITE_BILLING_DISCOUNT}</div>
                            <div className="box2">{order?.order_detail.discount_amount}</div>
                          </div>
                        )}
                        {Number(order?.order_detail?.is_vat_applied) === 1 && (
                          Number(event?.payment_setting?.hotel_vat_status) === 1 && Number(event?.eventsite_setting?.payment_type) === 0 ? (
                            <div className="form-rows">
                              <div className="box1">{event?.labels?.EVENTSITE_BILLING_VAT}
                                {order.order_detail.hotel[0].vat_rate && (
                                  "(" + order.order_detail.hotel[0].vat_rate + ")"
                                )}
                              </div>
                              <div className="box2">{order?.order_detail.total_vat_amount_display}</div>
                            </div>
                          ) : (
                            order?.order_detail?.order?.item_level_vat === 0 && order?.order_detail?.order?.vat > 0 ? (
                              <div className="form-rows">
                                <div className="box1">{event?.labels?.EVENTSITE_BILLING_VAT} ({order?.order_detail?.order?.vat}%)</div>
                                <div className="box2">{order?.order_detail.total_vat_amount_display}</div>
                              </div>
                            ) : (
                              Object.keys(order?.order_detail?.display_vat_detail).map((vatKey: any) =>
                                <div className="form-rows" key={vatKey}>
                                  <div className="box1">{event?.labels?.EVENTSITE_BILLING_VAT} ({vatKey}%)</div>
                                  <div className="box2">{order?.order_detail?.display_vat_detail[vatKey]}</div>
                                </div>
                              )
                            )
                          )
                        )}
                      </div>
                    }
                    {in_array(provider, ["attendee", "embed"]) && Number(event?.event_disclaimer_setting?.reg_site) === 1 && tos?.purchase_policy && Number(event?.eventsite_setting?.payment_type) === 1 && order?.order_detail?.order?.grand_total > 0 && (
                      <div style={{ lineHeight: '20px', paddingTop: 2 }} className="accept-terms">
                        <span onClick={() => {
                          setTos({
                            ...tos,
                            active: tos?.active === true ? false : true
                          });
                        }} className={`btn_checbox ${tos?.active === true ? 'checked' : ''}`}>
                          <i className="material-icons">check</i>
                        </span>
                        <p>{tos?.inline_text}<a style={{ cursor: 'pointer' }} onClick={() => setPopup(true)}>{tos?.purchase_policy_link_text}</a></p>
                      </div>
                    )}
                    {(order?.order_detail?.order?.status !== "completed" || (order?.order_detail?.order?.status === "completed" && Number(order?.order_detail?.order?.is_waitinglist) === 0)) && (Number(event?.eventsite_setting?.payment_type) === 1 && Number(event?.payment_setting?.is_voucher) === 1) && (order?.order_detail?.order?.grand_total > 0 || order?.order_detail?.order?.code) && (
                      <div className="discount-voucher">
                        <header className="header-section">
                          <h3 style={{ cursor: 'auto' }}>{event?.labels?.REGISTRATION_FORM_HAVE_DISCOUNT_COUPON}</h3>
                        </header>
                        <div className={`voucher-form ${order?.order_detail?.order?.coupon_id && order?.order_detail?.order?.code && 'voucher-applied'}`}>
                          <Input
                            type="text"
                            onChange={handleVoucher}
                            field={`field-voucher_code`}
                            label={event?.labels?.REGISTRATION_FORM_ENTER_YOUR_COUPON_CODE}
                            value={voucher}
                            className={`${voucher && 'ebs-input-verified'}`}
                            required={false}
                            autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                          />
                          {action === 'submit-voucher' && <span className="ebs-clear-voucher bg-white ebs-spinner bg-white"><i className="material-icons">cached</i></span>}
                          {action !== 'submit-voucher' && order?.order_detail?.order?.coupon_id && order?.order_detail?.order?.code ? (
                            <span onClick={removeVoucher} className="ebs-clear-voucher bg-white"><i className="material-icons">close</i></span>
                          ) : ''}
                        </div>
                        {getKeyValue(errors, 'voucher_code') && <p className="error-message mt-2">{getKeyValue(errors, 'voucher_code')}</p>}
                      </div>
                    )}

                    {in_array(provider, ["sale"]) && sale_types?.length > 0 && (
                      <DropDown
                        label={event?.labels?.REGISTRATION_FORM_SALE_TYPE}
                        listitems={sale_types}
                        selected={sale_type}
                        selectedlabel={getSelectedLabel(sale_types, sale_type)}
                        onChange={handleChange('sale_type', 'select')}
                        placeholder={event?.labels?.REGISTRATION_FORM_SALE_TYPE}
                      />
                    )}

                    <div className="footer-bottom-area">

                      <a onClick={submitOrder} className={`btn btn-loader ${tos?.active === undefined || !tos?.active && 'disabled'}`}>
                        {action === "submit-order" ? (
                          <>
                            Loading...
                            <i className="material-icons ebs-spinner">autorenew</i>
                          </>
                        ) : (
                          <>
                            {
                              (() => {
                                if (in_array(provider, ["attendee", "embed"]))
                                  return (
                                    <>
                                      {order?.order_detail?.order?.grand_total > 0 && order?.order_detail?.order_billing_detail?.billing_company_type === "private" ? event?.labels?.REGISTRATION_FORM_REGISTER_AND_PAY : event?.labels?.REGISTRATION_FORM_CONFIRM_AND_REGISTER} <i className='material-icons'>keyboard_arrow_right</i>
                                    </>
                                  )
                                else
                                  return (
                                    <>
                                      Save order <i className='material-icons'>keyboard_arrow_right</i>
                                    </>
                                  )
                              })()
                            }
                          </>
                        )}
                      </a>

                      {getKeyValue(errors, 'order') && <p className="error-message mt-2">{getKeyValue(errors, 'order')}</p>}

                      {!order?.order_detail?.order_billing_detail?.billing_ean && order?.order_detail?.order?.grand_total > 0 && order?.order_detail?.order_billing_detail?.billing_company_type === "private" && (Number(order?.order_detail?.order?.is_waitinglist) === 0 || Number(waitinglist) === Number(order_id)) && (
                        <ul className="ebs-payment-cards-logo">
                          {event?.payment_cards?.map((card: any, key: any) =>
                            <React.Fragment key={key}>
                              <>
                                {card === 'DK' && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/dk.svg")} alt="" />
                                  </li>
                                )}
                              </>
                              <>
                                {card === 'VISA' && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/card_Visa_53x33.svg")} alt="" />
                                  </li>
                                )}
                              </>
                              <>
                                {(card === 'Master') && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/mastercard.svg")} alt="" />
                                  </li>
                                )}
                              </>
                              <>
                                {(card === 'Dinner_club') && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/diner-club.svg")} alt="" />
                                  </li>
                                )}
                              </>
                              <>
                                {(card === 'American_express') && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/american-express.svg")} alt="" />
                                  </li>
                                )}
                              </>
                              <>
                                {(card === 'Maestro') && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/maestro.svg")} alt="" />
                                  </li>
                                )}
                              </>
                              <>
                                {card === 'ELEC' && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/visa-electron.svg")} alt="" />
                                  </li>
                                )}
                              </>
                              <>
                                {card === 'JCB' && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/jsb.svg")} alt="" />
                                  </li>
                                )}
                              </>
                              <>
                                {card === 'FFK' && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/frf.svg")} alt="" />
                                  </li>
                                )}
                              </>
                              <>
                                {card === 'PayPal' && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/paypal_card.svg")} alt="" />
                                  </li>
                                )}
                              </>
                              <>
                                {card === 'MP' && (
                                  <li>
                                    <img src={require("@/src/img/payment-methods/ico-mp.svg")} alt="" />
                                  </li>
                                )}
                              </>
                            </React.Fragment>
                          )}
                        </ul>
                      )}

                    </div>
                  </div>}
                  <div className="top-checkout-header">
                    <div className="left-checkout">
                      {order?.order_detail?.order?.grand_total > 0 && (
                        <>
                          <div className="price">{order?.order_detail?.grand_total_display}</div>
                          <div className="total-vat">{Number(order?.order_detail?.is_vat_applied) === 1 ? event?.labels?.EVENTSITE_BILLING_PAYMENT_TOTAL : event?.labels?.EVENTSITE_BILLING_PAYMENT_EXC_TOTAL}</div>
                        </>
                      )}
                    </div>
                    <button onClick={() => setToggle(!toggle)} className={`${toggle && 'active'} btn text-truncate`}>{event?.labels?.REGISTRATION_FORM_CHECKOUT} <i className="material-icons">
                      {toggle ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}
                    </i></button>
                  </div>
                </div>
              )}

              {(order?.order_detail?.order?.status !== "completed" || (order?.order_detail?.order?.status === "completed" && Number(order?.order_detail?.order?.is_waitinglist) === 0)) && (!order?.order_detail?.order_billing_detail?.billing_company_type && Number(event?.eventsite_setting?.payment_type) === 1) && order?.order_detail?.order?.grand_total > 0 && (
                <div className="wrapper-box order-summry">
                  <Link className="btn-payment-info" to={`/${event.url}/${provider}/payment-information/${order_id}`}><i className="material-icons">add_circle</i>{event?.labels?.REGISTRATION_FORM_ADD_PAYMENT_INFORMATION}</Link>
                </div>
              )}

              {order?.order_detail?.order_billing_detail?.billing_company_type && (
                <div className="wrapper-box payment-information">
                  <header className="header-section">
                    <h3 style={{ cursor: 'auto' }}>{event?.labels?.REGISTRATION_FORM_PAYMENT_INFORMATION}
                      {(order?.order_detail?.order?.status !== "completed" || (order?.order_detail?.order?.status === "completed" && Number(order?.order_detail?.order?.is_waitinglist) === 0)) && (
                        <Link className="btn-payment-info" to={`/${event.url}/${provider}/payment-information/${order_id}`}><img src={require('@/src/img/ico-edit.svg')} alt="" /></Link>
                      )}
                    </h3>
                  </header>
                  <div className="row d-flex">
                    <div className="col-6">
                      <div className="row">
                        <div className="col-6">
                          <h5>{event?.labels?.REGISTRATION_FORM_CONTACT_PERSON}</h5>
                          <p>
                            {order?.order_detail?.order_billing_detail?.billing_contact_person_name} <br />
                            {order?.order_detail?.order_billing_detail?.billing_contact_person_email} <br />
                            {order?.order_detail?.order_billing_detail?.billing_contact_person_mobile_number}
                          </p>
                        </div>
                        <div className="col-6">
                          {width > 768 ?
                            <React.Fragment>
                              <h5>{event?.labels?.REGISTRATION_FORM_COMPANY_DETAILS}</h5>
                              <p>
                                {order?.order_detail?.order_main_attendee?.info?.company_name && (
                                  <>
                                    {order?.order_detail?.order_main_attendee?.info?.company_name}<br />
                                  </>
                                )}
                                {order?.order_detail?.order_billing_detail?.billing_company_registration_number && (
                                  <>
                                    {order?.order_detail?.order_billing_detail?.billing_company_registration_number}<br />
                                  </>
                                )}
                                {order?.order_detail?.order_billing_detail?.billing_company_street && (
                                  <>
                                    {order?.order_detail?.order_billing_detail?.billing_company_street}<br />
                                  </>
                                )}
                                {order?.order_detail?.order_billing_detail?.billing_company_house_number && (
                                  <>
                                    {order?.order_detail?.order_billing_detail?.billing_company_house_number}<br />
                                  </>
                                )}
                                {order?.order_detail?.order_billing_detail?.billing_company_post_code && (
                                  <>
                                    {order?.order_detail?.order_billing_detail?.billing_company_post_code}<br />
                                  </>
                                )}
                                {order?.order_detail?.order_billing_detail?.billing_company_city && (
                                  <>
                                    {order?.order_detail?.order_billing_detail?.billing_company_city}<br />
                                  </>
                                )}
                                {order?.order_detail?.order_billing_detail?.billing_company_country && (
                                  <>
                                    {order?.order_detail?.order_billing_detail?.billing_company_country}<br />
                                  </>
                                )}
                              </p>
                            </React.Fragment>
                            : (
                              <React.Fragment>
                                {Number(event?.eventsite_setting?.payment_type) === 1 && (
                                  <>
                                    <h5>{event?.labels?.REGISTRATION_FORM_PAYMENT_INFORMATION}</h5>
                                    <p>
                                      {order?.order_detail?.order_billing_detail?.billing_company_type && (
                                        <>
                                          {event?.labels?.REGISTRATION_FORM_PAYMENT_METHOD} {order?.order_detail?.order_billing_detail?.billing_company_type}<br />
                                        </>
                                      )}
                                      {order?.order_detail?.order_billing_detail?.billing_ean && (
                                        <>
                                          {event?.labels?.REGISTRATION_FORM_EAN_METHOD} {order?.order_detail?.order_billing_detail?.billing_ean}<br />
                                        </>
                                      )}
                                    </p>
                                  </>
                                )}
                              </React.Fragment>
                            )}
                        </div>
                      </div>
                    </div>
                    <div className="col-6">
                      {width < 768 ?
                        <React.Fragment>
                          <h5>{event?.labels?.REGISTRATION_FORM_COMPANY_DETAILS}</h5>
                          <p>
                            {order?.order_detail?.order_main_attendee?.info?.company_name && (
                              <>
                                {order?.order_detail?.order_main_attendee?.info?.company_name}<br />
                              </>
                            )}
                            {order?.order_detail?.order_billing_detail?.billing_company_registration_number && (
                              <>
                                {order?.order_detail?.order_billing_detail?.billing_company_registration_number}<br />
                              </>
                            )}
                            {order?.order_detail?.order_billing_detail?.billing_company_street && (
                              <>
                                {order?.order_detail?.order_billing_detail?.billing_company_street}<br />
                              </>
                            )}
                            {order?.order_detail?.order_billing_detail?.billing_company_house_number && (
                              <>
                                {order?.order_detail?.order_billing_detail?.billing_company_house_number}<br />
                              </>
                            )}
                            {order?.order_detail?.order_billing_detail?.billing_company_post_code && (
                              <>
                                {order?.order_detail?.order_billing_detail?.billing_company_post_code}<br />
                              </>
                            )}
                            {order?.order_detail?.order_billing_detail?.billing_company_city && (
                              <>
                                {order?.order_detail?.order_billing_detail?.billing_company_city}<br />
                              </>
                            )}
                            {order?.order_detail?.order_billing_detail?.billing_company_country && (
                              <>
                                {order?.order_detail?.order_billing_detail?.billing_company_country}<br />
                              </>
                            )}
                          </p>
                        </React.Fragment>
                        : (
                          <React.Fragment>
                            {Number(event?.eventsite_setting?.payment_type) === 1 && (
                              <>
                                <h5>{event?.labels?.REGISTRATION_FORM_PAYMENT_INFORMATION}</h5>
                                <p>
                                  {order?.order_detail?.order_billing_detail?.billing_company_type && (
                                    <>
                                      {event?.labels?.REGISTRATION_FORM_PAYMENT_METHOD} {order?.order_detail?.order_billing_detail?.billing_company_type}<br />
                                    </>
                                  )}
                                  {order?.order_detail?.order_billing_detail?.billing_ean && (
                                    <>
                                      {event?.labels?.REGISTRATION_FORM_EAN_METHOD} {order?.order_detail?.order_billing_detail?.billing_ean}<br />
                                    </>
                                  )}
                                </p>
                              </>
                            )}
                          </React.Fragment>
                        )}
                    </div>
                  </div>
                </div>
              )}

              {(order?.order_detail?.order_summary_detail?.group_addons?.length > 0 || order?.order_detail?.order_summary_detail.single_addons?.length > 0) && (
                <div className="wrapper-box order-summry-table">
                  <header className="header-section">
                    <h3 style={{ cursor: 'auto' }}>{event?.labels?.REGISTRATION_FORM_YOUR_ORDER}</h3>
                  </header>
                  <div className="order-table-wrapper">
                    {width > 768 &&
                      <div className="order-row table-header">
                        <div className="data-description"></div>
                        <div className="data-price">
                          <strong>{event?.labels?.EVENTSITE_BILLING_QTY}</strong>
                        </div>
                        {Number(order?.order_detail?.order?.is_free) !== 1 && (
                          <React.Fragment>
                            <div className="data-price">
                              <strong>{event?.labels?.EVENTSITE_BILLING_PRICE}</strong>
                            </div>
                            <div className="data-price">
                              <strong>{event?.labels?.EVENTSITE_BILLING_SUBTOTAL}</strong>
                            </div>
                            {(Number(order?.order_detail?.order?.is_voucher) === 1 || Number(order?.order_detail?.order_summary_detail?.show_display_discount_col) === 1) && (
                              <div className="data-price">
                                <strong>{event?.labels?.EVENTSITE_BILLING_DISCOUNT}</strong>
                              </div>
                            )}
                            {Number(order?.order_detail?.show_display_discount_qty_col) === 1 && (
                              <div className="data-price">
                                <strong>{event?.labels?.EVENTSITE_BILLING_DISCOUNT_QTY}</strong>
                              </div>
                            )}
                            <div className="data-price">
                              <strong>{event?.labels?.REGISTRATION_FORM_TOTAL}</strong>
                            </div>
                          </React.Fragment>
                        )}
                      </div>
                    }

                    {order?.order_detail?.order_summary_detail?.group_addons?.map((group: any, groupKey: any) =>
                      <div className="data-with-inner" key={groupKey}>
                        <div className="order-row">
                          <div className="data-description">
                            <h5>{group.group_name}</h5>
                          </div>
                        </div>
                        <div className="inner-data-table">
                          <h4>{event?.labels?.REGISTRATION_FORM_TYPES}</h4>
                          {Object.keys(group?.addons)?.map((itemKey: any) =>
                            <>
                              <div className="order-row" key={itemKey}>
                                <div className="data-description">
                                  <h5>{group?.addons[itemKey]?.name}</h5>
                                  <p dangerouslySetInnerHTML={{ __html: group?.addons[itemKey]?.description }}></p>
                                </div>
                                <div className="data-price">
                                  {width <= 768 && <strong className="title">{event?.labels?.EVENTSITE_BILLING_QTY}</strong>}
                                  <div className="price-item">{group?.addons[itemKey]?.qty}</div>
                                </div>
                                {Number(order?.order_detail?.order?.is_free) !== 1 && (
                                  <React.Fragment>
                                    <div className="data-price">
                                      {width <= 768 && <strong className="title">{event?.labels?.EVENTSITE_BILLING_PRICE}</strong>}
                                      <div className="price-item">{group?.addons[itemKey]?.price_display}</div>
                                    </div>
                                    <div className="data-price">
                                      {width <= 768 && <strong className="title">{event?.labels?.EVENTSITE_BILLING_SUBTOTAL}</strong>}
                                      <div className="price-item">{group?.addons[itemKey]?.subtotal_display}</div>
                                    </div>
                                    {(Number(order?.order_detail?.order?.is_voucher) === 1 || Number(order?.order_detail?.order_summary_detail?.show_display_discount_col) === 1) && (
                                      <div className="data-price">
                                        {width <= 768 && <strong className="title">{event?.labels?.EVENTSITE_BILLING_DISCOUNT}</strong>}
                                        <div className="price-item">{group?.addons[itemKey]?.discount > 0 ? '-' + group?.addons[itemKey]?.discount_display : ''}</div>
                                      </div>
                                    )}
                                    {Number(order?.order_detail?.show_display_discount_qty_col) === 1 && (
                                      <div className="data-price">
                                        {width <= 768 && <strong className="title">{event?.labels?.EVENTSITE_BILLING_DISCOUNT_QTY}</strong>}
                                        <div className="price-item">{group?.addons[itemKey]?.quantity_discount > 0 ? '-' + group?.addons[itemKey]?.quantity_discount_display : ''}</div>
                                      </div>
                                    )}
                                    <div className="data-price data-price-total">
                                      {width <= 768 && <strong className="title">{event?.labels?.REGISTRATION_FORM_TOTAL}</strong>}
                                      <div className="price-item">{group?.addons[itemKey]?.grand_total_display}</div>
                                    </div>
                                  </React.Fragment>
                                )}
                              </div>
                              {Object.keys(group?.addons[itemKey]?.link_data) && Object.keys(group?.addons[itemKey]?.link_data).length > 0 && (
                                <div style={{ paddingLeft: 0 }} className="wrapper-date-list">
                                  <div className="btn-track-detail"><span>{event?.labels?.REGISTRATION_FORM_TRACK_DETAIL}<i className="material-icons">keyboard_arrow_down</i></span> </div>
                                  {Object.keys(group?.addons[itemKey]?.link_data).map((date: any, i: any) =>
                                    <div key={i} className="datelist-wrapper">
                                      <h5>{date}</h5>
                                      {group?.addons[itemKey]?.link_data[date] && group?.addons[itemKey]?.link_data[date]?.length > 0 && (
                                        <>
                                          {group?.addons[itemKey]?.link_data[date]?.map((itm: any, c: any) =>
                                            <div style={{ paddingLeft: 30 }} key={c} className="datelist-section">
                                              {itm.topic && <h6>{itm.topic}</h6>}
                                              <p>{`${itm.start_time} - ${itm.end_time}`}, {itm.location}</p>
                                            </div>
                                          )}
                                        </>
                                      )}
                                    </div>
                                  )}
                                </div>
                              )}
                            </>
                          )}
                        </div>
                      </div>
                    )}

                    {order.order_detail && order?.order_detail?.order_summary_detail && order?.order_detail?.order_summary_detail.single_addons.map((item: any, itemKey: any) =>
                      <>
                        <div className="order-row" key={itemKey}>
                          <div className="data-description">
                            <h5>{item.name}</h5>
                            <p dangerouslySetInnerHTML={{ __html: item.description }}></p>
                          </div>
                          <div className="data-price">
                            {width <= 768 && <strong className="title">{event?.labels?.EVENTSITE_BILLING_QTY}</strong>}
                            <div className="price-item">{item.qty}</div>
                          </div>
                          {Number(order?.order_detail?.order?.is_free) !== 1 && (
                            <React.Fragment>
                              <div className="data-price">
                                {width <= 768 && <strong className="title">{event?.labels?.EVENTSITE_BILLING_PRICE}</strong>}
                                <div className="price-item">{item.price_display}</div>
                              </div>
                              <div className="data-price">
                                {width <= 768 && <strong className="title">{event?.labels?.EVENTSITE_BILLING_SUBTOTAL}</strong>}
                                <div className="price-item">{item.subtotal_display}</div>
                              </div>
                              {(Number(order?.order_detail?.order?.is_voucher) === 1 || Number(order?.order_detail?.order_summary_detail?.show_display_discount_col) === 1) && (
                                <div className="data-price">
                                  {width <= 768 && <strong className="title">{event?.labels?.EVENTSITE_BILLING_DISCOUNT}</strong>}
                                  <div className="price-item">{item.discount > 0 ? '-' + item.discount_display : ''}</div>
                                </div>
                              )}
                              {Number(order?.order_detail?.show_display_discount_qty_col) === 1 && (
                                <div className="data-price">
                                  {width <= 768 && <strong className="title">{event?.labels?.EVENTSITE_BILLING_DISCOUNT_QTY}</strong>}
                                  <div className="price-item">{item.quantity_discount > 0 ? '-' + item.quantity_discount_display : ''}</div>
                                </div>
                              )}
                              <div className="data-price data-price-total">
                                {width <= 768 && <strong className="title">{event?.labels?.REGISTRATION_FORM_TOTAL}</strong>}
                                <div className="price-item">{item.grand_total_display}</div>
                              </div>
                            </React.Fragment>
                          )}
                        </div>
                        {Object.keys(item.link_data) && Object.keys(item.link_data).length > 0 && (
                          <div style={{ paddingLeft: 0 }} className="wrapper-date-list">
                            <div className="btn-track-detail"><span>{event?.labels?.REGISTRATION_FORM_TRACK_DETAIL}<i className="material-icons">keyboard_arrow_down</i></span> </div>
                            {Object.keys(item.link_data).map((date: any, i: any) =>
                              <div key={i} className="datelist-wrapper">
                                <h5>{date}</h5>
                                {item?.link_data[date] && item?.link_data[date]?.length > 0 && (
                                  <>
                                    {item?.link_data[date]?.map((itm: any, c: any) =>
                                      <div style={{ paddingLeft: 30 }} key={c} className="datelist-section">
                                        {itm.topic && <h6>{itm.topic}</h6>}
                                        <p>{`${itm.start_time} - ${itm.end_time}`}, {itm.location}</p>
                                      </div>
                                    )}
                                  </>
                                )}
                              </div>
                            )}
                          </div>
                        )}
                      </>
                    )}
                  </div>
                </div>
              )}
              {order.order_detail.attendee_summary_detail.filter((attendee: any, key: any) => (attendee.documents.length > 0)).length > 0 ? (
                <div className="wrapper-box document-listing-section">
                  <header className="header-section">
                    <h3 style={{ cursor: 'auto' }}>{event?.labels?.REQUIRED_DOCUMENTS_SUMMARY_DOCUMENT !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_SUMMARY_DOCUMENT : 'Documents'}</h3>
                  </header>
                  <div className="ebs-document-listing">
                    <div className="ebs-document-row d-flex ebs-header-documents">
                      <div className="ebs-document-box">
                        <strong>{event?.labels?.REQUIRED_DOCUMENTS_SUMMARY_DOCUMENT_NAME !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_SUMMARY_DOCUMENT_NAME : 'Document name'}</strong>

                      </div>
                      <div className="ebs-document-box text-center">
                        <strong>{event?.labels?.REQUIRED_DOCUMENTS_SUMMARY_DOCUMENT_TYPE !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_SUMMARY_DOCUMENT_TYPE : 'Document type'}</strong>
                      </div>
                      <div className="ebs-document-box text-center">
                        <strong>{event?.labels?.REQUIRED_DOCUMENTS_SUMMARY_DOCUMENT_ATTENDEE !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_SUMMARY_DOCUMENT_ATTENDEE : 'Attendee'}</strong>
                      </div>
                      <div className="ebs-document-box text-center">
                        <strong>{event?.labels?.REQUIRED_DOCUMENTS_SUMMARY_DOCUMENT_DATE !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_SUMMARY_DOCUMENT_DATE : 'Document date'}</strong>
                      </div>
                    </div>
                    {order.order_detail.attendee_summary_detail.map((attendee: any, attendeeKey: any) => {
                      return attendee.documents.map((doc: any) => (
                        <div key={doc.id} className="ebs-document-row d-flex align-items-center">
                          <div className="ebs-document-box w-25">
                            <div className="ebs-document-ext d-flex align-items-center">
                              <div className="ebs-ico-ext">
                                <ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-file.svg')} />
                                <span className='ebs-ext'>{doc.type}</span>
                              </div>
                              <p className="w-100">
                                {in_array(provider, ['admin', 'sale']) ? <a href={doc.s3 === 1 ? doc.s3_url : `${process.env.REACT_APP_EVENTCENTER_URL}/assets/documents/clients/${doc.path}`} download target="_blank" rel="noreferrer">
                                  {doc.name}
                                </a> :
                                  doc.name
                                }
                              </p>
                            </div>
                          </div>
                          <div className="ebs-document-box text-center w-25">
                            {doc.types.map((type: any, i: any) => (
                              <span key={type.value} className="ebs-doc-type mr-2">
                                {type.label}
                              </span>
                            ))}
                          </div>
                          <div className="ebs-document-box text-center w-25">
                            <p>{`${attendee.attendee_info.first_name} ${attendee.attendee_info.last_name}`}</p>
                          </div>
                          <div className="ebs-document-box text-center w-25">
                            <p>{moment(doc.created_at).tz(event.timezone.timezone).format('DD MM, YYYY HH:mm')}</p>
                          </div>
                        </div>
                      ))
                    })}
                  </div>
                </div>
              ) : ''}
            </div>
            {width > 768 && (
              <div className="col-3">
                <div className="wrapper-box checkout-sidebar">
                  <header className="header-section">
                    <h3 style={{ cursor: 'auto' }}>{event?.labels?.REGISTRATION_FORM_CHECKOUT}</h3>
                  </header>
                  {(Number(order?.order_detail?.order?.is_free) !== 1 || order?.order_detail?.order?.grand_total > 0) ?
                    <React.Fragment>
                      <div className="checkout-form">
                        <div className="form-rows">
                          <div className="box1">{event?.labels?.EVENTSITE_BILLING_SUBTOTAL}</div>
                          <div className="box2">{order?.order_detail.sub_total_without_discount_display}</div>
                        </div>
                        {order?.order_detail?.order?.discount_amount > 0 && (
                          <div className="form-rows">
                            <div className="box1">{event?.labels?.EVENTSITE_BILLING_DISCOUNT}</div>
                            <div className="box2">{order?.order_detail.discount_amount}</div>
                          </div>
                        )}
                        {Number(order?.order_detail?.is_vat_applied) === 1 && (
                          Number(event?.payment_setting?.hotel_vat_status) === 1 && Number(event?.eventsite_setting?.payment_type) === 0 ? (
                            <div className="form-rows">
                              <div className="box1">{event?.labels?.EVENTSITE_BILLING_VAT}
                                {order.order_detail.hotel[0].vat_rate && (
                                  "(" + order.order_detail.hotel[0].vat_rate + ")"
                                )}</div>
                              <div className="box2">{order?.order_detail.total_vat_amount_display}</div>
                            </div>
                          ) : (
                            order?.order_detail?.order?.item_level_vat === 0 && order?.order_detail?.order?.vat > 0 ? (
                              <div className="form-rows">
                                <div className="box1">{event?.labels?.EVENTSITE_BILLING_VAT} ({order?.order_detail?.order?.vat}%)</div>
                                <div className="box2">{order?.order_detail.total_vat_amount_display}</div>
                              </div>
                            ) : (
                              Object.keys(order?.order_detail?.display_vat_detail).map((vatKey: any) =>
                                <div className="form-rows" key={vatKey}>
                                  <div className="box1">{event?.labels?.EVENTSITE_BILLING_VAT} ({vatKey}%)</div>
                                  <div className="box2">{order?.order_detail?.display_vat_detail[vatKey]}</div>
                                </div>
                              )
                            )
                          )
                        )}
                        <div className="form-rows total-row">
                          <div className="box1">{order?.order_detail?.grand_total_display}</div>
                          <div className="box2">{Number(order?.order_detail?.is_vat_applied) === 1 ? event?.labels?.EVENTSITE_BILLING_PAYMENT_TOTAL : event?.labels?.EVENTSITE_BILLING_PAYMENT_EXC_TOTAL}</div>
                        </div>
                      </div>
                    </React.Fragment>
                    : (
                      <br></br>
                    )}
                  {in_array(provider, ["attendee", "embed"]) && Number(event?.event_disclaimer_setting?.reg_site) === 1 && tos?.purchase_policy && Number(event?.eventsite_setting?.payment_type) === 1 && order?.order_detail?.order?.grand_total > 0 && (
                    <div style={{ lineHeight: '20px', paddingTop: 2 }} className="accept-terms">
                      <span onClick={() => {
                        setTos({
                          ...tos,
                          active: tos?.active === true ? false : true
                        });
                      }} className={`btn_checbox ${tos?.active === true ? 'checked' : ''}`}>
                        <i className="material-icons">check</i>
                      </span>
                      <p>{tos?.inline_text}<a style={{ cursor: 'pointer' }} onClick={() => setPopup(true)}>{tos?.purchase_policy_link_text}</a></p>
                    </div>
                  )}

                  {(order?.order_detail?.order?.status !== "completed" || (order?.order_detail?.order?.status === "completed" && Number(order?.order_detail?.order?.is_waitinglist) === 0)) && (Number(event?.eventsite_setting?.payment_type) === 1 && Number(event?.payment_setting?.is_voucher) === 1) && (order?.order_detail?.order?.grand_total > 0 || order?.order_detail?.order?.code) && (
                    <div className="discount-voucher">
                      <header className="header-section">
                        <h3 style={{ cursor: 'auto' }}>{event?.labels?.REGISTRATION_FORM_HAVE_DISCOUNT_COUPON}</h3>
                      </header>
                      <div className={`voucher-form mb-3 ${order?.order_detail?.order?.coupon_id && order?.order_detail?.order?.code && 'voucher-applied'}`}>
                        <Input
                          type="text"
                          onChange={handleVoucher}
                          field={`field-voucher_code`}
                          label={event?.labels?.REGISTRATION_FORM_ENTER_YOUR_COUPON_CODE}
                          value={voucher}
                          required={false}
                          className={`${voucher && 'ebs-input-verified'}`}
                          autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                        />
                        {action === 'submit-voucher' && <span className="ebs-clear-voucher bg-white ebs-spinner bg-white"><i className="material-icons">cached</i></span>}
                        {action !== 'submit-voucher' && order?.order_detail?.order?.coupon_id && order?.order_detail?.order?.code ? (
                          <span onClick={removeVoucher} className="ebs-clear-voucher bg-white"><i className="material-icons">close</i></span>
                        ) : ''}
                      </div>
                      {getKeyValue(errors, 'voucher_code') && <p className="error-message w-100 mt-2">{getKeyValue(errors, 'voucher_code')}</p>}
                    </div>
                  )}

                  {in_array(provider, ["sale"]) && sale_types?.length > 0 && (
                    <DropDown
                      label={event?.labels?.REGISTRATION_FORM_SALE_TYPE}
                      listitems={sale_types}
                      selected={sale_type}
                      selectedlabel={getSelectedLabel(sale_types, sale_type)}
                      onChange={handleChange('sale_type', 'select')}
                      placeholder={event?.labels?.REGISTRATION_FORM_SALE_TYPE}
                    />
                  )}

                  <div className="footer-bottom-area">

                    <a className={`btn btn-loader ${tos?.active === undefined || !tos?.active && 'disabled'}`} onClick={submitOrder}>
                      {action === "submit-order" ? (
                        <>
                          Loading...
                          <i className="material-icons ebs-spinner">autorenew</i>
                        </>
                      ) : (
                        <>
                          {
                            (() => {
                              if (in_array(provider, ["attendee", "embed"]))
                                return (
                                  <>
                                    {order?.order_detail?.order?.grand_total > 0 && order?.order_detail?.order_billing_detail?.billing_company_type === "private" ? event?.labels?.REGISTRATION_FORM_REGISTER_AND_PAY : event?.labels?.REGISTRATION_FORM_CONFIRM_AND_REGISTER} <i className='material-icons'>keyboard_arrow_right</i>
                                  </>
                                )
                              else
                                return (
                                  <>
                                    Save order <i className='material-icons'>keyboard_arrow_right</i>
                                  </>
                                )
                            })()
                          }
                        </>
                      )}
                    </a>

                    {success_message && <p className="success-message mt-2">{success_message}</p>}

                    {getKeyValue(errors, 'order') && <p className="error-message w-100 mt-2">{getKeyValue(errors, 'order')}</p>}

                    {!order?.order_detail?.order_billing_detail?.billing_ean && order?.order_detail?.order?.grand_total > 0 && order?.order_detail?.order_billing_detail?.billing_company_type === "private" && (Number(order?.order_detail?.order?.is_waitinglist) === 0 || Number(waitinglist) === Number(order_id)) && (
                      <ul className="ebs-payment-cards-logo">
                        {event?.payment_cards?.map((card: any, key: any) =>
                          <React.Fragment key={key}>
                            <>
                              {card === 'DK' && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/dk.svg")} alt="" />
                                </li>
                              )}
                            </>
                            <>
                              {card === 'VISA' && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/card_Visa_53x33.svg")} alt="" />
                                </li>
                              )}
                            </>
                            <>
                              {(card === 'Master') && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/mastercard.svg")} alt="" />
                                </li>
                              )}
                            </>
                            <>
                              {(card === 'Dinner_club') && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/diner-club.svg")} alt="" />
                                </li>
                              )}
                            </>
                            <>
                              {(card === 'American_express') && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/american-express.svg")} alt="" />
                                </li>
                              )}
                            </>
                            <>
                              {(card === 'Maestro') && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/maestro.svg")} alt="" />
                                </li>
                              )}
                            </>
                            <>
                              {card === 'ELEC' && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/visa-electron.svg")} alt="" />
                                </li>
                              )}
                            </>
                            <>
                              {card === 'JCB' && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/jsb.svg")} alt="" />
                                </li>
                              )}
                            </>
                            <>
                              {card === 'FFK' && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/frf.svg")} alt="" />
                                </li>
                              )}
                            </>
                            <>
                              {card === 'PayPal' && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/paypal_card.svg")} alt="" />
                                </li>
                              )}
                            </>
                            <>
                              {card === 'MP' && (
                                <li>
                                  <img src={require("@/src/img/payment-methods/ico-mp.svg")} alt="" />
                                </li>
                              )}
                            </>
                          </React.Fragment>
                        )}
                      </ul>
                    )}
                  </div>
                </div>
              </div>
            )}
          </React.Fragment>
        )}
      </div>

      {popup &&
        <Popup
          onClick={() => setPopup(false)}
          width="800px"
          title={event?.labels?.EVENTSITE_TERMANDCONDITIONS}>
          <div className="ebs-popup-content">
            <Scrollbars
              renderThumbVertical={props => <div {...props} className="thumb-horizontal" />}
              autoHeight
              autoHeightMin={400}
              autoHeightMax={400}>
              <div style={{ paddingRight: 30 }} dangerouslySetInnerHTML={{ __html: tos?.purchase_policy }}></div>
            </Scrollbars>
            <div className="ebs-popup-buttons text-center">
              <div className="btn bordered" onClick={() => {
                setPopup(false);
              }}>{event?.labels?.GENERAL_CANCEL}</div>
              <div className="btn btn-primary" onClick={() => {
                setPopup(false);
                setTos({
                  ...tos,
                  active: true
                });
              }}>{event?.labels?.GENERAL_ACCEPT}</div>
            </div>
          </div>
        </Popup>
      }

      {bamboraPayment &&
        <Popup
          onClick={() => setBamboraPaymentUrl("")}
          title="Bambora payments"
          width="80%"
        >
          <div className="ebs-popup-content">
            <div className="ebs-popup-buttons text-center">
              <div className="iframe">
                <iframe style={{ height: '75vh', border: 'none' }} title='iframe' src={bamboraPayment} width="100%"></iframe>
              </div>
            </div>
          </div>
        </Popup>
      }

      {convergePayment &&
        <Popup
          onClick={() => setConvergePaymentUrl("")}
          title="Elavon payments"
          width="80%"
        >
          <iframe style={{ height: '75vh', border: 'none' }} title='iframe' src={convergePayment} width="100%"></iframe>
        </Popup>
      }

      {netsPayment &&
        <Popup
          onClick={() => setNetsPaymentUrl("")}
          title="Nets payment"
          width="80%"
        >
          <div className="ebs-popup-content">
            <div className="ebs-popup-buttons text-center">
              <div className="iframe">
                <iframe style={{ height: '75vh', border: 'none' }} title='iframe' src={netsPayment} width="100%"></iframe>
              </div>
            </div>
          </div>
        </Popup>
      }

      <Toaster />

    </React.Fragment>
  );
};

export default OrderSummary;