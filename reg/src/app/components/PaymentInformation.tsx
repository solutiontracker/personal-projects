import React, { ReactElement, FC, useState, useEffect, useRef, useContext, useMemo } from "react";
import { useParams, useHistory, Link } from 'react-router-dom';
import Input from '@/src/app/components/forms/Input';
import DropDown from '@/src/app/components/forms/DropDown';
import { service } from '@/src/app/services/service';
import Event from '@/src/app/components/event/interface/Event';
import Loader from '@/src/app/components/forms/Loader';
import SimpleReactValidator from 'simple-react-validator';
import { EventContext } from "@/src//app/context/event/EventProvider";
import Popup from '@/src/app/components/forms/Popup';
import in_array from "in_array";
import debounce from 'lodash.debounce';
import { header, postMessage } from "@/src/app/helpers";

type Params = {
  url: any;
  provider: any;
  order_id?: any;
  section: string;
};

const PaymentInformation: FC<any> = (props: any): ReactElement => {

  const history = useHistory();

  const { order_id, provider } = useParams<Params>();

  const [, forceUpdate] = useState(0);

  const mounted = useRef(false);

  const { event, updateEvent, updateRouteParams } = useContext<any>(EventContext);

  const simpleValidator = useRef(new SimpleReactValidator({
    element: (message: any) => <p className="error-message">{message}</p>,
    messages: {
      required: event?.labels?.REGISTRATION_FORM_FIELD_REQUIRED,
      email: event?.labels?.REGISTRATION_FORM_CONFIRM_EMAIL_MATCH,
      in: event?.labels?.REGISTRATION_FORM_CONFIRM_EMAIL_MATCH
    },
    autoForceUpdate: { forceUpdate: () => forceUpdate(1) }
  }))

  const [attendee, setAttendeeBilling] = useState<any>({
    company_type: "invoice",
    company_registration_number: "",
    contact_person_name: "",
    contact_person_email: "",
    calling_code_contact_person_mobile_number: "",
    contact_person_mobile_number: "",
    company_street: "",
    company_house_number: "",
    company_post_code: 0,
    company_city: 0,
    company_country: 0,
    poNumber: ""
  });

  const [sections, setSections] = useState([]);

  const [countries, setCountries] = useState([]);

  const [country_codes, setCountryCodes] = useState([]);

  const [event_country_code, setEventCountryCode] = useState([]);

  const [loading, setLoading] = useState(true);

  const [action, setAction] = useState('');

  const [errors, setErrors] = useState<any>({});

  const [cvr, setCvr] = useState(false);

  const [validate_cvr, setValidateCvr] = useState(0);

  const [ean, setEan] = useState(false);

  const [validate_ean, setValidateEan] = useState(0);

  const [validate_burger_id, setBurgerID] = useState(0);

  const [po_number, setPoNumber] = useState(false);

  const [validate_po_number, setValidatePoNumber] = useState(0);

  const params = useParams<Params>();

  useEffect(() => {
    mounted.current = true;
    postMessage({ page: 'payment-information' });
    updateRouteParams({ ...params, page: 'payment-information' });
    return () => {
      mounted.current = false;
      validateCvrHandler.cancel();
      postCodeHandler.cancel();
      payerPostCodeHandler.cancel();
      validateEanHandler.cancel();
      validatePoNumberHandler.cancel();
    };
  }, []);

  useEffect(() => {
    service.get(order_id ? `${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/payment-information/${order_id}?provider=${provider}` : `${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/payment-information?provider=${provider}`)
      .then(
        response => {
          if (response.success && mounted.current) {
            if (response?.data?.order?.order_detail?.order?.status !== "completed" || (response?.data?.order?.order_detail?.order?.status === "completed" && (Number(response?.data?.order?.order_detail?.order?.is_waitinglist) === 1 || in_array(provider, ['sale', 'admin'])))) {
              setAttendeeBilling(response.data.attendee_billing);
              setSections(response.data.sections);
              setCountries(response.data.metadata.countries);
              setCountryCodes(response.data.metadata.country_codes);
              setEventCountryCode(response.data.metadata.event_country_code);
              setLoading(false);

              //Update event info
              updateEvent({
                ...event,
                order: response?.data?.order
              });
            } else {
              history.push(`/${event.url}/${provider}`);
            }
          }
        },
        error => { }
      );
  }, []);

  const handleChange = (input: any, type?: any) => (e: any) => {
    if (type === 'select') {
      const updateAttendee = {
        ...attendee,
        [input]: e.value
      }
      setAttendeeBilling(updateAttendee);
    } else {
      if (e.target.value === undefined) {
        const updateAttendee = {
          ...attendee,
          [input]: []
        }
        setAttendeeBilling(updateAttendee);
      } else {
        const updateAttendee = {
          ...attendee,
          [input]: e.target.value
        }
        setAttendeeBilling(updateAttendee);
      }
    }
  }

  const validateCvrHandler = useMemo(() => {
    return debounce(function (cvr: any) {
      setAction('validate-cvr');
      service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/validate-cvr`, { cvr: cvr })
        .then(
          response => {
            if (mounted.current) {
              if (!response.success) {
                setCvr(true)
              }
              setAction('');
              setErrors({});
            }
          },
          error => { }
        );
    }, 1000);
  }, []);

  const validateEanHandler = useMemo(() => {
    return debounce(function (ean: any) {
      setAction('validate-ean');
      service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/validate-ean`, { ean: ean })
        .then(
          response => {
            if (mounted.current) {
              if (Number(response?.status) === 1) {
                if (response?.show_billing_bruger_id) {
                  setBurgerID(1);
                } else {
                  setBurgerID(0);
                }
                setEan(false)
              } else {
                setBurgerID(0);
                setEan(true)
              }
              setAction('');
              setErrors({});
            }
          },
          error => { }
        );
    }, 1000);
  }, []);

  const validatePoNumberHandler = useMemo(() => {
    return debounce(function (poNumber: any) {
      setAction('validate-ponumber');
      service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/validate-po-number`, { poNumber: poNumber })
        .then(
          response => {
            if (mounted.current) {
              if (!response.success) {
                setPoNumber(true);
              } else {
                setPoNumber(false);
              }
              setAction('');
              setErrors({});
            }
          },
          error => { }
        );
    }, 1000);
  }, []);

  const handleClick = (input: any, value: any) => (e: any) => {
    const updateAttendee = {
      ...attendee,
      [input]: value
    }
    setAttendeeBilling(updateAttendee);
  };

  const getSelectedLabel = (item: any, id: any) => {
    if (item && item.length > 0 && id) {
      const obj = item.find((o: any) => o.id.toString() === id.toString());
      return (obj ? obj.name : '');
    }
  }

  const handleSubmit = (evt: any) => {
    evt.preventDefault();
    const formValid = simpleValidator.current.allValid()
    if (!formValid) {
      simpleValidator.current.showMessages();
      setTimeout(() => {
        const scrollTo = document?.getElementsByClassName('error-message')[0];
        if (scrollTo !== undefined && scrollTo !== null) {
          scrollTo.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        }
      }, 500);
    } else if (!action) {
      setAction('submit');
      service.post(order_id ? `${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/payment-information/${order_id}` : `${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/payment-information`, { ...attendee, order_id: order_id, validate_cvr: validate_cvr, validate_ean: validate_ean, validate_burger_id: validate_burger_id, validate_po_number: validate_po_number, provider: provider })
        .then(
          response => {
            if (mounted.current) {
              if (response.success) {
                history.push(`/${event.url}/${provider}/order-summary/${order_id}`);
              } else {
                setErrors(response.errors);
              }
              setAction('');
            }
          },
          error => {
            setAction('');
          }
        );
    }
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

  const [payer_city, setPayerCity] = useState('');

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

  const payerPostCodeHandler = useMemo(() => {
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
              setPayerCity(data.navn);
            } else {
              setPayerCity('');
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
        ['company_city']: city
      }
      setAttendeeBilling(updateAttendee);
    }
  }, [city])

  useEffect(() => {
    if (payer_city) {
      const updateAttendee = {
        ...attendee,
        ['company_invoice_payer_city']: payer_city
      }
      setAttendeeBilling(updateAttendee);
    }
  }, [payer_city])

  const getKeyValue = function <T, U extends keyof T>(obj: T, key: U) { return obj[key] !== undefined ? obj[key] : '' }

  const tooltips: any = {
    'company_registration_number': "Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content.",
    'ean': "Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content."
  };

  return (
    <React.Fragment>
      <div className="row d-flex ebs-title-box align-items-center">
        <div className="col-6"><h2 className="section-title">{event?.labels?.REGISTRATION_FORM_PAYMENT_INFORMATION}</h2></div>
        {Number(event?.order?.order_detail?.order?.edit_mode) === 1 && (
          <div className="col-6 text-right">
            <Link to={`/${event.url}/${provider}/order-summary/${order_id}`} className="ebs-back-summary"><i className="material-icons">keyboard_backspace</i>{event?.labels?.REGISTRATION_FORM_BACK_TO_SUMMARY}</Link>
          </div>
        )}
      </div>
      <div className="row">
        <div className="col-12">
          <p className="ebs-attendee-caption">{event?.labels?.REGISTRATION_FORM_PAYMENT_INFORMATION_DESCRIPTION}</p>
        </div>
      </div>
      <div className="wrapper-box">
        {loading && <Loader className='fixed' />}
        <React.Fragment>
          <header className="header-section">
            <h3>{event?.labels?.REGISTRATION_FORM_PAYMENT_INFORMATION}</h3>
          </header>
          {event?.event_description?.detail?.personal_description && (
            <p className="ebs-attendee-caption" dangerouslySetInnerHTML={{ __html: event?.event_description?.detail?.personal_description }}></p>
          )}
          <div className="wrapper-inner-content">
            <form onSubmit={handleSubmit}>
              {sections && sections.map((section: any, key: any) => (
                in_array(section.field_alias, ["company_detail", "po_number"]) && section.fields?.length > 0 && (
                  <div className="row d-flex justify-content-center mb-3" key={key}>
                    <div className="col-6 ">
                      <div className="header-box clearfix">
                        <h4 className="float-left">{section?.detail?.name}</h4>
                      </div>
                      {section.fields && section.fields.map((field: any, index: any) => {
                        return (
                          <React.Fragment key={index}>
                            {
                              (() => {
                                if (in_array(field.field_alias, ["company_country"]))
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
                                else if (in_array(field.field_alias, ["contact_person_mobile_number"]))
                                  return (
                                    <React.Fragment>
                                      <div className='form-phone-field'>
                                        <DropDown
                                          listitems={country_codes}
                                          selected={getKeyValue(attendee, "calling_code_contact_person_mobile_number") ? getKeyValue(attendee, "calling_code_contact_person_mobile_number") : event_country_code}
                                          selectedlabel={getSelectedLabel(country_codes, getKeyValue(attendee, "calling_code_contact_person_mobile_number") ? getKeyValue(attendee, "calling_code_contact_person_mobile_number") : event_country_code)}
                                          onChange={handleChange("calling_code_contact_person_mobile_number", 'select')}
                                          required={false}
                                          placeholder={event?.labels?.REGISTRATION_FORM_SEARCH}
                                        />
                                        <Input
                                          onChange={handleChange(field.field_alias, 'text')}
                                          type="text"
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
                                else if (in_array(field.field_alias, ["company_type"]))
                                  return (
                                    <React.Fragment>
                                      <div className='inline radio-check-field style-radio'>
                                        <h5>{field.detail.name}</h5>
                                        <React.Fragment>
                                          {sections && sections.map((company_type_section: any) => (
                                            company_type_section.fields && company_type_section.fields.map((company_type_field: any, company_type_index: any) => {
                                              return (
                                                in_array(company_type_field.field_alias, ["company_invoice_payment", "credit_card_payment", "company_public_payment"]) && (
                                                  <React.Fragment key={company_type_index}>
                                                    {
                                                      (() => {
                                                        if (company_type_field.field_alias === "company_invoice_payment")
                                                          return <label onClick={handleClick(field.field_alias, 'invoice')} key={company_type_index} className={getKeyValue(attendee, field.field_alias) === "invoice" ? 'checked' : ''}><span>{company_type_field.detail.name}</span></label>
                                                        else if (company_type_field.field_alias === "credit_card_payment" && provider !== "sale")
                                                          return <label onClick={handleClick(field.field_alias, 'private')} key={company_type_index} className={getKeyValue(attendee, field.field_alias) === "private" ? 'checked' : ''}><span>{company_type_field.detail.name}</span></label>
                                                        else if (company_type_field.field_alias === "company_public_payment")
                                                          return <label onClick={handleClick(field.field_alias, 'public')} key={company_type_index} className={getKeyValue(attendee, field.field_alias) === "public" ? 'checked' : ''}><span>{company_type_field.detail.name}</span></label>
                                                      })()
                                                    }
                                                  </React.Fragment>
                                                )
                                              )
                                            })
                                          ))}
                                        </React.Fragment>
                                      </div>
                                      <React.Fragment>
                                        {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required')}
                                        {
                                          (() => {
                                            if (getKeyValue(errors, 'company_invoice_payment'))
                                              return <p className="error-message" id={'company_invoice_payment'}>{getKeyValue(errors, 'company_invoice_payment')}</p>
                                            else if (getKeyValue(errors, 'credit_card_payment'))
                                              return <p className="error-message" id={'credit_card_payment'}>{getKeyValue(errors, 'credit_card_payment')}</p>
                                            else if (getKeyValue(errors, 'credit_card_payment'))
                                              return <p className="error-message" id={'credit_card_payment'}>{getKeyValue(errors, 'credit_card_payment')}</p>
                                          })()
                                        }
                                      </React.Fragment>
                                    </React.Fragment>
                                  )
                                else if (in_array(field.field_alias, ["contact_person_email"]))
                                  return (
                                    <React.Fragment>

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
                                      {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), 'required|email')}
                                      {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}

                                      <Input
                                        onChange={handleChange('contact_person_confirm_email', 'text')}
                                        type="text"
                                        field={`field-${'contact_person_confirm_email'}`}
                                        label={event?.labels?.REGISTRATION_FORM_CONTACT_PERSON_CONFIRM_EMAIL}
                                        value={getKeyValue(attendee, 'contact_person_confirm_email')}
                                        className={`${getKeyValue(attendee, 'contact_person_confirm_email') && 'ebs-input-verified'}`}
                                        required={Number(field.mandatory) === 1 ? true : false}
                                        autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                      />

                                      {Number(field.mandatory) === 1 && simpleValidator.current.message('contact_person_confirm_email', getKeyValue(attendee, 'contact_person_confirm_email'), 'required|email')}
                                      {getKeyValue(errors, 'contact_person_confirm_email') && <p className="error-message" id={'contact_person_confirm_email'}>{getKeyValue(errors, 'contact_person_confirm_email')}</p>}

                                    </React.Fragment>
                                  )
                                else if ((!in_array(field.field_alias, ["custom_field_id", "credit_card_payment", "company_invoice_payment", "company_public_payment", "ean"])) || (field.field_alias === "ean" && attendee?.company_type === "public"))
                                  return (
                                    <React.Fragment>
                                      {
                                        (() => {
                                          if (field.field_alias === "company_registration_number") {
                                            return (
                                              <Input
                                                onChange={(e: any) => {
                                                  const updateAttendee = {
                                                    ...attendee,
                                                    ['company_registration_number']: e.target.value
                                                  }
                                                  setAttendeeBilling(updateAttendee);
                                                  validateCvrHandler(e.target.value);
                                                }}
                                                type="text"
                                                tooltip={tooltips?.[field?.field_alias] === undefined ? tooltips?.[field?.field_alias] : ''}
                                                field={`field-${field.field_alias}`}
                                                label={field.detail.name}
                                                value={getKeyValue(attendee, field.field_alias)}
                                                className={`${getKeyValue(attendee, field.field_alias) && 'ebs-input-verified'}`}
                                                required={Number(field.mandatory) === 1 ? true : false}
                                                autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                              />
                                            )
                                          } else if (field.field_alias === "poNumber") {
                                            return (
                                              <Input
                                                onChange={(e: any) => {
                                                  const updateAttendee = {
                                                    ...attendee,
                                                    ['poNumber']: e.target.value
                                                  }
                                                  setAttendeeBilling(updateAttendee);
                                                  if (validate_burger_id || attendee?.bruger_id) {
                                                    validatePoNumberHandler(e.target.value);
                                                  }
                                                }}
                                                type="text"
                                                tooltip={tooltips?.[field?.field_alias] === undefined ? tooltips?.[field?.field_alias] : ''}
                                                field={`field-${field.field_alias}`}
                                                label={field.detail.name}
                                                value={getKeyValue(attendee, field.field_alias)}
                                                className={`${getKeyValue(attendee, field.field_alias) && 'ebs-input-verified'}`}
                                                required={Number(field.mandatory) === 1 ? true : false}
                                                autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                              />
                                            )
                                          } else if (field.field_alias === "ean") {
                                            return (
                                              <>
                                                <Input
                                                  onChange={(e: any) => {
                                                    const updateAttendee = {
                                                      ...attendee,
                                                      ['ean']: e.target.value
                                                    }
                                                    setAttendeeBilling(updateAttendee);
                                                    validateEanHandler(e.target.value);
                                                  }}
                                                  type="text"
                                                  tooltip={tooltips?.[field?.field_alias] === undefined ? tooltips?.[field?.field_alias] : ''}
                                                  field={`field-${field.field_alias}`}
                                                  label={field.detail.name}
                                                  value={getKeyValue(attendee, field.field_alias)}
                                                  className={`${getKeyValue(attendee, field.field_alias) && 'ebs-input-verified'}`}
                                                  required={Number(field.mandatory) === 1 ? true : false}
                                                  autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                                />
                                                {(validate_burger_id || attendee?.bruger_id) ? (
                                                  <Input
                                                    onChange={handleChange('bruger_id', 'text')}
                                                    type="text"
                                                    field={`field-${'bruger_id'}`}
                                                    label={event?.labels?.BRUGER_ID}
                                                    value={getKeyValue(attendee, 'bruger_id')}
                                                    className={`${getKeyValue(attendee, 'bruger_id') && 'ebs-input-verified'}`}
                                                    required={true}
                                                    autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                                  />
                                                ) : ''}
                                                {getKeyValue(errors, 'bruger_id') && <p className="error-message" id={'bruger_id'}>{getKeyValue(errors, 'bruger_id')}</p>}
                                              </>
                                            )
                                          } else if (field.field_alias === "company_post_code") {
                                            return (
                                              <Input
                                                onChange={(e: any) => {
                                                  const updateAttendee = {
                                                    ...attendee,
                                                    ['company_post_code']: e.target.value
                                                  }
                                                  setAttendeeBilling(updateAttendee);
                                                  postCodeHandler(e.target.value);
                                                }}
                                                type="text"
                                                tooltip={tooltips?.[field?.field_alias] === undefined ? tooltips?.[field?.field_alias] : ''}
                                                field={`field-${field.field_alias}`}
                                                label={field.detail.name}
                                                value={getKeyValue(attendee, field.field_alias)}
                                                className={`${getKeyValue(attendee, field.field_alias) && 'ebs-input-verified'}`}
                                                required={Number(field.mandatory) === 1 ? true : false}
                                                autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                              />
                                            )
                                          } else if (field.field_alias === "company_invoice_payer_post_code") {
                                            return (
                                              <Input
                                                onChange={(e: any) => {
                                                  const updateAttendee = {
                                                    ...attendee,
                                                    ['company_invoice_payer_post_code']: e.target.value
                                                  }
                                                  setAttendeeBilling(updateAttendee);
                                                  payerPostCodeHandler(e.target.value);
                                                }}
                                                type="text"
                                                tooltip={tooltips?.[field?.field_alias] === undefined ? tooltips?.[field?.field_alias] : ''}
                                                field={`field-${field.field_alias}`}
                                                label={field.detail.name}
                                                value={getKeyValue(attendee, field.field_alias)}
                                                className={`${getKeyValue(attendee, field.field_alias) && 'ebs-input-verified'}`}
                                                required={Number(field.mandatory) === 1 ? true : false}
                                                autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                              />
                                            )
                                          } else {
                                            return (
                                              <Input
                                                onChange={handleChange(field.field_alias, 'text')}
                                                type="text"
                                                tooltip={tooltips?.[field?.field_alias] === undefined ? tooltips?.[field?.field_alias] : ''}
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
                                      {!in_array(field.field_alias, ["ean"]) && Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendee, field.field_alias), (in_array(field.field_alias, ["email", "confirmation_email"]) ? 'required|email' : 'required'))}
                                      {getKeyValue(errors, field.field_alias) && <p className="error-message" id={field.field_alias}>{getKeyValue(errors, field.field_alias)}</p>}
                                    </React.Fragment>
                                  )
                              })()
                            }
                          </React.Fragment>
                        )
                      })}
                    </div>
                  </div>
                )
              ))}
              <div className="bottom-button text-center">
                <button
                  type="submit"
                  className="btn btn-save-next btn-loader"
                >
                  {action === 'submit' ? (
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
            </form>
          </div>
        </React.Fragment>
      </div>

      {cvr &&
        <Popup
          onClick={() => setCvr(false)}
          title={event?.labels?.EVENTSITE_BILLING_INVALID_CVR_NUMBER}>
          <div className="ebs-popup-content">
            <div>
              <p>{event?.labels?.EVENTSITE_BILLING_INVALID_CVR_MGS}</p>
              <p>{event?.labels?.EVENTSITE_BILLING_CVR_VALID_AND_CONTINUE}</p>
              <Input
                onChange={handleChange('company_registration_number', 'text')}
                type="text"
                label={event?.labels?.EVENTSITE_BILLING_CVR}
                field={`field-cvr`}
                value={getKeyValue(attendee, 'company_registration_number')}
                className={`${getKeyValue(attendee, 'company_registration_number') && 'ebs-input-verified'}`}
                autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
              />
              <div className='radio-check-field field-terms-services'>
                <label className={validate_cvr === 1 ? 'checked' : ''} onClick={() => {
                  setValidateCvr(validate_cvr === 1 ? 0 : 1)
                }}><span>{event?.labels?.EVENTSITE_BILLING_CVR_NUMBER_IS_VALID}</span></label>
              </div>
            </div>
            <div className="ebs-popup-buttons text-center">
              <div className="btn btn-primary" onClick={() => {
                setCvr(false);
              }}>{event?.labels?.EVENTSITE_SESSION_CONTINUE}</div>
            </div>
          </div>
        </Popup>
      }

      {ean &&
        <Popup
          onClick={() => setEan(false)}
          title={event?.labels?.EVENTSITE_BILLING_INVALID_EAN_NUMBER}>
          <div className="ebs-popup-content">
            <div>
              <p>{event?.labels?.EVENTSITE_BILLING_INVALID_EAN_MGS}</p>
              <p>{event?.labels?.EVENTSITE_BILLING_EAN_VALID_AND_CONTINUE}</p>
              <Input
                onChange={(e: any) => {
                  const updateAttendee = {
                    ...attendee,
                    ['ean']: e.target.value
                  }
                  setAttendeeBilling(updateAttendee);
                }}
                type="text"
                tooltip={tooltips?.['ean'] === undefined ? tooltips?.['ean'] : ''}
                field={`field-${'ean'}`}
                label={event?.labels?.EVENTSITE_BILLING_EAN}
                value={getKeyValue(attendee, 'ean')}
                className={`${getKeyValue(attendee, 'ean') && 'ebs-input-verified'}`}
                required={true}
                autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
              />
              <div className='radio-check-field field-terms-services'>
                <label className={validate_ean === 1 ? 'checked' : ''} onClick={() => {
                  setValidateEan(validate_ean === 1 ? 0 : 1)
                }}><span>{event?.labels?.EVENTSITE_BILLING_EAN_NUMBER_IS_VALID}</span></label>
              </div>
            </div>
            <div className="ebs-popup-buttons text-center">
              <div className="btn btn-primary" onClick={() => {
                if (validate_ean) {
                  setEan(false);
                } else {
                  validateEanHandler(attendee?.ean);
                }
              }}>{event?.labels?.EVENTSITE_SESSION_CONTINUE}</div>
            </div>
          </div>
        </Popup>
      }

      {po_number &&
        <Popup
          onClick={() => setPoNumber(false)}
          title={event?.labels?.EVENTSITE_BILLING_INVALID_PO_NUMBER_NUMBER}>
          <div className="ebs-popup-content">
            <div>
              <p>{event?.labels?.EVENTSITE_BILLING_INVALID_PO_NUMBER_MGS}</p>
              <Input
                onChange={(e: any) => {
                  const updateAttendee = {
                    ...attendee,
                    ['poNumber']: e.target.value
                  }
                  setAttendeeBilling(updateAttendee);
                  validatePoNumberHandler(e.target.value);
                }}
                type="text"
                label={event?.labels?.EVENTSITE_BILLING_PO_NUMBER}
                field={`field-poNumber`}
                value={getKeyValue(attendee, 'poNumber')}
                className={`${getKeyValue(attendee, 'poNumber') && 'ebs-input-verified'}`}
                autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
              />
              <div className='radio-check-field field-terms-services'>
                <label className={validate_po_number === 1 ? 'checked' : ''} onClick={() => {
                  setValidatePoNumber(validate_po_number === 1 ? 0 : 1)
                }}><span>{event?.labels?.EVENTSITE_BILLING_PO_NUMBER_NUMBER_IS_VALID}</span></label>
              </div>
            </div>
            <div className="ebs-popup-buttons text-center">
              <div className="btn btn-primary" onClick={() => {
                setPoNumber(false);
              }}>{event?.labels?.EVENTSITE_SESSION_CONTINUE}</div>
            </div>
          </div>
        </Popup>
      }

    </React.Fragment>
  );
};

export default PaymentInformation;