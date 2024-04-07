import React, { ReactElement, FC, useState, useEffect, useRef, useContext } from "react";
import Input from '@/src/app/components/forms/Input';
import DropDown from '@/src/app/components/forms/DropDown';
import { service } from '@/src/app/services/service';
import Event from '@/src/app/components/event/interface/Event';
import Loader from '@/src/app/components/forms/Loader';
import SimpleReactValidator from 'simple-react-validator';
import { useHistory, useParams, useLocation } from 'react-router-dom';
import { EventContext } from "@/src//app/context/event/EventProvider";
import in_array from "in_array";

type Params = {
  url: any;
  provider: any;
};

const AddAttendee: FC<any> = (props: any): ReactElement => {

  const history = useHistory();

  const [, forceUpdate] = useState(0);

  const { event, updateEvent, validate_code, updateRouteParams } = useContext<any>(EventContext);

  const simpleValidator = useRef(new SimpleReactValidator({
    element: (message: any) => <p className="error-message">{message}</p>,
    messages: {
      required: event?.labels?.REGISTRATION_FORM_FIELD_REQUIRED,
      email: event?.labels?.REGISTRATION_FORM_CONFIRM_EMAIL_MATCH
    },
    autoForceUpdate: { forceUpdate: () => forceUpdate(1) }
  }))

  const [attendees, setAttendees] = useState<any>([]);

  const [total, setTotal] = useState(1);

  const [totalAttendees, setTotalAttendees] = useState([]);

  const [loading, setLoading] = useState(true);

  const [action, setAction] = useState(false);

  const [errors, setErrors] = useState<any>({});

  const [message, setMessage] = useState<any>({});

  const [default_attendee_type, setDefaultAttendeeType] = useState<any>('');

  const { provider } = useParams<Params>();

  const mounted = useRef(false);

  const search = useLocation().search;

  const attendee_types = new URLSearchParams(search).get("attendee_types");

  const sale_id = new URLSearchParams(search).get("sale_id");

  useEffect(() => {
    updateRouteParams({ page: 'add-attendee', provider: provider });
    mounted.current = true;
    return () => { mounted.current = false; };
  }, []);

  useEffect(() => {
    if (Number(event?.eventsite_setting?.eventsite_public) === 1 && !validate_code) {
      history.push(`/${event.url}/event-registration-code`);
    } else if (Number(event?.eventsite_setting?.eventsite_public) === 1 && validate_code && Number(validate_code) !== Number(event.id)) {
      history.push(`/${event.url}/event-registration-code`);
    }
  }, [event]);

  useEffect(() => {
    if (Number(event?.payment_setting?.evensite_additional_attendee) === 1) {
      setLoading(true);
      service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/add-attendees?provider=${provider}&attendee_types=${attendee_types !== null ? attendee_types : ''}`)
        .then(
          response => {
            if (response.success && mounted.current) {
              setTotalAttendees(response?.data?.totalAttendees)
              if (response?.data?.attendees.length > 0 && response?.data?.attendees[0]['attendee_type'] !== undefined) {
                loadRegistrationForm(response?.data?.attendees[0]['attendee_type'], 0, 'initial');
                setDefaultAttendeeType(response?.data?.attendees[0]['attendee_type']);
              }
            }
          },
          error => {
            setLoading(false);
          }
        );
    } else {
      history.push(`/${event.url}/${provider}/manage-attendee`);
    }
  }, []);

  const handleChange = (input: any, type?: any, index?: any, attendees?: any) => (e: any) => {
    if (input === 'attendee_type') {
      loadRegistrationForm(e.value, index, 'update');
    } else if (type === 'total') {
      const _attendees: any = [];
      for (let i = 1; i <= e.value; i++) {
        _attendees.push(attendees[i] !== undefined ? attendees[i] : attendees[0]);
      }
      setAttendees(_attendees);
      setTotal(e.value);
    } else {
      const _value = type === 'select' ? e.value : (e.target.value === undefined ? [] : e.target.value);
      const count = attendees?.filter((row: any, key: any) => (key === index)).length;
      if (count == 0) {
        setAttendees([...attendees, {
          [input]: _value
        }]);
      } else {
        const container = [...attendees];
        container[index] = {
          ...container[index],
          [input]: _value
        };
        setAttendees(container);
      }
    }
  }

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
      simpleValidator.current.showMessages()
       setTimeout(() => {
        const scrollTo = document?.getElementsByClassName('error-message')[0];
        if (scrollTo !== undefined && scrollTo !== null) {
          scrollTo.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        }
      }, 500);
    } else if (!action) {
      setMessage({});
      setAction(true);
      service.put(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/add-attendees?provider=${provider}&sale_id=${sale_id !== null ? sale_id : ''}`, { attendees: attendees, provider: provider, attendee_types: attendee_types })
        .then(
          response => {
            if (mounted.current) {
              if (response.success) {
                history.push(`/${event.url}/${provider}/manage-attendee/${response.data.order.id}/${response.data.attendee_id}`);
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

  const loadRegistrationForm = (type_id: any, i: any, action: any = 'initial') => {
    setLoading(true);
    service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/eventsite-settings/eventsite-section-fields`, {
      attendee_type_id: type_id,
      provider: props?.provider
    })
      .then(
        response => {
          if (response.success && mounted.current) {
            setLoading(false);
            setErrors({});
            if (action === 'initial') {
              setAttendees([{ ...response.data.attendee, sections: response.data.sections, attendee_type: type_id }]);
            } else if (action === 'update') {
              setAttendees(attendees.map((attendee: any, index: any) => {
                if (i === index) {
                  return { ...response.data.attendee, sections: response.data.sections, attendee_type: type_id };
                } else {
                  return attendee;
                }
              }));
            } else if (action === 'add') {
              setAttendees([...attendees, { ...response.data.attendee, sections: response.data.sections, attendee_type: type_id }])
            }
          }
        },
        error => {
          setLoading(false);
        }
      );
  }

  const getKeyValue = function <T, U extends keyof T>(obj: T, key: U) { return obj[key] !== undefined ? obj[key] : '' }

  simpleValidator?.current?.purgeFields();

  return (
    <>
      <div className="row d-flex ebs-title-box align-items-center">
        <div className="col-6">
          <h2 className="section-title">{event?.labels?.REGISTRATION_FORM_REGISTRATION}</h2>
          <p className="ebs-attendee-caption">{event?.labels?.REGISTRATION_FORM_REGISTRATION_DESCRIPTION}</p>
        </div>
      </div>
      <div className={`wrapper-box`}>
        {loading && <Loader className='fixed' />}
        <React.Fragment>
          <div className="ebs-wrapper-attendee wrapper-inner-content">
            <form onSubmit={handleSubmit} autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}>
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
                })()
              }
              <div className="row d-flex justify-content-center">
                <div className="col-12 ">
                  <div className="header-box clearfix">
                    <h4 className="float-left">{event?.labels?.REGISTRATION_FORM_REGISTER}</h4>
                  </div>
                  <div className="row d-flex">
                    <div className="col-lg-11">
                      <div className="row d-flex">
                        <div className="col-lg-4 col-md-7">
                          <DropDown
                            label={event?.labels?.REGISTRATION_FORM_NUMBER_OF_ATTENDEES}
                            listitems={totalAttendees}
                            selected={total}
                            selectedlabel={getSelectedLabel(totalAttendees, total)}
                            onChange={handleChange('total', 'total', null, attendees)}
                            required={true}
                            placeholder={event?.labels?.REGISTRATION_FORM_SEARCH}
                          />
                        </div>
                      </div>
                      <p className="ebs-attendee-caption">{event?.labels?.REGISTRATION_FORM_REGISTRATION_DESCRIPTION_2}</p>
                    </div>
                  </div>
                  {[...Array(total)].map((x, i) =>
                    <div className="ebs-register-rows" key={i}>
                      <span className="ebs-remove-attendee" onClick={() => {
                        const _attendees = attendees;
                        if (total > 1) {
                          if (_attendees?.[i] !== undefined) {
                            setAttendees(attendees?.filter((row: any, key: any) => (key !== i)));
                            setTotal(total - 1)
                          } else {
                            setTotal(total - 1)
                          }
                        }
                      }}>
                        <i className="material-icons">close</i>
                      </span>
                      <div className="row d-flex">
                        <div className="col-lg-10">
                          {(event?.eventsite_registration_forms?.length > 1 && (
                            <div className="row d-flex">
                              <div className="col-lg-4">
                                <React.Fragment>
                                  <div className="header-box clearfix">
                                    <h4 className="float-left">{event?.labels?.REGISTRATION_FORM_ATTENDEE} #{i + 1}</h4>
                                  </div>
                                  <DropDown
                                    label={event?.labels?.REGISTRATION_FORM_ATTENDEE_TYPE}
                                    listitems={event?.eventsite_registration_forms}
                                    selected={getKeyValue(attendees?.filter((row: any, key: any) => (key === i)).length > 0 ? attendees[i] : '', 'attendee_type')}
                                    selectedlabel={getSelectedLabel(event?.eventsite_registration_forms, getKeyValue(attendees?.filter((row: any, key: any) => (key === i)).length > 0 ? attendees[i] : '', 'attendee_type'))}
                                    onChange={handleChange('attendee_type', 'select', i, attendees)}
                                    required={true}
                                    placeholder={event?.labels?.REGISTRATION_FORM_SEARCH}
                                  />
                                  {simpleValidator.current.message('attendee_type', getKeyValue(attendees?.filter((row: any, key: any) => (key === i)).length > 0 ? attendees[i] : '', 'attendee_type'), 'required')}
                                  {getKeyValue(errors, 'attendee_type-' + i) && <p className="error-message">{getKeyValue(errors, 'attendee_type-' + i)}</p>}
                                  {getKeyValue(errors, 'duplicates') && <p className="error-message">{getKeyValue(errors, 'duplicates')}</p>}
                                </React.Fragment>
                              </div>
                            </div>
                          ))}
                          <div className="row d-flex">
                            {attendees[i]?.sections && attendees[i]?.sections.map((section: any, key: any) => (
                              in_array(section.field_alias, ["basic"]) && (
                                section.fields?.length > 0 && (
                                  section.fields && section.fields.map((field: any, index: any) => {
                                    return (
                                      <React.Fragment key={index}>
                                        {
                                          (() => {
                                            if (in_array(field.field_alias, ["first_name", "last_name", "email"]))
                                              return (
                                                <div className="col-lg-4">
                                                  <Input
                                                    onChange={handleChange(field.field_alias, 'text', i, attendees)}
                                                    className={`${getKeyValue(attendees?.filter((row: any, key: any) => (key === i)).length > 0 ? attendees[i] : '', field.field_alias) && 'ebs-input-verified'}`}
                                                    type="text"
                                                    label={field.detail.name}
                                                    field={`field-${field.field_alias}`}
                                                    value={getKeyValue(attendees?.filter((row: any, key: any) => (key === i)).length > 0 ? attendees[i] : '', field.field_alias)}
                                                    required={(Number(field.mandatory) === 1 ? true : false)}
                                                    autoComplete={Number(event?.eventsite_setting?.auto_complete) === 1 ? 'off' : ''}
                                                  />
                                                  {Number(field.mandatory) === 1 && simpleValidator.current.message(field.field_alias, getKeyValue(attendees?.filter((row: any, key: any) => (key === i)).length > 0 ? attendees[i] : '', field.field_alias), field.field_alias === "email" ? 'required|email' : 'required')}
                                                  {getKeyValue(errors, field.field_alias + '-' + i) && <p className="error-message">{getKeyValue(errors, field.field_alias + '-' + i)}</p>}
                                                </div>
                                              )
                                          })()
                                        }
                                      </React.Fragment>
                                    )
                                  })
                                )
                              )
                            ))}
                          </div>
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              </div>
              {total < 20 && <div className="ebs-add-more">
                <span onClick={() => {
                  setTotal(total + 1);
                  loadRegistrationForm(default_attendee_type, total, 'add');
                }} className="ebs-add">{event?.labels?.REGISTRATION_FORM_ADD_MORE}</span>
              </div>}
              <div className="bottom-button text-center">
                <button
                  type="submit"
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
            </form>
          </div>
        </React.Fragment>
      </div>
    </>
  );
};

export default AddAttendee;