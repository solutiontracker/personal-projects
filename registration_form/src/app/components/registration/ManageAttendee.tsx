import React, { ReactElement, FC, useState, useEffect, useRef, useContext, useMemo } from "react";
import Input from '@/src/app/components/forms/Input';
import DateTime from '@/src/app/components/forms/DateTime';
import DropDown from '@/src/app/components/forms/DropDown';
import TextArea from '@/src/app/components/forms/TextArea';
import { service } from '@/src/app/services/service';
import Event from '@/src/app/components/event/interface/Event';
import Loader from '@/src/app/components/forms/Loader';
import Popup from '@/src/app/components/forms/Popup';
import SimpleReactValidator from 'simple-react-validator';
import moment from 'moment-timezone';
import { useLocation, useHistory } from 'react-router-dom';
import { EventContext } from "@/src//app/context/event/EventProvider";
import in_array from "in_array";
import { toggleValueInArray, getLanguageCode, header } from '@/src/app/helpers';
import { confirmAlert } from 'react-confirm-alert';
import 'react-confirm-alert/src/react-confirm-alert.css';
import debounce from "lodash.debounce";
interface Props {
  section: any;
  order_id?: number;
  attendee_id?: number;
  provider?: any;
  ids?: any;
  registration_form_id?: any;
  goToSection: any;
  event: Event;
  orderAttendee?: any;
  formSettings?: any;
  loadFormSeting?: any;
}

const ManageAttendee: FC<Props> = (props: any): ReactElement => {

  const { section } = props;

  const location = useLocation();

  const history = useHistory();

  const [, forceUpdate] = useState(0);

  const { event, updateEvent, updateRouteParams, routeParams, updateFormBuilderForms, formBuilderForms, updateOrder } = useContext<any>(EventContext);

  const simpleValidator = useRef(new SimpleReactValidator({
    element: (message: any) => <p className="error-message">{message}</p>,
    messages: {
      required: event?.labels?.REGISTRATION_FORM_FIELD_REQUIRED,
      email: event?.labels?.REGISTRATION_FORM_CONFIRM_EMAIL_MATCH,
      in: event?.labels?.REGISTRATION_FORM_CONFIRM_EMAIL_MATCH
    },
    autoForceUpdate: { forceUpdate: () => forceUpdate(1) }
  }))

  const [attendee, setAttendee] = useState<any>({});

  const [sections, setSections] = useState([]);

  const [countries, setCountries] = useState([]);

  const [languages, setLanguages] = useState([]);

  const [country_codes, setCountryCodes] = useState([]);

  const [event_country_code, setEventCountryCode] = useState([]);

  const [custom_fields, setCustomFields] = useState([]);

  const [registration_forms, setRegistrationForms] = useState([]);

  const [custom_field_ids, setCustomFieldIds] = useState<any[]>([]);

  const [gdpr, setGdpr] = useState<any>({});

  const [order, setOrder] = useState<any>({});

  const [food, setFood] = useState<any>({});

  const [disclaimer, setDisclaimer] = useState<any>();

  const [popup, setPopup] = useState(false);

  const [stock_message, setStockMessage] = useState('');

  const [customize, setCustomize] = useState(false);

  const [foodModal, setFoodModal] = useState(false);

  const [disclaimerModal, setDisclaimerModal] = useState(false);

  const [loading, setLoading] = useState(in_array(section, ["manage-attendee", "autoregister", "registration-form"]) ? true : false);

  const [action, setAction] = useState(false);

  const [errors, setErrors] = useState<any>({});

  const [show, setShow] = useState(true);

  const [message, setMessage] = useState<any>({});

  const [subscribers, setSubscribers] = useState<any>([]);

  const mounted = useRef(false);

  const search = location.search;

  const attendee_types = new URLSearchParams(search).get("attendee_types");

  const sale_id = new URLSearchParams(search).get("sale_id");

  useEffect(() => {
    mounted.current = true;

    return () => {
      postCodeHandler.cancel();
      mounted.current = false;
    };
  }, []);

  useEffect(() => {
    if (in_array(section, ['registration-form', 'manage-attendee'])) {
      setLoading(true);
      service.get(props.order_id ? `${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/attendee-personal-information/${props.order_id}/${props.attendee_id || 0
        }?provider=${props?.provider}` : `${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/attendee-personal-information?registration_form_id=${props?.registration_form_id !== undefined ? props?.registration_form_id : 0}&provider=${props?.provider}&attendee_types=${attendee_types !== null ? attendee_types : ''}`)
        .then(
          response => {
            if (response.success && mounted.current) {
              if (response?.data?.order?.order_detail?.order?.status !== "completed" || (response?.data?.order?.order_detail?.order?.status === "completed" && (Number(response?.data?.order?.order_detail?.order?.is_waitinglist) === 1 || in_array(props?.provider, ['sale', 'admin'])))) {
                setGdpr(response.data.gdpr);
                setFood(response.data.food);
                setDisclaimer(response.data.disclaimer);
                setAttendee(response.data.attendee);
                setRegistrationForms(response.data.registration_forms);
                setSections(response.data.sections);
                setCustomFields(response.data.custom_fields);
                setCountries(response.data.metadata.countries);
                setLanguages(response.data.languages);
                setCountryCodes(response.data.metadata.country_codes);
                setEventCountryCode(response.data.metadata.event_country_code);
                setSubscribers(response?.data?.subscribers);
                setStockMessage(response?.data?.stock_message);
                setOrder(response?.data?.order?.order_detail?.order);
                updateOrder(response?.data?.order?.order_detail?.order);
                setLoading(false);

                //Update event info
                updateEvent({
                  ...event,
                  order: response?.data?.order
                });

                //Update event info
                updateFormBuilderForms({
                  forms: [...response?.data?.form_builder_forms
                  ], attendee_id: props?.attendee_id, order_id: props?.order_id
                });

                //Save payment settings
                if (Number(response.data.attendee?.attendee_type) > 0) {
                  props?.loadFormSeting(response.data.form_settings);
                }

              } else {
                history.push(`/${event.url}/${props?.provider}/manage-attendee`);
              }
            }
          },
          error => {
            setLoading(false);
          }
        );
    } else if (section === "autoregister") {
      service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/autoregister/${props.ids}?provider=${props?.provider}`)
        .then(
          response => {
            if (mounted.current) {
              setLoading(false);
              if (response.success) {
                if (Number(response?.data?.registered) === 1 || response?.data?.order?.order_detail?.order?.status !== "completed" || (response?.data?.order?.order_detail?.order?.status === "completed" && (Number(response?.data?.order?.order_detail?.order?.is_waitinglist) === 1 || in_array(props?.provider, ['sale', 'admin'])))) {
                  if (Number(response?.data?.registered) === 1) {
                    history.push(`/${event.url}/${props?.provider}/auto-registration-success/${response?.data?.order?.order_detail?.order?.id}`);
                  } else {
                    setGdpr(response.data.gdpr);
                    setFood(response.data.food);
                    setDisclaimer(response.data.disclaimer);
                    setAttendee(response.data.attendee);
                    setSections(response.data.sections);
                    setCustomFields(response.data.custom_fields);
                    setCountries(response.data.metadata.countries);
                    setLanguages(response.data.languages);
                    setCountryCodes(response.data.metadata.country_codes);
                    setEventCountryCode(response.data.metadata.event_country_code);
                    setStockMessage(response?.data?.stock_message);
                    setOrder(response?.data?.order?.order_detail?.order);
                    updateOrder(response?.data?.order?.order_detail?.order);
                    
                    //Save payment settings
                    if (Number(response.data.attendee?.attendee_type) > 0) {
                      props?.loadFormSeting(response.data.form_settings);
                    }
                  }
                } else {
                  history.push(`/${event.url}/${props?.provider}/manage-attendee`);
                }
              } else {
                setMessage({ success: false, info: response?.data?.message });
              }
            }
          },
          error => {
            setLoading(false);
          }
        );
    }
    setMessage({});
  }, [section]);

  const loadRegistrationForm = (type_id: any, input: any) => {
    setLoading(true);
    service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/eventsite-settings/eventsite-section-fields`, {
      attendee_type_id: type_id,
      provider: props?.provider
    })
      .then(
        response => {
          if (response.success && mounted.current) {
            setSections(response.data.sections);
            setCustomFields(response.data.custom_fields);
            const updateAttendee = {
              ...response.data.attendee,
              [input]: type_id,
              'first_name': attendee?.first_name,
              'last_name': attendee?.last_name,
              'email': attendee?.email,
              'confirm_email': attendee?.confirm_email,
              'company_name': attendee?.company_name,
            }
            setAttendee(updateAttendee);
            //Update event info
            updateFormBuilderForms({
              forms: [...response?.data?.form_builder_forms
              ], attendee_id: props?.attendee_id, order_id: props?.order_id
            });
            props?.loadFormSeting(response.data.form_settings, true);
            setLoading(false);
            setErrors({});
            setStockMessage(response?.data?.stock_message);
          }
        },
        error => {
          setLoading(false);
        }
      );
  }
  const handleChange = (input: any, type?: any) => (e: any) => {
    if (input === 'attendee_type') {
      if (e.value !== attendee.attendee_type && customize || (e.value !== attendee.attendee_type && props.attendee_id !== undefined)) {
        confirmAlert({
          customUI: ({ onClose }) => {
            return (
              <div id="loader-wrapper" className={`fixed ebs-popup-container ${in_array(routeParams?.provider, ["sale", "embed"]) && 'ebs-popup-top'}`}>
                <div className="ebs-popup-wrapper" style={{ maxWidth: '550px' }}>
                  <span onClick={() => {
                    onClose();
                  }} className="ebs-close link"><i className="material-icons">close</i></span>
                  <header className="ebs-header">
                    <h3 className='link'>{event?.labels?.REGISTRATION_FORM_ALERT}</h3>
                  </header>
                  <div className="ebs-popup-content">
                    <div>
                      {event?.labels?.REGISTRATION_FORM_ATTENDEE_TYPE_CHANGE_CONFIRMATION_ALERT}
                    </div>
                  </div>
                  <div className="ebs-popup-buttons text-center" style={{ marginBottom: '25px' }}>
                    <div className="btn bordered" onClick={() => {
                      onClose();
                    }}>{event?.labels?.GENERAL_CANCEL}</div>
                    <div className="btn btn-primary" onClick={() => {
                      loadRegistrationForm(e.value, input);
                      setCustomize(false);
                      onClose();
                    }}>{event?.labels?.REGISTRATION_FORM_UPDATE}</div>
                  </div>
                </div>
              </div>
            );
          },
        });
      } else if (e.value !== attendee.attendee_type && !customize) {
        loadRegistrationForm(e.value, input);
      }
    } else if (type === 'select') {
      const updateAttendee = {
        ...attendee,
        [input]: e.value
      }
      setAttendee(updateAttendee);
      setCustomize(true);
    } else if (type === 'multi-select') {
      const updateAttendee = {
        ...attendee,
        [input]: e
      }
      setAttendee(updateAttendee);
      setCustomize(true);
    } else if (type === 'custom_field') {
      const updateAttendee = {
        ...attendee,
        [input]: e.value
      }
      setAttendee(updateAttendee);
      setCustomFieldIds([...custom_field_ids, e.value]);
      setCustomize(true);
    } else if (type === 'datetime' && e !== undefined && e !== 'Invalid date' && e !== 'cleardate') {
      const date = moment(new Date(e)).tz(event.timezone.timezone).format('YYYY-MM-DD');
      const updateAttendee = {
        ...attendee,
        [input]: date
      }
      setAttendee(updateAttendee);
      setCustomize(true);
    } else {
      if (e.target.value === undefined) {
        const updateAttendee = {
          ...attendee,
          [input]: []
        }
        setAttendee(updateAttendee);
      } else {
        const updateAttendee = {
          ...attendee,
          [input]: e.target.value
        }
        setAttendee(updateAttendee);
        setCustomize(true);
      }
    }
  }

  const handleBlur = (input: any, type?: any) => (e: any) => {
    //Validations on change for specific fields
    if (in_array(input, ['email', 'confirm_email'])) {
      //simpleValidator.current.showMessageFor('confirm_email');
    }
  }

  const handleClick = (input: any, value: any) => (e: any) => {
    const updateAttendee = {
      ...attendee,
      [input]: value
    }
    setAttendee(updateAttendee);
  };

  const handleTos = (value: any) => {
    const updateAttendee = {
      ...attendee,
      'accept_gdpr': value
    }
    setAttendee(updateAttendee);
  };

  const handleFoodAllergies = (value: any) => {
    const updateAttendee = {
      ...attendee,
      'accept_foods_allergies': value
    }
    setAttendee(updateAttendee);
  };

  const handleDisclaimer = (value: any) => {
    const updateAttendee = {
      ...attendee,
      'cbkterms': value
    }
    setAttendee(updateAttendee);
  };

  const getSelectedLabel = (item: any, id: any) => {
    if (item && item.length > 0 && id) {
      const obj = item.find((o: any) => o.id.toString() === id.toString());
      return (obj ? obj.name : '');
    }
  }

  const handleSubmit = (evt: any) => {
    evt.preventDefault();
    const formValid = simpleValidator.current.allValid();
    if (!formValid) {
      simpleValidator.current.showMessages();
      setTimeout(() => {
        const scrollTo = document?.getElementsByClassName('error-message')[0];
        if (scrollTo !== undefined && scrollTo !== null) {
          scrollTo.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        }
      }, 500);
    } else if (!action) {
      setMessage({});
      setAction(true);
      service.post(props.order_id ? `${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/attendee-personal-information/${props.order_id}${props.attendee_id ? '/' + props.attendee_id : ''
        }?provider=${props?.provider}` : `${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/attendee-personal-information?provider=${props?.provider}&sale_id=${sale_id !== null ? sale_id : ''}`, { ...attendee, order_id: props.order_id, attendee_id: props.attendee_id, attendee_types: attendee_types !== null ? attendee_types : '' })
        .then(
          response => {
            if (mounted.current) {
              if (response.success) {
                updateRouteParams({ ...routeParams, page: 'registration-information', orderAttendee: response.data.order_attendee });
                if (((Number(props?.formSettings?.show_items) === 1 && Number(event?.eventsite_setting?.payment_type) === 0) || (Number(props?.formSettings?.skip_items_step) === 0 && Number(event?.eventsite_setting?.payment_type) === 1))) {
                  props.goToSection('manage-items', response.data.order.id, response.data.attendee_id);
                } else {
                  if (Number(props?.formSettings?.show_business_dating) === 1) {
                    props.goToSection('manage-keywords', response.data.order.id, response.data.attendee_id);
                  } else if (Number(props?.formSettings?.show_subregistration) === 1) {
                    props.goToSection('manage-sub-registrations', response.data.order.id, response.data.attendee_id);
                  } else if (Number(props?.formSettings?.show_required_documents) === 1) {
                    props.goToSection('manage-documents', response.data.order.id, response.data.attendee_id);
                  } else if (Number(props?.formSettings?.show_hotels) === 1) {
                    props.goToSection('manage-hotel-booking', response.data.order.id, response.data.attendee_id);
                  } else if (formBuilderForms.length > 0) {
                    props.goToSection('custom-forms', response.data.order.id, response.data.attendee_id, formBuilderForms[0].id);
                  } else {
                    completedAttendeeIteration(response.data.attendee_id, response.data.order.id);
                  }
                }
              } else {
                if (response?.errors !== undefined && Object.keys(response?.errors).length > 0) {
                  setErrors(response.errors);
                } else {
                  setMessage({ success: false, info: response?.message });
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

  const completedAttendeeIteration = (attendee_id: any, order_id: any) => {
    service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/completed-attendee-iteration`, { attendee_id: attendee_id, order_id: order_id, provider: props?.provider })
      .then(
        response => {
          if (mounted.current) {
            if (response.success) {
              history.push(`/${event.url}/${props?.provider}/order-summary/${order_id}`);
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
    if (errors !== undefined && Object.keys(errors).length > 0) {
      const scrollTo = document.getElementById(Object.keys(errors)[0]);
      if (scrollTo !== undefined && scrollTo !== null) {
        setTimeout(() => {
          scrollTo.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        }, 500);
      }
    }
  }, [errors]);

  const [city, setCity] = useState('');

  const postCodeHandler = useMemo(() => {
    return debounce(function (code: any) {
      fetch(
        `https://api.dataforsyningen.dk/postnumre/${code}`,
        {
          method: "GET",
          headers: header('GET'),
        }
      ).then(
        async response => {
          if (mounted.current) {
            if (response.status == 200) {
              const text = await response?.text();
              const data = text && JSON.parse(text);
              setCity(data.navn);
            } else {
              setCity('');
            }
          }
        },
        error => { }
      );

    }, 300);
  }, []);

  useEffect(() => {
    if (city) {
      const updateAttendee = {
        ...attendee,
        ['private_city']: city
      }
      setAttendee(updateAttendee);
    }
  }, [city])

  const getKeyValue = function <T, U extends keyof T>(obj: T, key: U) { return obj[key] !== undefined ? obj[key] : '' }

  simpleValidator?.current?.purgeFields();

  return (
    <div className={`${!in_array(section, ["manage-attendee", "autoregister", "registration-form"]) && 'tab-collapse'} wrapper-box`}>
      {loading && <Loader className='fixed' />}
      <React.Fragment>
        <header className="header-section">
          <h3 onClick={(e: any) => {
            if (props.order_id && props.attendee_id && !in_array(section, ["manage-attendee", "autoregister", "registration-form"])) {
              history.push(`/${event.url}/${props?.provider}/manage-attendee/${props.order_id}/${props.attendee_id}`);
            } else {
              setShow(!show)
            }
          }}>
            {event?.labels?.REGISTRATION_FORM_REGISTRATION_INFORMATION}
            <i className="material-icons"> {in_array(section, ["manage-attendee", "autoregister", "registration-form"]) && show ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}</i>
          </h3>
          <div className="icon-tick">
            {((location.pathname.toString().includes("/manage-attendee") || location.pathname.toString().includes("/manage-items") || location.pathname.toString().includes("/manage-keywords") || location.pathname.toString().includes("/manage-sub-registrations") || location.pathname.toString().includes("/manage-documents")) || props?.orderAttendee?.status === 'complete') ? (
              <img src={require('@/src/img/tick-green.svg')} alt="" />
            ) : (
              <img src={require('@/src/img/tick-grey.svg')} alt="" />
            )}
          </div>
        </header>
        {in_array(section, ["manage-attendee", "autoregister", "registration-form"]) && show && event?.event_description?.detail?.personal_description && (
          <p className="ebs-attendee-caption" dangerouslySetInnerHTML={{ __html: event?.event_description?.detail?.personal_description }}></p>
        )}
        {in_array(section, ["manage-attendee", "autoregister", "registration-form"]) && show && (
          <div className="wrapper-inner-content">
            <form onSubmit={handleSubmit}>

              {!message?.success && message?.info && (
                <div className="alert alert-danger" role="alert">
                  {message?.info}
                </div>
              )}

              {
                (() => {
                  if (getKeyValue(errors, 'general_error'))
                    return (
                      <div className="alert alert-danger" id="general_error">{getKeyValue(errors, 'general_error')}</div>
                    )
                  else if (stock_message)
                    return (
                      <div className="alert alert-danger" id="general_error">{stock_message}</div>
                    )
                })()
              }

              {(registration_forms?.length > 1 && (
                <div className="row d-flex justify-content-center mb-3">
                  <div className="col-6 ">
                    <React.Fragment>
                      <div className="header-box clearfix">
                        <h4 className="float-left">{event?.labels?.REGISTRATION_FORM_ATTENDEE_TYPE}</h4>
                      </div>
                      <DropDown
                        label={event?.labels?.REGISTRATION_FORM_ATTENDEE_TYPE}
                        listitems={registration_forms}
                        selected={getKeyValue(attendee, 'attendee_type')}
                        selectedlabel={getSelectedLabel(registration_forms, getKeyValue(attendee, 'attendee_type'))}
                        onChange={handleChange('attendee_type', 'select')}
                        required={true}
                        isDisabled={(section === 'autoregister' || (in_array(props?.provider, ['attendee', 'embed']) && Number(event?.payment_setting?.allow_change_attendee_type) === 0 && props.attendee_id !== undefined) || Number(order?.is_waitinglist) === 1) ? true : false}
                        placeholder={event?.labels?.REGISTRATION_FORM_SEARCH}
                      />
                      {simpleValidator.current.message('attendee_type', getKeyValue(attendee, 'attendee_type'), 'required')}
                      {getKeyValue(errors, 'attendee_type') && <p className="errormessage">{getKeyValue(errors, 'attendee_type')}</p>}
                    </React.Fragment>
                  </div>
                </div>
              ))}

              {Number(attendee?.attendee_type) > 0 && (
                <>
                  {sections && sections.map((section: any, key: any) => (
                    !in_array(section.field_alias, ["company_detail", "po_number", "attendee_type_head"]) && (
                      <div className="row d-flex justify-content-center mb-3" key={key}>
                        {section.fields?.length > 0 && (
                          <div className="col-6 ">
                            <div className="header-box clearfix">
                              <h4 className="float-left">{section?.detail?.name}</h4>
                            </div>
                            {section.fields && section.fields.map((field: any, index: any) => {
                              return (
                                <React.Fragment key={index}>
                                  {
                                    (() => {
                                      if (in_array(field.field_alias, ["private_country", "company_country", "country"]))
                                        return (
                                          <React.Fragment>
                                            <DropDown
                                              label={field.detail.name}
                                              listitems={countries}
                                              selected={getKeyValue(attendee, field.field_alias) ? getKeyValue(attendee, field.field_alias) : event.country_id}
                                              selectedlabel={getSelectedLabel(countries, getKeyValue(attendee, field.field_alias) ? getKeyValue(attendee, field.field_alias) : event.country_id)}
                                              onChange={handleChange(field.field_alias, 'select')}
                                              required={(Number(field.mandatory) === 1 ? true : false)}
                                              placeholder={event?.labels?.REGISTRATION_FORM_SEARCH}
                                            />
                                            {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required')}
                                            {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}
                                          </React.Fragment>
                                        )
                                      else if (in_array(field.field_alias, ["SPOKEN_LANGUAGE"]))
                                        return (
                                          <React.Fragment>
                                            <DropDown
                                              label={field.detail.name}
                                              listitems={languages}
                                              selected={getKeyValue(attendee, field.field_alias)}
                                              onChange={handleChange(field.field_alias, 'multi-select')}
                                              required={(Number(field.mandatory) === 1 ? true : false)}
                                              isMulti={true}
                                              placeholder={event?.labels?.REGISTRATION_FORM_SEARCH}
                                            />
                                            {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required')}
                                            {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}
                                          </React.Fragment>
                                        )
                                      else if (in_array(field.field_alias, ["about", "interests"]))
                                        return (
                                          <React.Fragment>
                                            <TextArea
                                              onChange={handleChange(field.field_alias, 'text')}
                                              label={field.detail.name}
                                              value={getKeyValue(attendee, field.field_alias)}
                                              required={(Number(field.mandatory) === 1 ? true : false)}
                                              className={getKeyValue(errors, field.field_alias) ? "error" : ""}
                                              autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                            />
                                            {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required')}
                                            {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}
                                          </React.Fragment>
                                        )
                                      else if (in_array(field.field_alias, ["password", "confirm_password"]))
                                        return (
                                          <React.Fragment>
                                            {!props.attendee_id && (
                                              <React.Fragment>
                                                <Input
                                                  onChange={handleChange(field.field_alias, 'text')}
                                                  type="password"
                                                  field={`field-${field.field_alias}`}
                                                  label={field.detail.name}
                                                  className={`${getKeyValue(errors, field.field_alias) ? "error" : "ebs-completed-arrow"} ${getKeyValue(attendee, field.field_alias) && 'ebs-input-verified'}`}
                                                  value={getKeyValue(attendee, field.field_alias)}
                                                  required={(Number(field.mandatory) === 1 ? true : false)}
                                                  autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                                />
                                                {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required')}
                                                {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}
                                              </React.Fragment>
                                            )}
                                          </React.Fragment>
                                        )
                                      else if (in_array(field.field_alias, ["phone"]))
                                        return (
                                          <React.Fragment>
                                            <div className='form-phone-field'>
                                              <DropDown
                                                listitems={country_codes}
                                                selected={getKeyValue(attendee, "calling_code_phone") ? getKeyValue(attendee, "calling_code_phone") : event_country_code}
                                                selectedlabel={getSelectedLabel(country_codes, getKeyValue(attendee, "calling_code_phone") ? getKeyValue(attendee, "calling_code_phone") : event_country_code)}
                                                onChange={handleChange("calling_code_phone", 'select')}
                                                required={false}
                                                placeholder={event?.labels?.REGISTRATION_FORM_SEARCH}
                                              />
                                              <Input
                                                onChange={handleChange(field.field_alias, 'text')}
                                                type="text"
                                                countryCode={getKeyValue(attendee, "calling_code_phone") ? getKeyValue(attendee, "calling_code_phone") : event_country_code}
                                                field={`field-${field.field_alias}`}
                                                label={field.detail.name}
                                                value={getKeyValue(attendee, field.field_alias)}
                                                className={`${getKeyValue(attendee, field.field_alias) && 'ebs-input-verified'}`}
                                                required={(Number(field.mandatory) === 1 ? true : false)}
                                                autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                              />
                                            </div>
                                            {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required')}
                                            {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}
                                          </React.Fragment>
                                        )
                                      else if (in_array(field.field_alias, ["BIRTHDAY_YEAR", "EMPLOYMENT_DATE", "date_of_issue_passport", "date_of_expiry_passport"]))
                                        return (
                                          <React.Fragment>
                                            <DateTime
                                              onChange={handleChange(field.field_alias, 'datetime')}
                                              label={field.detail.name}
                                              value={getKeyValue(attendee, field.field_alias)}
                                              required={(Number(field.mandatory) === 1 ? true : false)}
                                              showdate={'YYYY-MM-DD'}
                                              locale={getLanguageCode(event.language_id)}
                                            />
                                            {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required')}
                                            {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}
                                          </React.Fragment>
                                        )
                                      else if (in_array(field.field_alias, ["gender"]))
                                        return (
                                          <React.Fragment>
                                            <div className='inline radio-check-field style-radio'>
                                              <h5>{field.detail.name} {Number(field.mandatory) === 1 && <em className="req">*</em>} </h5>
                                              <label onClick={handleClick('gender', "male")} className={getKeyValue(attendee, "gender") === "male" ? 'checked' : ''}><span>Male</span></label>
                                              <label onClick={handleClick('gender', "female")} className={getKeyValue(attendee, 'gender') === "female" ? 'checked' : ''}><span>Female</span></label>
                                            </div>
                                            {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required')}
                                            {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}
                                          </React.Fragment>
                                        )
                                      else if (in_array(field.field_alias, ["custom_field_id"]) && custom_fields?.length > 0)
                                        return (
                                          <React.Fragment>
                                            <h5 className="mt-4">{field.detail.name}</h5>
                                            {custom_fields && custom_fields.map((custom_field: any, custom_field_key) => (
                                              <React.Fragment key={custom_field_key}>
                                                {custom_field?.allow_multiple ? (
                                                  <DropDown
                                                    key={custom_field_key}
                                                    label={custom_field.name}
                                                    listitems={custom_field.children_recursive}
                                                    selected={getKeyValue(attendee, `custom-field-${custom_field.id}`)}
                                                    onChange={handleChange(`custom-field-${custom_field.id}`, 'multi-select')}
                                                    required={(Number(field.mandatory) === 1 ? true : false)}
                                                    placeholder={event?.labels?.REGISTRATION_FORM_SEARCH}
                                                    isMulti={true}
                                                  />
                                                ) : (
                                                  <DropDown
                                                    key={custom_field_key}
                                                    label={custom_field.name}
                                                    listitems={custom_field.children_recursive}
                                                    selected={getKeyValue(attendee, `custom-field-${custom_field.id}`)}
                                                    selectedlabel={getSelectedLabel(custom_field.children_recursive, getKeyValue(attendee, `custom-field-${custom_field.id}`))}
                                                    onChange={handleChange(`custom-field-${custom_field.id}`, 'custom_field')}
                                                    required={(Number(field.mandatory) === 1 ? true : false)}
                                                    placeholder={event?.labels?.REGISTRATION_FORM_SEARCH}
                                                  />
                                                )}
                                                {Number(field.mandatory) === 1 && simpleValidator.current.message(`custom-field-${custom_field.id}`, getKeyValue(attendee, `custom-field-${custom_field.id}`), 'required')}
                                                {getKeyValue(errors, `custom-field-${custom_field.id}`) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, `custom-field-${custom_field.id}`)}</p>}
                                              </React.Fragment>
                                            ))}
                                          </React.Fragment>
                                        )
                                      else if (in_array(field.field_alias, ["email"]))
                                        return (
                                          <React.Fragment>

                                            <Input
                                              onChange={handleChange(field.field_alias, 'text')}
                                              onBlur={handleBlur(field.field_alias, 'text')}
                                              type="text"
                                              field={`field-${field.field_alias}`}
                                              label={field.detail.name}
                                              readOnly={order?.platform === 'eventsite' && props.attendee_id ? true : false}
                                              value={getKeyValue(attendee, field.field_alias)}
                                              className={`${getKeyValue(attendee, field.field_alias) && 'ebs-input-verified'}`}
                                              required={Number(field.mandatory) === 1 ? true : false}
                                              autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                            />
                                            {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required|email')}
                                            {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}

                                            <Input
                                              onChange={handleChange('confirm_email', 'text')}
                                              onBlur={handleBlur('confirm_email', 'text')}
                                              type="text"
                                              readOnly={order?.platform === 'eventsite' && props.attendee_id ? true : false}
                                              field={`field-${'confirm_email'}`}
                                              label={event?.labels?.REGISTRATION_FORM_CONFIRM_EMAIL}
                                              value={getKeyValue(attendee, 'confirm_email')}
                                              className={`${getKeyValue(attendee, 'confirm_email') && 'ebs-input-verified'}`}
                                              required={Number(field.mandatory) === 1 ? true : false}
                                              autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                            />

                                            {Number(field.mandatory) === 1 && simpleValidator.current.message('confirm_email', getKeyValue(attendee, 'confirm_email'), getKeyValue(attendee, 'email') ? 'required|email|in:' + getKeyValue(attendee, 'email') : 'required|email')}
                                            {getKeyValue(errors, 'confirm_email') && <p className="error-message" id={'confirm_email'}>{getKeyValue(errors, 'confirm_email')}</p>}

                                          </React.Fragment>
                                        )
                                      else if (!in_array(field.field_alias, ["attendee_type", "custom_field_id"]))
                                        return (
                                          <React.Fragment>
                                            {
                                              (() => {
                                                if (field.field_alias === "private_post_code") {
                                                  return (
                                                    <Input
                                                      onChange={(e: any) => {
                                                        const updateAttendee = {
                                                          ...attendee,
                                                          ['private_post_code']: e.target.value
                                                        }
                                                        setAttendee(updateAttendee);
                                                        postCodeHandler(e.target.value);
                                                      }}
                                                      type="text"
                                                      field={`field-${field.field_alias}`}
                                                      label={field.detail.name}
                                                      value={getKeyValue(attendee, field.field_alias)}
                                                      className={`${getKeyValue(attendee, field.field_alias) && 'ebs-input-verified'}`}
                                                      required={Number(field.mandatory) === 1 ? true : false}
                                                      autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                                    />
                                                  )
                                                }
                                                else {
                                                  return (
                                                    <Input
                                                      onChange={handleChange(field.field_alias, 'text')}
                                                      type="text"
                                                      field={`field-${field.field_alias}`}
                                                      label={field.detail.name}
                                                      value={getKeyValue(attendee, field.field_alias)}
                                                      className={`${getKeyValue(attendee, field.field_alias) && 'ebs-input-verified'}`}
                                                      required={Number(field.mandatory) === 1 ? true : false}
                                                      autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                                    />
                                                  )
                                                }
                                              })()
                                            }
                                            {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required')}
                                            {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}
                                          </React.Fragment>
                                        )
                                    })()
                                  }
                                </React.Fragment>
                              )
                            })}
                          </div>
                        )}
                      </div>
                    )
                  ))}

                  {Number(event?.gdpr_setting?.enable_gdpr) === 1 && (
                    <div className="row d-flex justify-content-center mb-3">
                      <div className="col-6 ">
                        <div className='radio-check-field ebs-radio-lg field-terms-services'>
                          <label className={Number(attendee?.accept_gdpr) === 1 || (Number(event?.gdpr_setting?.auto_selected) === 1 && props.attendee_id === undefined) ? 'checked' : ''} onClick={() => {
                            handleTos(Number(attendee?.accept_gdpr) === 1 ? 0 : 1);
                          }}><span>{gdpr?.inline_text} <mark onClick={(e: any) => {
                            e.preventDefault();
                            e.stopPropagation();
                            setPopup(true);
                          }}>{gdpr?.purchase_policy_link_text}</mark></span></label>
                        </div>

                        {Number(event?.gdpr_setting?.gdpr_required) === 1 && Number(event?.gdpr_setting?.auto_selected) === 0 && props.attendee_id === undefined && <div style={{ display: 'block' }}>
                          {simpleValidator.current.message('accept_gdpr', (Number(getKeyValue(attendee, 'accept_gdpr')) === 1 ? getKeyValue(attendee, 'accept_gdpr') : ''), 'required')}
                        </div>}
                      </div>
                    </div>
                  )}

                  {Number(event?.attendee_setting?.enable_foods) === 1 && (
                    <div className="row d-flex justify-content-center mb-3">
                      <div className="col-6 ">
                        <div className='radio-check-field ebs-radio-lg field-terms-services'>
                          <label className={Number(attendee?.accept_foods_allergies) === 1 ? 'checked' : ''} onClick={() => {
                            handleFoodAllergies(Number(attendee?.accept_foods_allergies) === 1 ? 0 : 1);
                          }}><span>{food?.inline_text} <mark onClick={(e: any) => {
                            e.preventDefault();
                            e.stopPropagation();
                            setFoodModal(true);
                          }}>{food?.food_link}</mark></span></label>
                        </div>
                      </div>
                    </div>
                  )}

                  {Number(event?.event_disclaimer_setting?.reg_site) === 1 && disclaimer && (
                    <div className="row d-flex justify-content-center mb-3">
                      <div className="col-6 ">
                        <div className='radio-check-field ebs-radio-lg field-terms-services'>
                          <label className={Number(attendee?.cbkterms) === 1 ? 'checked' : ''} onClick={() => {
                            handleDisclaimer(Number(attendee?.cbkterms) === 1 ? 0 : 1);
                          }}><span>{event?.labels?.EVENTSITE_TERM_AGREE} <mark onClick={(e: any) => {
                            e.preventDefault();
                            e.stopPropagation();
                            setDisclaimerModal(true);
                          }}>{event?.labels?.EVENTSITE_TERMANDCONDITIONS}</mark></span></label>
                        </div>
                        <div style={{ display: 'none' }}>
                          {simpleValidator.current.message('cbkterms', (Number(getKeyValue(attendee, 'cbkterms')) === 1 ? getKeyValue(attendee, 'cbkterms') : ''), 'required')}
                        </div>
                      </div>
                    </div>
                  )}

                  {subscribers?.subscriber_list?.length > 0 && Number(subscribers?.status) === 1 && (
                    <div className="ebs-bottom-description-box">
                      <div className="row d-flex justify-content-center">
                        <div className="col-12 ">
                          <div className="header-box clearfix">
                            <h4 className="float-left">{event?.labels?.REGISTRATION_FORM_NEWSLETTER_HEADING}
                              <em data-tip="abc" className="app-tooltip inline-tooltip">
                                <i className="material-icons">info</i>
                              </em>
                            </h4>
                          </div>
                          <p className="ebs-attendee-caption">{event?.labels?.REGISTRATION_FORM_NEWSLETTER_DESCRIPTOIN}</p>
                          {subscribers?.subscriber_list?.map((row: any, key: any) =>
                            <div className="mb-3" key={key}>
                              <div className='radio-check-field ebs-radio-lg field-terms-services'>
                                <label className={in_array(Number(row?.id), attendee?.subscriber_ids) ? 'checked' : ''} onClick={() => {
                                  setAttendee({
                                    ...attendee,
                                    subscriber_ids: toggleValueInArray(attendee?.subscriber_ids, row?.id)
                                  });
                                }}><span dangerouslySetInnerHTML={{ __html: row?.name }} ></span></label>
                              </div>
                            </div>
                          )}
                        </div>
                      </div>
                    </div>
                  )}

                  <div className="bottom-button text-center">
                    {props?.orderAttendee?.status === 'complete' && (
                      <a onClick={() => {
                        if (((Number(props?.formSettings?.show_items) === 1 && Number(event?.eventsite_setting?.payment_type) === 0) || (Number(props?.formSettings?.skip_items_step) === 0 && Number(event?.eventsite_setting?.payment_type) === 1))) {
                          props.goToSection('manage-items', props.order_id, props.attendee_id);
                        } else {
                          if (Number(props?.formSettings?.show_business_dating) === 1) {
                            props.goToSection('manage-keywords', props.order_id, props.attendee_id);
                          } else if (Number(props?.formSettings?.show_subregistration) === 1) {
                            props.goToSection('manage-sub-registrations', props.order_id, props.attendee_id);
                          } else if (Number(props?.formSettings?.show_required_documents) === 1) {
                            props.goToSection('manage-documents', props.order_id, props.attendee_id);
                          } else if (Number(props?.formSettings?.show_hotels) === 1) {
                            props.goToSection('manage-hotel-booking', props.order_id, props.attendee_id);
                          } else {
                            history.push(`/${event.url}/${props?.provider}/order-summary/${props.order_id}`);
                          }
                        }
                      }} className="btn btn-cancel">{event?.labels?.REGISTRATION_FORM_SKIP} <i className="material-icons">keyboard_arrow_right</i></a>
                    )}
                    <button
                      type="submit"
                      disabled={(!simpleValidator.current.fieldValid('cbkterms') && (Number(event?.event_disclaimer_setting?.reg_site) === 1 && disclaimer)) ? true : false}
                      className="btn btn-save-next btn-loader">
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
                </>
              )}

              {popup &&
                <Popup
                  onClick={() => {
                    setPopup(false);
                  }}
                  title={gdpr?.subject}>
                  <div className="ebs-popup-content">
                    <div dangerouslySetInnerHTML={{ __html: gdpr?.description }}></div>
                    <div className="ebs-popup-buttons text-center">
                      <div className="btn bordered" onClick={() => {
                        handleTos(0);
                        setPopup(false);
                      }}>{event?.labels?.GDPR_CANCEL}</div>
                      <div className="btn btn-primary" onClick={() => {
                        handleTos(1);
                        setPopup(false);
                      }}>{event?.labels?.GDPR_ACCEPT}</div>
                    </div>
                  </div>
                </Popup>}


              {foodModal &&
                <Popup
                  onClick={() => {
                    setFoodModal(false);
                  }}
                  title={food?.subject}>
                  <div className="ebs-popup-content">
                    <div dangerouslySetInnerHTML={{ __html: food?.description }}></div>
                    <div className="ebs-popup-buttons text-center">
                      <div className="btn bordered" onClick={() => {
                        setFoodModal(false);
                      }}>{event?.labels?.GENERAL_CANCEL}</div>
                      <div className="btn btn-primary" onClick={() => {
                        handleFoodAllergies(1);
                        setFoodModal(false);
                      }}>{event?.labels?.GENERAL_I_AGREE}</div>
                    </div>
                  </div>
                </Popup>}

              {disclaimerModal &&
                <Popup
                  onClick={() => {
                    setDisclaimerModal(false)
                  }}
                  title={event?.labels?.EVENTSITE_TERMANDCONDITIONS}>
                  <div className="ebs-popup-content">
                    <div dangerouslySetInnerHTML={{ __html: disclaimer }}></div>
                    <div className="ebs-popup-buttons text-center">
                      <div className="btn bordered" onClick={() => {
                        setDisclaimerModal(false);
                      }}>{event?.labels?.GENERAL_CANCEL}</div>
                      <div className="btn btn-primary" onClick={() => {
                        handleDisclaimer(1);
                        setDisclaimerModal(false);
                      }}>{event?.labels?.GENERAL_I_AGREE}</div>
                    </div>
                  </div>
                </Popup>}
            </form>
          </div>
        )}
      </React.Fragment>
    </div>
  );
};

export default ManageAttendee;