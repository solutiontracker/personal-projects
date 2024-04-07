import React, { ReactElement, FC, useState, useEffect, useContext, useRef } from "react";
import { useParams, useHistory, Link } from 'react-router-dom';
import DateRange from '@/src/app/components/forms/DateRange';
import DropDown from '@/src/app/components/forms/DropDown';
import { service } from '@/src/app/services/service';
import { EventContext } from "@/src//app/context/event/EventProvider";
import Loader from '@/src/app/components/forms/Loader';
import moment from 'moment';
import Select from 'react-select';
import SimpleReactValidator from 'simple-react-validator';
import { ReactSVG } from "react-svg";
import toast, { Toaster } from 'react-hot-toast';
import in_array from "in_array";
import { confirmAlert } from 'react-confirm-alert';
import { getLanguageCode, postMessage } from '@/src/app/helpers';
import 'react-confirm-alert/src/react-confirm-alert.css';

type Params = {
  url: any;
  provider: any;
  order_id: any;
  attendee_id: any;
};

const ManageHotelBooking: FC<any> = (props: any): ReactElement => {

  const roomsArray = [{ "id": "1", "name": "1" }, { "id": "2", "name": "2" }, { "id": "3", "name": "3" }, { "id": "4", "name": "4" }, { "id": "5", "name": "5" }, { "id": "6", "name": "6" }, { "id": "7", "name": "7" }, { "id": "8", "name": "8" }, { "id": "9", "name": "9" }, { "id": "10", "name": "10" }, { "id": "11", "name": "11" }, { "id": "12", "name": "12" }, { "id": "13", "name": "13" }, { "id": "14", "name": "14" }, { "id": "15", "name": "15" }, { "id": "16", "name": "16" }, { "id": "17", "name": "17" }, { "id": "18", "name": "18" }, { "id": "19", "name": "19" }, { "id": "20", "name": "20" }];

  const [data, setData] = useState<any>({});

  const [width, setWidth] = useState(0);

  const { event, updateEvent, updateRouteParams, updateFormBuilderForms, formBuilderForms } = useContext<any>(EventContext);

  const { order_id, attendee_id, provider } = useParams<Params>();

  const [hotels, setHotels] = useState<any>([]);

  const [nights, setNights] = useState<any>(0);

  const [loading, setLoading] = useState(true);

  const [action, setAction] = useState("");

  const [count, setCount] = useState(0);

  const [errors, setErrors] = useState<any>({});

  const [form_settings, setFormSettings] = useState<any>({});

  const [checkin, setCheckIn] = useState('');

  const [checkout, setCheckout] = useState('');

  const [rooms, setRooms] = useState(roomsArray);

  const [room, setRoom] = useState(1);

  const [total_hotels, setTotalHotels] = useState(1);

  const [, forceUpdate] = useState(0);

  const history = useHistory();

  const mounted = useRef(false);

  const params = useParams<Params>();

  const simpleValidator = useRef(new SimpleReactValidator({
    element: (message: any) => <p style={{ maxWidth: '300%', marginTop: '3px', color: '#fff' }} className="error-message">{message}</p>,
    messages: {
      required: event?.labels?.REGISTRATION_FORM_FIELD_REQUIRED
    },
    autoForceUpdate: { forceUpdate: () => forceUpdate(1) }
  }))

  function updateWindowDimensions() {
    setWidth(window.innerWidth);
  }

  useEffect(() => {
    mounted.current = true;
    postMessage({ page: 'manage-hotels' });
    return () => { mounted.current = false; };
  }, []);

  useEffect(() => {
    updateRouteParams({ ...params, page: 'hotel-booking', orderAttendee: data?.order_attendee, form_settings: form_settings });
  }, [data?.order_attendee, form_settings]);

  useEffect(() => {
    updateWindowDimensions();
    window.addEventListener('resize', updateWindowDimensions);
    return () => {
      window.removeEventListener('resize', updateWindowDimensions);
    };
  }, []);

  useEffect(() => {
    loadSummary(event, order_id);
  }, []);

  function loadSummary(event: any, order_id: any) {
    setLoading(true);
    service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/hotels/${order_id}/${attendee_id}?provider=${provider}`)
      .then(
        response => {
          if (response.success && mounted.current) {
            if (response?.data?.order?.status !== "completed" || (response?.data?.order?.status === "completed" && (Number(response?.data?.order?.is_waitinglist) === 1 || in_array(provider, ['sale', 'admin'])))) {
              if (Number(response?.data?.form_settings.show_hotels) === 1) {
                setData(response.data);
                setTotalHotels(response.data.hotels)
                setFormSettings(response?.data?.form_settings);
                setLoading(false);
                //Update event info
                updateFormBuilderForms({
                  forms: [...response?.data?.form_builder_forms
                  ], attendee_id, order_id
                });
                //Update event info
                updateEvent({
                  ...event,
                  order: response?.data?.order
                });
              } else {
                if (formBuilderForms.length > 0) {
                  history.push(`/${event.url}/${provider}/custom-forms/${order_id}/${attendee_id}/${formBuilderForms[0].id}`);
                } else {
                  completedAttendeeIteration(attendee_id);
                }
              }
            } else {
              history.push(`/${event.url}/${provider}/manage-attendee`);
            }
          }
        },
        error => { }
      );
  }


  const handleSearch = (evt: any) => {
    evt.preventDefault();
    setAction("search");
    if (event.url !== undefined) {
      setHotels([]);
      service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/search-hotels/${order_id}/${attendee_id}`, { checkin: checkin, checkout: checkout, room: room, provider: provider })
        .then(
          response => {
            if (mounted.current) {
              if (response.success) {
                setHotels(response.data.hotels);
              } else {
                toast.error(response.message, {
                  position: "bottom-center"
                });
              }
              setAction("");
            }

            //Clear validation
            simpleValidator.current.purgeFields();
          },
          error => {
            setAction("");
          }
        );
    }
  }

  const handleSubmit = (action: any) => {
    const formValid = simpleValidator.current.allValid()
    if (!formValid) {
      simpleValidator.current.showMessages();
       setTimeout(() => {
        const scrollTo = document?.getElementsByClassName('error-message')[0];
        if (scrollTo !== undefined && scrollTo !== null) {
          scrollTo.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        }
      }, 500);
    } else {
      if (hotels.filter((hotel: any, key: any) => (Number(hotel.checked) === 1)).length === 0 && (Number(form_settings.hotel_mandatory_registration_site) === 1 || hotels?.length > 0)) {
        toast.error(event?.labels?.REGISTRATION_FORM_SELECT_AT_LEAST_ONE_HOTEL, {
          position: "bottom-center"
        })
      } else {
        setAction(action);
        service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/save-hotels/${order_id}/${attendee_id}`, { hotels: hotels, provider: provider })
          .then(
            response => {
              if (mounted.current) {
                if (response.success) {
                  if (action === "save-next") {
                    if (formBuilderForms.length > 0) {
                      history.push(`/${event.url}/${provider}/custom-forms/${order_id}/${attendee_id}/${formBuilderForms[0].id}`);
                    } else {
                      completedAttendeeIteration(attendee_id);
                    }
                  } else {
                    setCheckIn('');
                    setCheckout('');
                    setHotels([]);
                    loadSummary(event, order_id);
                  }
                } else {
                  if (response?.message !== undefined && response?.message) {
                    toast.error(response.message, {
                      position: "bottom-center"
                    });
                  } else {
                    setErrors(response.errors);
                  }
                  setAction("");
                }
              }
            },
            error => {
              setAction("");
            }
          );
      }
    }
  }

  const handleDateChange = (input: any, value: any) => {
    if (value !== undefined) {
      const date = value && value !== 'Invalid date' && value !== 'cleardate' ? moment(new Date(value)).format('YYYY-MM-DD') : '';
      if (input === "from") {
        setCheckIn(date);
      } else if (input === "to") {
        setCheckout(date);
      }
    }
  };

  const handleHotelSelection = (key: any) => () => {
    hotels[key]['checked'] = Number(hotels[key]['checked']) === 1 ? 0 : 1;
    setHotels(hotels);
    setCount(count + 1);

    //Clear validation
    simpleValidator.current.purgeFields();
  };

  const attachAttendee = (key: any, room: any) => (e: any) => {
    hotels[key]['hotel_person_id'] = [...new Set([...hotels[key]['hotel_person_id'], e.value])];
    hotels[key]['hotel_person_room_' + room] = e.value;
    setHotels(hotels);
    setCount(count + 1);
  };

  const getSelectedLabel = (item: any, id: any) => {
    if (item && item.length > 0 && id) {
      const obj = item.find((o: any) => o.id.toString() === id.toString());
      return (obj ? (obj.name ? obj.name : obj.label) : '');
    }
  }

  const style = {
    control: (base: any) => ({
      ...base,
      boxShadow: 'none',
      width: 300
    })
  };

  const handleClick = (e: any) => {
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

  const completedAttendeeIteration = (attendee_id: any) => {
    service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/completed-attendee-iteration`, { attendee_id: attendee_id, order_id: order_id, provider: provider })
      .then(
        response => {
          if (mounted.current) {
            if (response.success) {
              history.push(`/${event.url}/${provider}/order-summary/${order_id}`);
            } else {
              setErrors(response.errors);
            }
            setLoading(false);
          }
        },
        error => {
          setLoading(false);
        }
      );
  }

  useEffect(() => {
    if (checkin && checkin !== 'Invalid date' && checkout && checkout !== 'Invalid date') {
      const start = moment(checkin, "YYYY-MM-DD");
      const end = moment(checkout, "YYYY-MM-DD");
      setNights(moment.duration(end.diff(start)).asDays());
    } else {
      setNights(0);
    }
  }, [checkin, checkout]);

  const getKeyValue = function <T, U extends keyof T>(obj: T, key: U) { return obj[key] !== undefined ? obj[key] : '' }
  
  return (
    <React.Fragment>
      <div className="row d-flex ebs-title-box align-items-center">
        <div className="col-6">
          <h2 className="section-title">{event?.labels?.REGISTRATION_FORM_HOTEL_BOOKING_HEADING}</h2>
        </div>
        {Number(event?.order?.order_detail?.order?.edit_mode) === 1 && (
          <div className="col-6 text-right">
            <Link to={`/${event.url}/${provider}/order-summary/${order_id}`} className="ebs-back-summary"><i className="material-icons">keyboard_backspace</i>{event?.labels?.REGISTRATION_FORM_BACK_TO_SUMMARY}</Link>
          </div>
        )}
      </div>
      <div className="row">
        <div className="col-12">
          <p className="ebs-attendee-caption">{event?.labels?.REGISTRATION_FORM_HOTEL_BOOKING_DESCRIPTION}</p>
        </div>
      </div>
      <div className="wrapper-box hotel-booking-section">
        {loading && <Loader className='fixed' />}
        <header className="header-section">
          <h3>{event?.labels?.REGISTRATION_FORM_FIND_CHECK_AVAILBILITY}</h3>
        </header>
        {event?.event_description?.detail?.hotel_description && (
          <p className="ebs-attendee-caption" dangerouslySetInnerHTML={{ __html: event?.event_description?.detail?.hotel_description }}></p>
        )}
        <div className="top-form-booking">
          <DateRange
            onChange={handleDateChange}
            min={(data?.hotel_min_date ? data?.hotel_min_date : '')}
            max={(data?.hotel_max_date ? data?.hotel_max_date : '')}
            from={checkin}
            to={checkout}
            eventTimezone={event?.timezone?.timezone}
            locale={getLanguageCode(event.language_id)}
            label_from={event?.labels?.REGISTRATION_FORM_HOTEL_CHECK_IN_LABEL}
            label_to={event?.labels?.REGISTRATION_FORM_HOTEL_CHECK_OUT_LABEL}
          />
          <div className={`${event?.registration_flow_theme && event?.registration_flow_theme?.mode === 'dark' && 'dark-theme'} hotel-room`}>
            <span>{nights}</span>{nights === 1 ? event?.labels?.REGISTRATION_FORM_HOTEL_NIGHT_LABEL : event?.labels?.REGISTRATION_FORM_HOTEL_NIGHTS_LABEL}
          </div>
          {Number(form_settings.allow_single_room_only) === 0 && (
            <div className="number-rooms">
              <DropDown
                label={event?.labels?.REGISTRATION_FORM_HOTEL_ROOMS_LABEL}
                listitems={rooms}
                selected={room}
                selectedlabel={getSelectedLabel(rooms, room)}
                onChange={(e: any) => {
                  setRoom(e.value);
                  setHotels([]);
                }}
                placeholder={event?.labels?.REGISTRATION_FORM_SEARCH}
              />
            </div>
          )}
          <div className="form-button">
            <button className="btn btn-loader" onClick={handleSearch}>
              {action === "search" ? (
                <>
                  Loading...
                </>
              ) : (
                <>
                  {event?.labels?.REGISTRATION_FORM_SEARCH}
                </>
              )}
            </button>
          </div>
        </div>

        {data?.order && data?.order.order_detail && data?.order.order_detail.hotel?.filter((row: any, key: any) => (Number(row?.registration_form_id) === Number(data?.registration_form_id))).length > 0 && (
          <React.Fragment>
            <div className="summry-list-section">
              <h4>{event?.labels?.REGISTRATION_FORM_HOTEL_BOOKING_HEADING} - {data?.order.order_detail.hotel?.filter((row: any, key: any) => (Number(row?.registration_form_id) === Number(data?.registration_form_id))).length} {event?.labels?.REGISTRATION_FORM_HOTELS}</h4>
              {data?.order.order_detail.hotel?.filter((row: any, key: any) => (Number(row?.registration_form_id) === Number(data?.registration_form_id))).map((hotel: any, hotelKey: any) =>
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
                        <div className="per-night">{hotel.nights} {hotel.nights === 1 ? event?.labels?.REGISTRATION_FORM_HOTEL_NIGHT_LABEL : event?.labels?.REGISTRATION_FORM_HOTEL_NIGHTS_LABEL}, {hotel.rooms} {hotel.rooms === 1 ? event?.labels?.REGISTRATION_FORM_HOTEL_ROOMS_LABEL : event?.labels?.REGISTRATION_FORM_HOTEL_ROOMS_LABEL}</div>
                      </>
                    )}
                  </div>
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
                </div>
              )}
            </div>
            <br></br>
          </React.Fragment>
        )}

        {hotels && hotels.length > 0 && (
          <div className="data-wrapper-table">
            <div className="data-row header-table">
              <div className="row d-flex">
                <div className="col-6 col-6 description-box">
                  <div className="result-found">{hotels.length} {hotels.length === 1 ? event?.labels?.REGISTRATION_FORM_HOTEL_FOUND : event?.labels?.REGISTRATION_FORM_HOTELS_FOUND}</div>
                </div>
                <div className="col-2 box-price">
                  {Number(event?.payment_setting?.show_hotel_prices) === 1 && (
                    <strong>{event?.labels?.REGISTRATION_FORM_PRICE}</strong>
                  )}
                </div>
                <div className="col-2 box-subtotal">
                  {Number(event?.payment_setting?.show_hotel_prices) === 1 && (
                    <strong>{event?.labels?.REGISTRATION_FORM_SUB_TOTAL}</strong>
                  )}
                </div>
              </div>
            </div>
            {hotels && hotels.map((hotel: any, hotelKey: any) =>
              <div key={hotelKey} className="data-row">
                <div className="row d-flex">
                  <div className="col-6 description-box">
                    <span className={`btn_checbox style-radio ${Number(hotel.checked) === 1 ? 'checked' : ''}`} onClick={handleHotelSelection(hotelKey)}></span>
                    <h4>
                      {hotel.name}
                      {hotel.url && (
                        <a style={{ marginLeft: '10px' }} href={hotel.url} target="_blank" rel="noreferrer"><ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-feather-link.svg')} /></a>
                      )}
                    </h4>
                    <p>{hotel.description}</p>
                  </div>
                  <div className="col-2 box-price">
                    {Number(event?.payment_setting?.show_hotel_prices) === 1 && (
                      <>
                        <div className="price-item">{hotel.priceDisplay}</div>
                        <span className="per-night">{event?.labels?.REGISTRATION_FORM_HOTELS_PER_NIGHT}</span>
                      </>
                    )}
                  </div>
                  <div className="col-2 box-subtotal">
                    {Number(event?.payment_setting?.show_hotel_prices) === 1 && (
                      <>
                        <div className="price-item">{hotel.total_price_display}</div>
                        <span className="per-night">{hotel.nights} {hotel.nights === 1 ? event?.labels?.REGISTRATION_FORM_HOTEL_NIGHT_LABEL : event?.labels?.REGISTRATION_FORM_HOTEL_NIGHTS_LABEL}, {hotel.rooms} {hotel.rooms === 1 ? event?.labels?.REGISTRATION_FORM_HOTELS_ROOM : event?.labels?.REGISTRATION_FORM_HOTEL_ROOMS_LABEL}</span>
                      </>
                    )}
                  </div>
                  {Number(hotel.checked) === 1 && (
                    <div className="col-12 description-box">
                      <div className="room-detail-wrapper">
                        {Array.from({ length: room }, (_: any, i: any) =>
                          <div key={i} className="room-row">
                            <h4>{event?.labels?.REGISTRATION_FORM_HOTELS_ROOM} {(i + 1)} <em className="req">*</em></h4>
                            <p>{event?.labels?.REGISTRATION_FORM_HOTELS_SELECT_PEOPLE_HEADING}</p>
                            <div className="room-detail-form">
                              <Select
                                components={{ IndicatorSeparator: null }}
                                styles={style}
                                placeholder={event?.labels?.REGISTRATION_FORM_HOTELS_PERSON_NAME}
                                options={data?.attendees}
                                value={{ label: getSelectedLabel(data?.attendees, getKeyValue(hotel, 'hotel_person_room_' + (i + 1))), value: getKeyValue(hotel, 'hotel_person_room_' + (i + 1)) }}
                                onChange={attachAttendee(hotelKey, (i + 1))}
                              />
                            </div>
                            {simpleValidator.current.message(`${hotel.id}-${(i + 1)}`, getKeyValue(hotel, 'hotel_person_room_' + (i + 1)) ? true : null, 'required')}
                          </div>
                        )}
                      </div>
                    </div>
                  )}
                </div>
              </div>
            )}
          </div>
        )}

        <div className="bottom-button text-center bottom-button-panel">
          <button onClick={() => props.history.goBack()} className="btn btn-cancel">{event?.labels?.REGISTRATION_FORM_BACK}</button>
          {hotels && hotels.length > 0 && (
            <React.Fragment>
              <button className="btn btn-save-addmore btn-loader" onClick={(e: any) => { e.preventDefault(); setHotels([]); }}>
                {event?.labels?.REGISTRATION_FORM_CANCEL}
              </button>
              {Number(form_settings.allow_multiple_bookings) === 1 && (
                <button className="btn btn-save-addmore btn-loader" onClick={(e: any) => { e.preventDefault(); handleSubmit("save-book") }}>
                  {action === "save-book" ? (
                    <>
                      Loading...
                    </>
                  ) : (
                    <>
                      {event?.labels?.REGISTRATION_FORM_HOTELS_RESERVE_ANOTHER_ROOM}
                    </>
                  )}
                </button>
              )}
              <button className="btn btn-save-nex btn-loader" onClick={(e: any) => { e.preventDefault(); handleSubmit("save-next") }}>
                {action === "save-next" ? (
                  <>
                    Loading...
                    <i className="material-icons ebs-spinner">autorenew</i>
                  </>
                ) : (
                  <>
                    {event?.labels?.REGISTRATION_FORM_HOTELS_RESERVE_AND_NEXT}
                    <i className="material-icons">keyboard_arrow_right</i>
                  </>
                )}
              </button>
            </React.Fragment>
          )}
          {(Number(form_settings.dont_need_hotel) === 1 && total_hotels > 0) ? (
            <a onClick={() => {
              setLoading(true);
              if (formBuilderForms.length > 0) {
                history.push(`/${event.url}/${provider}/custom-forms/${order_id}/${attendee_id}/${formBuilderForms[0].id}`);
              } else {
                completedAttendeeIteration(attendee_id);
              }

            }} className="btn btn-cancel">{event?.labels?.EVENTSITE_DONT_NEED_HOTTEL_BUTTON}<i className="material-icons">keyboard_arrow_right</i></a>
          ) : (
            <>
              {((Number(form_settings.hotel_mandatory_registration_site) === 0 || total_hotels === 0) || data?.order?.order_detail?.hotel?.filter((row: any, key: any) => (Number(row?.registration_form_id) === Number(data?.registration_form_id))).length > 0) && (
                <a onClick={() => {
                    if((checkin !== '' || checkout !== '')){
                        toast.error(event?.labels?.CLEAR_CHECK_IN_OUT_DATES ? event?.labels?.CLEAR_CHECK_IN_OUT_DATES : 'Please clear checkin/out date(s) to move forward.', {
                          position: "bottom-center"
                        })
                    }else{
                      setLoading(true);
                      if (formBuilderForms.length > 0) {
                        history.push(`/${event.url}/${provider}/custom-forms/${order_id}/${attendee_id}/${formBuilderForms[0].id}`);
                      } else {
                        completedAttendeeIteration(attendee_id);
                      }
                    }
                }} className="btn btn-cancel">{event?.labels?.EVENTSITE_BILLING_PAYMENT_NEXT}<i className="material-icons">keyboard_arrow_right</i></a>
              )}
            </>
          )}
          
        </div>

      </div>

      <Toaster />

    </React.Fragment>
  );
};

export default ManageHotelBooking;