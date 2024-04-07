import React, { ReactElement, FC, useState, useEffect, useRef, useContext } from "react";
import { service } from '@/src/app/services/service';
import Event from '@/src/app/components/event/interface/Event';
import Loader from '@/src/app/components/forms/Loader';
import { useHistory } from 'react-router-dom';
import { ReactSVG } from "react-svg";
import { EventContext } from "@/src//app/context/event/EventProvider";
import in_array from "in_array";
import { getCurrency } from '@/src/app/helpers';
import ReactTooltip from "react-tooltip";
interface Props {
  section: any;
  order_id: number;
  provider: any;
  attendee_id: number;
  goToSection: any;
  event: Event;
  orderAttendee?: any;
  formSettings?: any;
}

const ManageItem: FC<Props> = (props): ReactElement => {

  const { section } = props;

  const { event, updateEvent, formBuilderForms, updateOrder } = useContext<any>(EventContext);

  const [count, setCount] = useState(0);

  const [currency, setCurrency] = useState("DKK");

  const [sum, setSum] = useState(0);

  const [orderAttendeeItemsCount, setOrderAttendeeItemsCount] = useState(0);

  const [items, setItems] = useState<any>([]);

  const [loading, setLoading] = useState(section === "manage-items" ? true : false);

  const [action, setAction] = useState(false);

  const [errors, setErrors] = useState<any>({});

  const [show, setShow] = useState(true);

  const [message, setMessage] = useState<any>({});

  const history = useHistory();

  const mounted = useRef(false);

  useEffect(() => {
    mounted.current = true;

    return () => {
      setItems([]);
      setMessage({});
      mounted.current = false;
    };
  }, []);

  useEffect(() => {
    if (section === "manage-items") {
      setLoading(true);
      service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/items/${props.order_id}/${props.attendee_id}?provider=${props?.provider}`)
        .then(
          response => {
            if (response.success && mounted.current) {
              if (response?.data?.order?.order_detail?.order?.status !== "completed" || (response?.data?.order?.order_detail?.order?.status === "completed" && (Number(response?.data?.order?.order_detail?.order?.is_waitinglist) === 1 || in_array(props?.provider, ['sale', 'admin'])))) {
                if (response?.data?.registrationItems?.length > 0 && ((Number(props?.formSettings?.show_items) === 1 && Number(event?.eventsite_setting?.payment_type) === 0) || (Number(props?.formSettings?.skip_items_step) === 0 && Number(event?.eventsite_setting?.payment_type) === 1))) {
                  setItems(response?.data?.registrationItems);
                  setOrderAttendeeItemsCount(response.data.orderAttendeeItemsCount);
                  setCurrency(response.data.currency);
                  setSum(calculateSubTotal(response.data.registrationItems));
                  updateOrder(response?.data?.order?.order_detail?.order);

                  //Update event info
                  updateEvent({
                    ...event,
                    order: response?.data?.order
                  });
                } else if (Number(event?.eventsite_setting?.payment_type) === 0 || (Number(event?.eventsite_setting?.payment_type) === 1 && Number(props?.formSettings?.skip_items_step) === 1)) {
                  if (Number(props?.formSettings?.show_business_dating) === 1) {
                    props.goToSection('manage-keywords', props.order_id, props.attendee_id);
                  } else if (Number(props?.formSettings?.show_subregistration) === 1) {
                    props.goToSection('manage-sub-registrations', props.order_id, props.attendee_id);
                  } else if (Number(props?.formSettings?.show_required_documents) === 1) {
                    props.goToSection('manage-documents', props.order_id, props.attendee_id);
                  } else if (Number(props?.formSettings?.show_hotels) === 1) {
                    props.goToSection('manage-hotel-booking', props.order_id, props.attendee_id);
                  } else if (formBuilderForms.length > 0) {
                    props.goToSection('custom-forms', props.order_id, props.attendee_id, formBuilderForms[0].id);
                  } else {
                    completedAttendeeIteration(props.attendee_id);
                  }
                } else {
                  setMessage({ success: false, info: event?.labels?.REGISTRATION_FORM_NO_ITEM_ARE_AVAILABLE_PLEASE_CONTACT_ADMINISTRATOR });
                }
                setLoading(false);
              } else {
                history.push(`/${event.url}/${props?.provider}`);
              }
            }
          },
          error => { }
        );
    }
  }, [section]);

  const handleItem = (item: any, index: any, key: any, value: any) => {
    if ((in_array(key, ['is_default']) && Number(item.is_required) === 0) || in_array(key, ['group_is_expanded']) || in_array(key, ['open_track_detail']) || (in_array(key, ['discount']) && Number(value) >= 0 && Number(value) <= (Number(item?.price) * Number(item?.quantity))) || (in_array(key, ['price']) && Number(value) >= 0 && Number(item?.discount) <= (Number(value) * Number(item?.quantity)))) {
      if (in_array(props?.provider, ['attendee', 'embed', 'sale']) || (!in_array(props?.provider, ['attendee', 'embed', 'sale']) && Number(item?.non_editable) !== 1)) {
        items[index][key] = in_array(key, ['price', 'discount']) ? Number(value) : value;
        if (in_array(key, ['discount'])) {
          items[index]['discount_type'] = (Number(value) > 0 ? 3 : 0);
        }
        setItems(items);
        setSum(calculateSubTotal(items));
        setCount(count + 1);
      }
    }
  };

  const handleItemQuantity = (item: any, itemKey: any) => (e: any) => {
    const quantity = e.target.value;
    if (!isNaN(quantity) && quantity <= item.qty && (item.remaining_tickets?.toString() === "Unlimited" || quantity <= Number(item.remaining_tickets))) {
      if (in_array(props?.provider, ['attendee', 'embed', 'sale']) || (!in_array(props?.provider, ['attendee', 'embed', 'sale']) && Number(item?.non_editable) !== 1)) {
        items[itemKey]['quantity'] = quantity;
        setItems(items);
        setSum(calculateSubTotal(items));
        setCount(count + 1);
      }
    }
  };

  const handleItemCounter = (item: any, itemKey: any, operator: any) => (e: any) => {
    if (in_array(props?.provider, ['attendee', 'embed', 'sale']) || (!in_array(props?.provider, ['attendee', 'embed', 'sale']) && Number(item?.non_editable) !== 1)) {
      if (operator === "+" && item.quantity < item.qty && (item.remaining_tickets?.toString() === "Unlimited" || item.quantity < Number(item.remaining_tickets))) {
        items[itemKey]['quantity'] = items[itemKey]['quantity'] + 1;
      } else if (operator === "-" && item.quantity > 1) {
        items[itemKey]['quantity'] = items[itemKey]['quantity'] - 1;
      }
      setItems(items);
      setSum(calculateSubTotal(items));
      setCount(count + 1);
    }
  };

  const handleGroupItem = (group: any, index: any, groupIndex: any, key: any, value: any) => {
    if (in_array(props?.provider, ['attendee', 'embed', 'sale']) || (!in_array(props?.provider, ['attendee', 'embed', 'sale']) && Number(items[index]['group_data'][groupIndex]['non_editable']) !== 1)) {
      if (!in_array(key, ['discount', 'price', 'open_track_detail', 'is_default'])) {
        setItems(items);
        setSum(calculateSubTotal(items));
        setCount(count + 1);
      } else if ((in_array(key, ['is_default']) && Number(items[index]['group_data'][groupIndex]['is_required']) === 0) || in_array(key, ['open_track_detail']) || (in_array(key, ['discount']) && Number(value) >= 0 && Number(value) <= (Number(items[index]['group_data'][groupIndex]['price']) * Number(items[index]['group_data'][groupIndex]['quantity']))) || (in_array(key, ['price']) && Number(value) >= 0 && Number(items[index]['group_data'][groupIndex]['discount']) <= (Number(value) * Number(items[index]['group_data'][groupIndex]['quantity'])))) {
        items[index]['group_data'][groupIndex][key] = in_array(key, ['price', 'discount']) ? Number(value) : value;
        if (in_array(key, ['discount'])) {
          items[index]['group_data'][groupIndex]['discount_type'] = (Number(value) > 0 ? 3 : 0);
        }
        if (in_array(key, ['is_default'])) {
          if (group.group_type === "multiple") {
            items[index]['group_data'][groupIndex][key] = value;
          } else {
            group.group_data.forEach((item: any, itemKey: any) => {
              items[index]['group_data'][itemKey][key] = 0;
            });
            items[index]['group_data'][groupIndex][key] = value;
          }
        }
        setItems(items);
        setSum(calculateSubTotal(items));
        setCount(count + 1);
      }
    }
  };

  const handleGroupItemCounter = (group_item: any, itemKey: any, groupItemKey: any, operator: any) => (e: any) => {
    if (in_array(props?.provider, ['attendee', 'embed', 'sale']) || (!in_array(props?.provider, ['attendee', 'embed', 'sale']) && Number(items[itemKey]['group_data'][groupItemKey]['non_editable']) !== 1)) {
      if (operator === "+" && group_item.quantity < group_item.qty && (group_item.remaining_tickets?.toString() === "Unlimited" || group_item.quantity < Number(group_item.remaining_tickets))) {
        items[itemKey]['group_data'][groupItemKey]['quantity'] = items[itemKey]['group_data'][groupItemKey]['quantity'] + 1;
      } else if (operator === "-" && group_item.quantity > 1) {
        items[itemKey]['group_data'][groupItemKey]['quantity'] = items[itemKey]['group_data'][groupItemKey]['quantity'] - 1;
      }
      setItems(items);
      setSum(calculateSubTotal(items));
      setCount(count + 1);
    }
  };

  const handleGroupItemQuantity = (group_item: any, itemKey: any, groupItemKey: any) => (e: any) => {
    if (in_array(props?.provider, ['attendee', 'embed', 'sale']) || (!in_array(props?.provider, ['attendee', 'embed', 'sale']) && Number(items[itemKey]['group_data'][groupItemKey]['non_editable']) !== 1)) {
      const quantity = e.target.value;
      if (!isNaN(quantity) && quantity > 0 && quantity <= group_item.qty && (group_item.remaining_tickets?.toString() === "Unlimited" || quantity <= Number(group_item.remaining_tickets))) {
        items[itemKey]['group_data'][groupItemKey]['quantity'] = quantity;
        setItems(items);
        setSum(calculateSubTotal(items));
        setCount(count + 1);
      }
    }
  };

  function calculateSubTotal(items: any): any {
    let sum = 0;
    items.forEach((item: any) => {
      if (Number(item.is_default) === 1) sum += ((item.price * item.quantity) - item.discount);
      if (item.group_data && item.group_data.length > 0) {
        item.group_data.forEach((groupItem: any) => {
          if (Number(groupItem.is_default) === 1) sum += ((groupItem.price * groupItem.quantity) - groupItem.discount);
        });
      }
    });

    return sum;
  }

  const handleSubmit = (evt: any) => {
    evt.preventDefault();
    if (!action) {
      setAction(true);
      setMessage({});
      service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/items/${props.order_id}/${props.attendee_id}`, { items: items, provider: props?.provider })
        .then(
          response => {
            if (mounted.current) {
              if (response.success) {
                if (Number(props?.formSettings?.show_business_dating) === 1) {
                  props.goToSection('manage-keywords', props.order_id, props.attendee_id);
                } else if (Number(props?.formSettings?.show_subregistration) === 1) {
                  props.goToSection('manage-sub-registrations', props.order_id, props.attendee_id);
                } else if (Number(props?.formSettings?.show_required_documents) === 1) {
                  props.goToSection('manage-documents', props.order_id, props.attendee_id);
                } else if (Number(props?.formSettings?.show_hotels) === 1) {
                  props.goToSection('manage-hotel-booking', props.order_id, props.attendee_id);
                } else if (formBuilderForms.length > 0) {
                  props.goToSection('custom-forms', props.order_id, props.attendee_id, formBuilderForms[0].id);
                } else {
                  completedAttendeeIteration(props.attendee_id);
                }
              } else {
                if (response?.errors !== undefined && Object.keys(response?.errors).length > 0) {
                  setErrors(response.errors);
                } else {
                  setMessage({ success: false, info: response?.message });
                   setTimeout(() => {
                  const scrollTo = document?.getElementsByClassName('alert-danger')[0];
                  if (scrollTo !== undefined && scrollTo !== null) {
                    scrollTo.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
                  }
                }, 500);
                }
              }
              setAction(false);
            }
          },
          error => {
            setAction(false);
          }
        );
    }
  }

  const completedAttendeeIteration = (attendee_id: any) => {
    service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/completed-attendee-iteration`, { attendee_id: attendee_id, order_id: props.order_id, provider: props?.provider })
      .then(
        response => {
          if (mounted.current) {
            if (response.success) {
              history.push(`/${event.url}/${props?.provider}/order-summary/${props.order_id}`);
            } else {
              setErrors(response.errors);
            }
            setAction(false);
            setLoading(false);
          }
        },
        error => {
          setAction(false);
          setLoading(false);
        }
      );
  }

  useEffect(() => {
    setMessage({});
  }, [section]);

  useEffect(() => {
    ReactTooltip.rebuild()
  }, [show])

  return (
    <React.Fragment>
      {((Number(props?.formSettings?.show_items) === 1 && Number(event?.eventsite_setting?.payment_type) === 0) || Number(props?.formSettings?.skip_items_step) === 0 && (Number(event?.eventsite_setting?.payment_type) === 1)) && (
        <div className={`${section !== "manage-items" && 'tab-collapse'} wrapper-box select-items-section`}>
          {loading ? (
            <Loader className='fixed' />
          ) : (
            <React.Fragment>
              <header className="header-section">
                <h3 onClick={(e: any) => {
                  if (props.order_id && props.attendee_id && section !== "manage-items") {
                    if ((location.pathname.toString().includes("/manage-sub-registrations") || location.pathname.toString().includes("/manage-keywords") || location.pathname.toString().includes("/manage-documents")) || props?.orderAttendee?.status === 'complete') {
                      history.push(`/${event.url}/${props?.provider}/manage-items/${props.order_id}/${props.attendee_id}`);
                    }
                  } else {
                    setShow(!show)
                  }
                }}>
                  {event?.labels?.REGISTRATION_FORM_SELECT_ITEMS}  <i className="material-icons"> {section === "manage-items" && show ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}</i>
                </h3>
                <div className="icon-tick">
                  {((location.pathname.toString().includes("/manage-items") || location.pathname.toString().includes("/manage-keywords") || location.pathname.toString().includes("/manage-sub-registrations") || location.pathname.toString().includes("/manage-documents")) || props?.orderAttendee?.status === 'complete') ? (
                    <img src={require('@/src/img/tick-green.svg')} alt="" />
                  ) : (
                    <img src={require('@/src/img/tick-grey.svg')} alt="" />
                  )}
                </div>
              </header>

              {section === "manage-items" && show && (
                <div className="wrapper-inner-content">
                  {section === "manage-items" && show && event?.event_description?.detail?.items_description && (
                    <p className="ebs-attendee-caption" dangerouslySetInnerHTML={{ __html: event?.event_description?.detail?.items_description }}></p>
                  )}
                  {!message?.success && message?.info && (
                    <div className="alert alert-danger" role="alert">
                      {message?.info}
                    </div>
                  )}
                  {items?.length > 0 && (
                    <React.Fragment>
                      <div className="data-wrapper-table">
                        <div className="data-row header-table">
                          <div className="row d-flex">
                            <div className={`${props?.provider === 'sale' ? 'col-4' : 'col-6'}`}></div>
                            {(Number(event?.eventsite_setting?.payment_type) === 1 || (Number(event?.eventsite_setting?.payment_type) === 0 && Number(props?.formSettings?.display_left_tickets) === 1)) && (
                              <div className="col-2 box-price">
                                <strong>{Number(event?.eventsite_setting?.payment_type) === 1 ? event?.labels?.REGISTRATION_FORM_PRICE : event?.labels?.REGISTRATION_FORM_TICKETS}</strong>
                              </div>
                            )}
                            {props?.provider === 'sale' && Number(event?.eventsite_setting?.payment_type) === 1 && (
                              <div className="col-2 box-price">
                                <strong>{event?.labels?.REGISTRATION_FORM_DISCOUNT}</strong>
                              </div>
                            )}
                            <div className="col-2 box-qty">
                              <strong>{event?.labels?.REGISTRATION_FORM_QTY}</strong>
                            </div>
                            {Number(event?.eventsite_setting?.payment_type) === 1 && (
                              <div className="col-2 box-subtotal">
                                <strong>{event?.labels?.REGISTRATION_FORM_SUB_TOTAL}</strong>
                              </div>
                            )}
                          </div>
                        </div>
                        {items && items?.length > 0 && items.map((item: any, itemKey: any) =>
                          <div key={itemKey} className="data-row">
                            {item.group_data && item.group_data.length > 0 ? (
                              <div className="inner-table-fields">
                                <h3 style={{ cursor: 'pointer', marginBottom: item.group_is_expanded === "yes" ? '16px' : '0' }} className="d-flex align-items-center" onClick={() => {
                                  handleItem(item, itemKey, 'group_is_expanded', item.group_is_expanded === "yes" ? 'no' : 'yes');
                                }}>
                                  <i style={{ fontSize: '18px' }} className="material-icons">{item.group_is_expanded === "yes" ? 'keyboard_arrow_down' : 'keyboard_arrow_right'}</i>
                                  {item.detail.group_name}
                                  {item.group_required === "yes" && (
                                    <em className='req'>*</em>
                                  )}
                                </h3>
                                {item.group_is_expanded === "yes" ?
                                  item.group_data.map((group_item: any, groupItemKey: any) =>
                                    <div key={groupItemKey} className="data-row">
                                      <div className="row d-flex">
                                        <div className={`${props?.provider === 'sale' ? 'col-4' : 'col-6'} description-box`}>
                                          {item.group_type === "multiple" ? (
                                            <span className={`btn_checbox ${Number(group_item.is_default) === 1 ? 'checked' : ''} ${group_item?.remaining_tickets?.toString() === "0" && 'sold-out'}`} onClick={(e: any) => {
                                              if (group_item?.remaining_tickets?.toString() !== "0") {
                                                handleGroupItem(item, itemKey, groupItemKey, 'is_default', (Number(group_item.is_default) === 1 ? 0 : 1));
                                              }
                                            }}></span>
                                          ) : (
                                            <span className={`btn_checbox radio-style ${Number(group_item.is_default) === 1 ? 'checked' : ''} ${group_item?.remaining_tickets?.toString() === "0" && 'sold-out'}`} onClick={(e: any) => {
                                              if (group_item?.remaining_tickets?.toString() !== "0") {
                                                handleGroupItem(item, itemKey, groupItemKey, 'is_default', (Number(group_item.is_default) === 1 ? 0 : 1));
                                              }
                                            }}></span>
                                          )}
                                          <h4>
                                            {group_item.detail.item_name}
                                            {!in_array(props?.provider, ['attendee', 'embed', 'sale']) && Number(group_item?.non_editable) === 1 && (
                                              <i style={{ fontSize: 16, marginLeft: 5, cursor: 'pointer' }} data-tip="You cannot edit this item" className="material-icons">info</i>
                                            )}
                                            {Number(group_item.is_required) === 1 && (
                                              <em className='req'>*</em>
                                            )}
                                            {group_item?.qty_discount_info && (
                                              <span data-event={window.innerWidth <= 768 ? 'click focus' : ''} data-html={true} data-tip={group_item?.qty_discount_info}  className="ebs-icon-line ebs-main-tooltip" ><span>{event?.labels?.REGISTRATION_FORM_OFFER}</span></span>
                                            )}
                                          </h4>
                                          {group_item.detail.description && <p dangerouslySetInnerHTML={{ __html: group_item.detail.description }}></p>}
                                        </div>
                                        {(Number(event?.eventsite_setting?.payment_type) === 1 || (Number(event?.eventsite_setting?.payment_type) === 0 && Number(props?.formSettings?.display_left_tickets) === 1)) && (
                                          <div className="col-2 box-price">
                                            {Number(event?.eventsite_setting?.payment_type) === 1 ? (
                                              props?.provider === 'sale' ? (
                                                <div className="price-item">
                                                  <input type="text" value={group_item.price} onChange={(e: any) => {
                                                    e.preventDefault();
                                                    handleGroupItem(item, itemKey, groupItemKey, 'price', e?.target?.value);
                                                  }} />
                                                </div>
                                              ) : (
                                                <>
                                                  <div className="price-item">{group_item.priceDisplay}</div>
                                                  {Number(props?.formSettings?.display_left_tickets) === 1 && (
                                                      <div className={`qty-items ${group_item.remaining_tickets?.toString() === "0" && 'error-qty'}`}>{group_item.remaining_tickets?.toString() !== "Unlimited" && `${group_item.remaining_tickets} ${event?.labels?.REGISTRATION_FORM_TICKETS_LEFT}`}</div>
                                                  )}
                                                </>
                                              )
                                            ) : (
                                                <div className={`qty-items ${group_item.remaining_tickets?.toString() === "0" && 'error-qty'}`}>{group_item.remaining_tickets?.toString() !== "Unlimited" && `${group_item.remaining_tickets} ${event?.labels?.REGISTRATION_FORM_TICKETS_LEFT}`}</div>
                                            )}
                                          </div>
                                        )}
                                        {Number(event?.eventsite_setting?.payment_type) === 1 && (
                                          props?.provider === 'sale' && (
                                            <div className="col-2 box-price">
                                              <div className="price-item">
                                                <input type="text" value={group_item.discount} onChange={(e: any) => {
                                                  e.preventDefault();
                                                  handleGroupItem(item, itemKey, groupItemKey, 'discount', e?.target?.value);
                                                }} />
                                              </div>
                                            </div>
                                          )
                                        )}
                                        <div className="col-2 box-qty">
                                          {group_item.qty > 1 && (
                                            <div className="theme-counter-items">
                                              <span><i onClick={handleGroupItemCounter(group_item, itemKey, groupItemKey, '-')} className="material-icons">keyboard_arrow_left</i></span>
                                              <input type="number" name="quantity" value={group_item.quantity} onChange={handleGroupItemQuantity(group_item, itemKey, groupItemKey)} autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''} />
                                              <span><i onClick={handleGroupItemCounter(group_item, itemKey, groupItemKey, '+')} className="material-icons">keyboard_arrow_right</i></span>
                                            </div>
                                          )}
                                        </div>
                                        {Number(event?.eventsite_setting?.payment_type) === 1 && (
                                          <div className="col-2 box-subtotal">
                                            <div className="price-item">{getCurrency(((group_item.price * group_item.quantity) - group_item.discount), currency) + ' ' + currency}</div>
                                          </div>
                                        )}
                                      </div>
                                      {group_item.link_data?.length > 0 && Object.keys(group_item.link_data) && Object.keys(group_item.link_data).length > 0 && (
                                        <div className="wrapper-date-list">
                                          <div className="btn-track-detail"><span>{event?.labels?.REGISTRATION_FORM_TRACK_DETAIL}<i className="material-icons" onClick={(e: any) => {
                                            e.preventDefault();
                                            handleGroupItem(item, itemKey, groupItemKey, 'open_track_detail', (Number(group_item.open_track_detail) === 1 ? 0 : 1));
                                          }}>{Number(group_item.open_track_detail) === 1 ? 'keyboard_arrow_down' : 'keyboard_arrow_right'}</i></span> </div>
                                          {Number(group_item.open_track_detail) === 1 && (
                                            <>
                                              {Object.keys(group_item.link_data).map((date: any, i: any) =>
                                                <div key={i} className="datelist-wrapper">
                                                  <h5>{date}</h5>
                                                  {group_item?.link_data[date] && group_item?.link_data[date]?.length > 0 && (
                                                    <>
                                                      {group_item?.link_data[date]?.map((itm: any, c: any) =>
                                                        <div key={c} className="datelist-section" style={{ paddingLeft: 30 }}>
                                                          {itm.topic && <h6>{itm.topic} <i data-event={window.innerWidth <= 768 ? 'click focus' : ''} data-tip={itm.description ? itm.description : itm.topic} data-place="right" className="material-icons ico-tooltip-info">info</i></h6>}
                                                          <p>{`${itm.start_time} - ${itm.end_time}`}, {itm.location}</p>
                                                        </div>
                                                      )}
                                                    </>
                                                  )}
                                                </div>
                                              )}
                                            </>
                                          )}
                                        </div>
                                      )}
                                    </div>
                                  ) : ''
                                }
                              </div>
                            ) : (
                              <>
                                <div className="row d-flex">
                                  <div className={`${props?.provider === 'sale' ? 'col-4' : 'col-6'} description-box`}>
                                    {!item.group_data && (
                                      <span className={`btn_checbox ${Number(item.is_default) === 1 ? 'checked' : ''} ${item?.remaining_tickets?.toString() === "0" && 'sold-out'}`} onClick={(e: any) => {
                                        e.preventDefault();
                                        if (item?.remaining_tickets?.toString() !== "0") {
                                          handleItem(item, itemKey, 'is_default', Number(item.is_default) === 1 ? 0 : 1);
                                        }
                                      }}></span>
                                    )}
                                    <h4>
                                      {item.detail.item_name}
                                      {!in_array(props?.provider, ['attendee', 'embed', 'sale']) && Number(item?.non_editable) === 1 && (
                                        <i style={{ fontSize: 16, marginLeft: 5, cursor: 'pointer' }} data-tip="You cannot edit this item" className="material-icons">info</i>
                                      )}
                                      {Number(item.is_required) === 1 && (
                                        <em className='req'>*</em>
                                      )}
                                      {item?.qty_discount_info && (
                                        <span data-event={window.innerWidth <= 768 ? 'click focus' : ''} data-html={true} data-tip={item?.qty_discount_info}  className="ebs-icon-line ebs-main-tooltip" ><span>{event?.labels?.REGISTRATION_FORM_OFFER}</span></span>
                                      )}
                                    </h4>
                                    {item.detail.description && <p dangerouslySetInnerHTML={{ __html: item.detail.description }}></p>}
                                  </div>
                                  {(Number(event?.eventsite_setting?.payment_type) === 1 || (Number(event?.eventsite_setting?.payment_type) === 0 && Number(props?.formSettings?.display_left_tickets) === 1)) && (
                                    <div className="col-2 box-price">
                                      {!item.group_data && (
                                        Number(event?.eventsite_setting?.payment_type) === 1 ? (
                                          <React.Fragment>
                                            {props?.provider === 'sale' ? (
                                              <div className="price-item">
                                                <input type="text" value={item.price} onChange={(e: any) => {
                                                  e.preventDefault();
                                                  handleItem(item, itemKey, 'price', e.target.value);
                                                }} />
                                              </div>
                                            ) : (
                                              <>
                                                <div className="price-item">{item.priceDisplay}</div>
                                                {Number(props?.formSettings?.display_left_tickets) === 1 && (
                                                      <div className={`qty-items ${item.remaining_tickets?.toString() === "0" && 'error-qty'}`}>{item.remaining_tickets?.toString() !== "Unlimited" && `${item.remaining_tickets} ${event?.labels?.REGISTRATION_FORM_TICKETS_LEFT}`}</div>
                                                )}
                                              </>
                                            )}
                                          </React.Fragment>
                                        ) : (
                                              <div className={`qty-items ${item.remaining_tickets?.toString() === "0" && 'error-qty'}`}>{item.remaining_tickets?.toString() !== "Unlimited" && `${item.remaining_tickets} ${event?.labels?.REGISTRATION_FORM_TICKETS_LEFT}`}</div>
                                        )
                                      )}
                                    </div>
                                  )}
                                  {Number(event?.eventsite_setting?.payment_type) === 1 && (
                                    props?.provider === 'sale' && (
                                      <div className="col-2 box-price">
                                        {!item.group_data && (
                                          <React.Fragment>
                                            <div className="price-item">
                                              <input type="text" value={item.discount} onChange={(e: any) => {
                                                e.preventDefault();
                                                handleItem(item, itemKey, 'discount', e.target.value);
                                              }} />
                                            </div>
                                          </React.Fragment>
                                        )}
                                      </div>
                                    )
                                  )}
                                  <div className="col-2 box-qty">
                                    {item.qty > 1 && (
                                      <div className="theme-counter-items">
                                        <span><i onClick={handleItemCounter(item, itemKey, '-')} className="material-icons">keyboard_arrow_left</i></span>
                                        <input type="number" name="quantity" value={item.quantity} onChange={handleItemQuantity(item, itemKey)} autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''} />
                                        <span><i onClick={handleItemCounter(item, itemKey, '+')} className="material-icons">keyboard_arrow_right</i></span>
                                      </div>
                                    )}
                                  </div>
                                  {Number(event?.eventsite_setting?.payment_type) === 1 && (
                                    <div className="col-2 box-subtotal">
                                      {!item.group_data && (
                                        <div className="price-item">{getCurrency(((item.price * item.quantity) - item.discount), currency) + ' ' + currency}</div>
                                      )}
                                    </div>
                                  )}
                                </div>
                                {item.link_data?.length > 0 && Object.keys(item.link_data) && Object.keys(item.link_data).length > 0 && (
                                  <div className="wrapper-date-list">
                                    <div className="btn-track-detail"><span>{event?.labels?.REGISTRATION_FORM_TRACK_DETAIL}<i className="material-icons" onClick={(e: any) => {
                                      e.preventDefault();
                                      handleItem(item, itemKey, 'open_track_detail', Number(item.open_track_detail) === 1 ? 0 : 1)
                                    }}>{Number(item.open_track_detail) === 1 ? 'keyboard_arrow_down' : 'keyboard_arrow_right'}</i></span> </div>
                                    {Number(item.open_track_detail) === 1 && (
                                      <>
                                        {Object.keys(item.link_data).map((date: any, i: any) =>
                                          <div key={i} className="datelist-wrapper">
                                            <h5>{date}</h5>
                                            {item?.link_data[date] && item?.link_data[date]?.length > 0 && (
                                              <>
                                                {item?.link_data[date]?.map((itm: any, c: any) =>
                                                  <div key={c} className="datelist-section" style={{ paddingLeft: 30 }}>
                                                    {itm.topic && <h6>{itm.topic} <i data-event={window.innerWidth <= 768 ? 'click focus' : ''} data-tip={itm.description ? itm.description : itm.topic} data-place="right" className="material-icons ico-tooltip-info">info</i></h6>}
                                                    <p>{`${itm.start_time} - ${itm.end_time}`}, {itm.location}</p>
                                                  </div>
                                                )}
                                              </>
                                            )}
                                          </div>
                                        )}
                                      </>
                                    )}
                                  </div>
                                )}
                              </>
                            )}
                          </div>
                        )}
                        {Number(event?.eventsite_setting?.payment_type) === 1 && (
                          <div className="data-row footer-table">
                            <div className="row d-flex">
                              <div className="col-6"></div>
                              <div className="col-2 box-price">
                              </div>
                              <div className="col-2 box-qty">
                                <strong>{event?.labels?.REGISTRATION_FORM_SUB_TOTAL_EXCL_VAT}</strong>
                              </div>
                              <div className="col-2 box-subtotal">
                                <strong>{(currency !== null && currency !== undefined ? getCurrency(sum, currency) + ' ' + currency : 0)}</strong>
                              </div>
                            </div>
                          </div>
                        )}
                      </div>
                      <div className="bottom-button text-center">
                        {props?.orderAttendee?.status === 'complete' && (
                          <a onClick={() => {
                            if (Number(props?.formSettings?.show_business_dating) === 1) {
                              props.goToSection('manage-keywords', props.order_id, props.attendee_id);
                            } else if (Number(props?.formSettings?.show_subregistration) === 1) {
                              props.goToSection('manage-sub-registrations', props.order_id, props.attendee_id);
                            } else if (Number(props?.formSettings?.show_required_documents) === 1) {
                              props.goToSection('manage-documents', props.order_id, props.attendee_id);
                            } else if (Number(props?.formSettings?.show_hotels) === 1) {
                              props.goToSection('manage-hotel-booking', props.order_id, props.attendee_id);
                            } else {
                              setLoading(true);
                              completedAttendeeIteration(props.attendee_id);
                            }
                          }} className="btn btn-cancel">{event?.labels?.REGISTRATION_FORM_SKIP} <i className="material-icons">keyboard_arrow_right</i></a>
                        )}
                        <button
                          onClick={handleSubmit}
                          className="btn btn-save-next btn-loader"
                        >
                          {action ? (
                            <>
                              Loading...
                              <i className="material-icons ebs-spinner">autorenew</i>
                            </>
                          ) : (
                            <>
                              {event?.labels?.REGISTRATION_FORM_SAVE_AND_NEXT}
                              <i className="material-icons">keyboard_arrow_right</i>
                            </>
                          )}
                        </button>
                      </div>
                    </React.Fragment>
                  )}
                </div>
              )}
              <ReactTooltip className="ebs-tooltip-wrapper" globalEventOff="click" data-scroll-hide resizeHide clickable effect="solid" />
            </React.Fragment>
          )
          }
        </div>
      )}
    </React.Fragment>
  );
};

export default ManageItem;