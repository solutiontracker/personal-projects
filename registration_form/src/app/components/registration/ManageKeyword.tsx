import React, { ReactElement, FC, useEffect, useState, useRef, useContext, useMemo } from "react";
import { service } from '@/src/app/services/service';
import Event from '@/src/app/components/event/interface/Event';
import Loader from '@/src/app/components/forms/Loader';
import { useHistory } from 'react-router-dom';
import { EventContext } from "@/src//app/context/event/EventProvider";
import in_array from "in_array";
import debounce from 'lodash.debounce';
interface Props {
  section: any;
  order_id: number;
  attendee_id: number;
  provider: any;
  goToSection: any;
  event: Event;
  orderAttendee?: any;
  formSettings?: any;
}

const ManageKeyword: FC<Props> = (props: any): ReactElement => {

  const { section } = props;

  const { event, updateEvent, formBuilderForms } = useContext<any>(EventContext);

  const [keywords, setKeywords] = useState<any>([]);

  const [filter, setFilter] = useState<any>([]);

  const [loading, setLoading] = useState(section === "manage-keywords" ? true : false);

  const [action, setAction] = useState('');

  const [count, setCount] = useState(0);

  const [errors, setErrors] = useState<any>({});

  const [show, setShow] = useState(true);

  const [search, setSearch] = useState('');

  const history = useHistory();

  const mounted = useRef(false);

  useEffect(() => {
    mounted.current = true;
    setKeywords([]);
    setFilter([]);
    setSearch('');
    return () => {
      mounted.current = false;
      debouncedResults.cancel();
    };
  }, []);

  useEffect(() => {
    setKeywords([]);
    setFilter([]);
    setSearch('');
  }, [section]);

  useEffect(() => {
    if (section === "manage-keywords") {
      if (!search) setLoading(true);
      service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/keywords/${props.order_id}/${props.attendee_id}`, { filter: filter, search: search, provider: props?.provider })
        .then(
          response => {
            if (response.success && mounted.current) {
              if (response?.data?.order?.order_detail?.order?.status !== "completed" || (response?.data?.order?.order_detail?.order?.status === "completed" && (Number(response?.data?.order?.order_detail?.order?.is_waitinglist) === 1 || in_array(props?.provider, ['sale', 'admin'])))) {
                if ((response.data.keywords?.length > 0 || search) && Number(props?.formSettings?.show_business_dating) === 1) {
                  setKeywords(response.data.keywords);
                  //Update event info
                  updateEvent({
                    ...event,
                    order: response?.data?.order
                  });
                } else {
                  if (Number(props?.formSettings?.show_subregistration) === 1) {
                    props.goToSection('manage-sub-registrations', props.order_id, props.attendee_id);
                  } else if(Number(props?.formSettings?.show_required_documents) === 1){
                    props.goToSection('manage-documents', props.order_id, props.attendee_id);
                  } else if (Number(props?.formSettings?.show_hotels) === 1) {
                    props.goToSection('manage-hotel-booking', props.order_id, props.attendee_id);
                  } else if (formBuilderForms.length > 0) {
                    props.goToSection('custom-forms', props.order_id, props.attendee_id, formBuilderForms[0].id);
                  } else {
                    completedAttendeeIteration(props.attendee_id);
                  }
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
  }, [section, filter, search]);

  const handleKeyword = (keywordKey: any, childKey: any) => (e: any) => {
    keywords[keywordKey]['children'][childKey]['status'] = (keywords[keywordKey]['children'][childKey]['status'] === 1 ? 0 : 1);
    setKeywords(keywords);
    setCount(count + 1);
    saveKeywords('auto');
  };

  const handleKeywordFilter = (keywordKey: any) => (e: any) => {
    if (keywordKey === 0) {
      setFilter([0]);
    } else if (filter?.filter((i: any) => i === keywordKey)?.length > 0) {
      setFilter(filter?.filter((i: any) => (i !== keywordKey && i !== 0)));
    } else {
      setFilter((filter: any) => [...filter?.filter((i: any) => i !== 0), keywordKey]);
    }
  };

  useEffect(() => {
    if (filter?.length === 0) {
      setFilter([0]);
    }
  }, [filter]);

  const handleSearch = (e: any) => {
    setSearch(e.target.value);
  };

  const debouncedResults = useMemo(() => {
    return debounce(handleSearch, 300);
  }, []);

  const handleSubmit = (evt: any) => {
    evt.preventDefault();
    saveKeywords('manual');
  }

  const saveKeywords = (action: any) => {
    setAction(action);
    service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/keywords/${props.order_id}/${props.attendee_id}`, { keywords: keywords, action: 'save', provider: props?.provider })
      .then(
        response => {
          if (mounted.current) {
            if (action === 'manual') {
              if (response.success) {
                if (Number(props?.formSettings?.show_subregistration) === 1) {
                  props.goToSection('manage-sub-registrations', response.data.order_id, response.data.attendee_id);
                } else if(Number(props?.formSettings?.show_required_documents) === 1){
                  props.goToSection('manage-documents', response.data.order_id, response.data.attendee_id);
                } else if (Number(props?.formSettings?.show_hotels) === 1) {
                  props.goToSection('manage-hotel-booking', response.data.order_id, response.data.attendee_id);
                } else if (formBuilderForms.length > 0) {
                  props.goToSection('custom-forms', response.data.order_id, response.data.attendee_id, formBuilderForms[0].id);
                } else {
                  completedAttendeeIteration(response.data.attendee_id);
                }
              } else {
                setErrors(response.errors);
              }
              setAction('');
            }
          }
        },
        error => {
          setAction('');
        }
      );
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
            setAction('');
            setLoading(false);
          }
        },
        error => {
          setAction('');
          setLoading(false);
        }
      );
  }

  return (
    <React.Fragment>
      {Number(props?.formSettings?.show_business_dating) === 1 && (
        <div className={`${section !== "manage-keywords" && 'tab-collapse'} wrapper-box select-items-section`}>
          {loading && <Loader className='fixed' />}
          <React.Fragment>
            <header className="header-section">
              <h3 onClick={(e: any) => {
                if (props?.orderAttendee?.status === 'complete' || (location.pathname.toString().includes("/manage-sub-registrations") || location.pathname.toString().includes("/manage-documents"))) {
                  history.push(`/${event.url}/${props?.provider}/manage-keywords/${props.order_id}/${props.attendee_id}`);
                } else {
                  setShow(!show)
                }
              }}>
                {event?.labels?.REGISTRATION_FORM_SELECT_NETWORK_INTERESTS}  <i className="material-icons"> {section === "manage-keywords" && show ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}</i>
              </h3>
              <div className="icon-tick">
                {((location.pathname.toString().includes("/manage-keywords") || location.pathname.toString().includes("/manage-sub-registrations") || location.pathname.toString().includes("/manage-documents")) || props?.orderAttendee?.status === 'complete') ? (
                  <img src={require('@/src/img/tick-green.svg')} alt="" />
                ) : (
                  <img src={require('@/src/img/tick-grey.svg')} alt="" />
                )}
              </div>
            </header>

            {section === "manage-keywords" && show && event?.event_description?.detail?.keyword_description && (
              <p className="ebs-attendee-caption" dangerouslySetInnerHTML={{ __html: event?.event_description?.detail?.keyword_description }}></p>
            )}

            {section === "manage-keywords" && show && (
              <div className="wrapper-inner-content network-category-sec">
                <div className="ebs-keyword-search">
                  <label>
                    <input placeholder={event?.labels?.REGISTRATION_FORM_SEARCH_NETWORK} type="text" onChange={debouncedResults} />
                    <i className="material-icons">search</i>
                  </label>
                </div>
                {keywords?.length > 0 && (
                  <div className="ebs-keywords-filter">
                    <div className="network-cateogry-list ebs-cateogry-filter">
                      <ul>
                        <li>
                          <label><input onChange={handleKeywordFilter(0)} checked={in_array(0, filter)} type="checkbox" />
                            <span>{event?.labels?.REGISTRATION_FORM_NETWORK_INTEREST_ALL_LABEL}</span></label>
                        </li>
                        {keywords && keywords.map((keyword: any, key: any) =>
                          <li key={key}>
                            <label><input onChange={handleKeywordFilter(keyword.id)} checked={in_array(keyword.id, filter)} type="checkbox" /><span>{keyword.name}</span></label>
                          </li>
                        )}
                      </ul>
                    </div>
                  </div>
                )}
                {keywords && keywords.map((keyword: any, keywordKey: any) =>
                  keyword.children && keyword.children.length > 0 && (
                    <div className="network-cateogry-list" key={keywordKey}>
                      <h5>{keyword.name}</h5>
                      <ul>
                        {keyword.children && keyword.children.map((child: any, childKey: any) =>
                          <li key={childKey}>
                            <label><input type="checkbox" onChange={handleKeyword(keywordKey, childKey)} checked={Number(child.status) === 1 ? true : false} /><span>{child.name}</span></label>
                          </li>
                        )}
                      </ul>
                    </div>
                  )
                )}
                <div className="bottom-button text-center">
                  {props?.orderAttendee?.status === 'complete' && (
                    <a onClick={() => {
                      if (Number(props?.formSettings?.show_subregistration) === 1) {
                        props.goToSection('manage-sub-registrations', props.order_id, props.attendee_id);
                      } else if(Number(props?.formSettings?.show_required_documents) === 1){
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
                    {action === 'manual' ? (
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
              </div>
            )}
          </React.Fragment>
        </div>
      )}
    </React.Fragment>
  )
};

export default ManageKeyword;