import React, { ReactElement, FC, useEffect, useState, useRef, useContext } from "react";
import Select from 'react-select';
import DateTime from '@/src/app/components/forms/DateTime';
import 'react-day-picker/lib/style.css';
import { service } from '@/src/app/services/service';
import Event from '@/src/app/components/event/interface/Event';
import Loader from '@/src/app/components/forms/Loader';
import moment from 'moment-timezone';
import SimpleReactValidator from 'simple-react-validator';
import { useHistory } from 'react-router-dom';
import { EventContext } from "@/src//app/context/event/EventProvider";
import Input from '@/src/app/components/forms/Input';
import in_array from "in_array";
import { getLanguageCode } from '@/src/app/helpers';
interface Props {
  section: any;
  order_id: number;
  attendee_id: number;
  goToSection: any;
  provider: any;
  event: Event;
  orderAttendee?: any;
  formSettings?: any;
}

const ManageSubRegistration: FC<Props> = (props: any): ReactElement => {

  const { section } = props;

  const { event, updateEvent, formBuilderForms, updateOrder } = useContext<any>(EventContext);

  const [width, setWidth] = useState(0);

  const [questions, setQuestions] = useState<any>([]);

  const [settings, setSettings] = useState<any>({});

  const [orderAttendeeAnswers, setOrderAttendeeAnswers] = useState<any>([]);

  const [loading, setLoading] = useState(section === "manage-sub-registrations" ? true : false);

  const [action, setAction] = useState(false);

  const [count, setCount] = useState(0);

  const [errors, setErrors] = useState<any>({});

  const [show, setShow] = useState(true);

  const history = useHistory();

  const mounted = useRef(false);

  const style = {
    control: (base: any) => ({
      ...base,
      boxShadow: 'none'
    })
  };

  const [, forceUpdate] = useState(0);

  const simpleValidator = useRef(new SimpleReactValidator({
    element: (message: any) => <p className="error-message">{message}</p>,
    messages: {
      required: event?.labels?.REGISTRATION_FORM_FIELD_REQUIRED
    },
    autoForceUpdate: { forceUpdate: () => forceUpdate(1) }
  }))

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

    return () => {
      setQuestions([]);
      mounted.current = false;
    };
  }, []);

  useEffect(() => {
    if (section === "manage-sub-registrations") {
      setLoading(true);
      service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/sub-registration/${props.order_id}/${props.attendee_id}?provider=${props?.provider}`)
        .then(
          response => {
            if (response.success && mounted.current) {
              if (response?.data?.order?.order_detail?.order?.status !== "completed" || (response?.data?.order?.order_detail?.order?.status === "completed" && (Number(response?.data?.order?.order_detail?.order?.is_waitinglist) === 1 || in_array(props?.provider, ['sale', 'admin'])))) {
                if (response.data.questions?.length > 0 && Number(props?.formSettings?.show_subregistration) === 1) {
                  setQuestions(response.data.questions);
                  setOrderAttendeeAnswers(response.data.orderAttendeeAnswers);
                  setSettings(response?.data?.sub_registration_settings);
                  updateOrder(response?.data?.order?.order_detail?.order);

                  //Update event info
                  updateEvent({
                    ...event,
                    order: response?.data?.order
                  });
                } else {
                  if (Number(props?.formSettings?.show_required_documents) === 1) {
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
  }, [section]);

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

  const handleCheckBox = (questionKey: any, answerKey: any) => () => {
    let count = questions[questionKey]['answer'].filter((answer: any) => (Number(answer.is_default) === 1)).length;
    count = (Number(questions[questionKey]['answer'][answerKey]['is_default']) === 0 ? count + 1 : count - 1);
    if (((count <= questions[questionKey].max_options || (questions[questionKey].min_options === 0 && questions[questionKey].max_options === 0)) && questions[questionKey]['answer'][answerKey].ticket_left === "yes") || Number(questions[questionKey]['answer'][answerKey]['is_default']) === 1) {
      if ((settings?.favorite_session_registration_same_time === 1 || ((settings?.favorite_session_registration_same_time === 0 && questions[questionKey]['answer'].filter((answer: any) => (Number(answer.is_default) === 1 && answer?.program_schedule === questions[questionKey]['answer'][answerKey]['program_schedule'])).length === 0) || Number(questions[questionKey]['answer'][answerKey]['link_to']) === 0)) || Number(questions[questionKey]['answer'][answerKey]['is_default']) === 1) {
        // Released stock for that answer that was already in stocked after getting unselect
        if (Number(questions[questionKey]['answer'][answerKey]['is_default']) === 1) {
          questions[questionKey]['answer'][answerKey]['ticket_left'] = "yes";
          questions[questionKey]['answer'][answerKey]['tickets'] = 1;
        }
        questions[questionKey]['answer'][answerKey]['is_default'] = (Number(questions[questionKey]['answer'][answerKey]['is_default']) === 0 ? 1 : 0);
        setQuestions(questions);
        setCount(count + 1);
      }
    }
  };

  const handleComment = (questionKey: any) => (e: any) => {
    questions[questionKey]['comment'] = e.target.value;
    setQuestions(questions);
    setCount(count + 1);
  };

  const handleToggleComment = (questionKey: any) => (e: any) => {
    questions[questionKey]['show_comment'] = questions[questionKey]['show_comment'] === 1 ? 0 : 1;
    setQuestions(questions);
    setCount(count + 1);
  };

  const handleOpenQuestion = (questionKey: any) => (e: any) => {
    questions[questionKey]['answerValue'] = e.target.value;
    setQuestions(questions);
    setCount(count + 1);
  };

  const handleDropDown = (questionKey: any) => (e: any) => {
    const answerKey = questions[questionKey]?.answer.findIndex((o: any) => o.value.toString() === e.value.toString());
    if (answerKey !== undefined) {
      if (questions[questionKey]['answer'][answerKey].ticket_left === "yes" || Number(questions[questionKey]['answer'][answerKey]?.is_default) === 1) {
        const existing = questions[questionKey]?.answer.findIndex((o: any) => o.value.toString() === questions[questionKey]['answerValue'].toString());
        if (existing !== undefined && Number(questions[questionKey]['answer'][existing]?.is_default) === 1) {
          questions[questionKey]['answer'][existing]['ticket_left'] = "yes";
          questions[questionKey]['answer'][existing]['tickets'] = 1;
        }
        questions[questionKey]['answerValue'] = e.value;
        setQuestions(questions);
        setCount(count + 1);
      }
    }
  };

  const handleDateChange = (questionKey: any) => (e: any) => {
    if (e !== undefined && e !== 'Invalid date' && e !== 'cleardate') {
      const date = moment(new Date(e)).format('YYYY-MM-DD');
      questions[questionKey]['answerValue'] = date;
      setQuestions(questions);
      setCount(count + 1);
    }
  };

  const handleDateTimeChange = (questionKey: any) => (e: any) => {
    if (e !== undefined) {
      const date = moment(new Date(e)).format('YYYY-MM-DD HH:mm:ss');
      questions[questionKey]['answerValue'] = date;
      setQuestions(questions);
      setCount(count + 1);
    }
  };

  const handleRadioButton = (questionKey: any, value: any) => () => {
    const answerKey = questions[questionKey]?.answer.findIndex((o: any) => o.value.toString() === value.toString());
    if (answerKey !== undefined) {
      if (questions[questionKey]['answer'][answerKey].ticket_left === "yes" || Number(questions[questionKey]['answer'][answerKey]['is_default']) === 1) {
        if (Number(questions[questionKey]['answer'][answerKey]['is_default']) === 1) {
          questions[questionKey]['answer'][answerKey]['ticket_left'] = "yes";
          questions[questionKey]['answer'][answerKey]['tickets'] = 1;
        }
        questions[questionKey]['answerValue'] = value;
        setQuestions(questions);
        setCount(count + 1);
      }
    }
  };

  const handleMatrixAnswer = (questionKey: any, answerKey: any, matrix_id: any) => () => {
    questions[questionKey]['answer'][answerKey]['answerValue'] = matrix_id;
    setQuestions(questions);
    setCount(count + 1);
  };

  const handleMatrixSelectAnswer = (questionKey: any, answerKey: any) => (e: any) => {
    questions[questionKey]['answer'][answerKey]['answerValue'] = e.value;
    setQuestions(questions);
    setCount(count + 1);
  };

  const getSelectedLabel = (item: any, id: any) => {
    if (item && item.length > 0 && id) {
      const obj = item.find((o: any) => o.id.toString() === id.toString());
      return (obj ? (obj.name ? obj.name : obj.label) : '');
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
      setAction(true);
      service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/sub-registration/${props.order_id}/${props.attendee_id}`, { questions: questions, provider: props?.provider })
        .then(
          response => {
            if (mounted.current) {
              if (response.success) {
                if (Number(props?.formSettings?.show_required_documents) === 1) {
                  props.goToSection('manage-documents', props.order_id, props.attendee_id);
                } else if (Number(props?.formSettings?.show_hotels) === 1) {
                  props.goToSection('manage-hotel-booking', props.order_id, props.attendee_id);
                } else if (formBuilderForms.length > 0) {
                  props.goToSection('custom-forms', props.order_id, props.attendee_id, formBuilderForms[0].id);
                } else {
                  completedAttendeeIteration(props.attendee_id);
                }
              } else {
                setQuestions(response.data.questions);
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

  return (
    <React.Fragment>
      {Number(props?.formSettings?.show_subregistration) === 1 && (
        <div className={`${section !== "manage-sub-registrations" && 'tab-collapse'} wrapper-box other-information-sec`}>
          {loading && <Loader className='fixed' />}
          <React.Fragment>
            <header className="header-section">
              <h3 onClick={(e: any) => {
                if (props?.orderAttendee?.status === 'complete' || location.pathname.toString().includes("/manage-documents")) {
                  history.push(`/${event.url}/${props?.provider}/manage-sub-registrations/${props.order_id}/${props.attendee_id}`);
                } else {
                  setShow(!show)
                }
              }}>
                {event?.labels?.REGISTRATION_FORM_SELECT_OTHER_INFORMATION} <i className="material-icons">
                  {section === "manage-sub-registrations" && show ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}</i>
              </h3>
              <div className="icon-tick">
                {((location.pathname.toString().includes("/manage-sub-registrations") || location.pathname.toString().includes("/manage-documents")) || props?.orderAttendee?.status === 'complete') ? (
                  <img src={require('@/src/img/tick-green.svg')} alt="" />
                ) : (
                  <img src={require('@/src/img/tick-grey.svg')} alt="" />
                )}
              </div>
            </header>
            {section === "manage-sub-registrations" && show && event?.event_description?.detail?.sub_registration_description && (
              <p className="ebs-attendee-caption" dangerouslySetInnerHTML={{ __html: event?.event_description?.detail?.sub_registration_description }}></p>
            )}
            {section === "manage-sub-registrations" && show && (
              <div className="wrapper-inner-content">
                <div className="other-information-inner">
                  {questions && questions.map((question: any, questionKey: any) =>
                    <React.Fragment key={questionKey}>
                      {
                        (() => {
                          if (question.answer && question.answer.length > 0 && question.question_type === "multiple")
                            return (
                              <div className="ebs-field-wrapper">
                                <div className='ebs-half-wrapper'>
                                  <div className='radio-check-field'>
                                    <h5>{`${question.detail.question}`}{Number(question.required_question) === 1 && (
                                      <em className='req'>*</em>
                                    )}</h5>
                                    <div className="ebs-field-wrapp">
                                      {question.answer && question.answer.map((answer: any, answerKey: any) =>
                                        <label key={answerKey} onClick={handleCheckBox(questionKey, answerKey)} className={Number(answer.is_default) === 1 ? "checked" : ""} ><span>{answer.detail.answer}</span></label>
                                      )}
                                    </div>
                                    {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, question.answer.filter((answer: any) => (Number(answer.is_default) === 1)).length > 0 ? true : null, 'required')}
                                    {question?.error !== undefined && question?.error && <p className="error-message">{question?.error}</p>}
                                    {Number(question.enable_comments) === 1 && (
                                      <div className="generic-form">
                                        <p style={{ cursor: 'pointer' }} className="d-flex align-items-center">{event?.labels?.GENERAL_COMMENT} <i style={{ fontSize: '16px', margin: '3px 0 0 3px' }} className="material-icons" onClick={handleToggleComment(questionKey)}>{question?.show_comment === 1 ? 'remove_circle_outline' : 'add_circle_outline'}</i></p>
                                        {question?.show_comment === 1 && (
                                          <textarea onChange={handleComment(questionKey)} value={question.comment} cols={30} rows={5}></textarea>
                                        )}
                                      </div>
                                    )}
                                  </div>
                                </div>
                              </div>
                            )
                          else if (question.question_type === "number")
                            return (
                              <div className="ebs-field-wrapper">
                                <div className='ebs-half-wrapper'>
                                  <div className="generic-form">
                                    <h5>{`${question.detail.question}`} {Number(question.required_question) === 1 && (
                                      <em className='req'>*</em>
                                    )}</h5>
                                    <Input
                                      onChange={handleOpenQuestion(questionKey)}
                                      type="number"
                                      field={`${questionKey}`}
                                      label={"Answer"}
                                      value={question.answerValue}
                                      className={`${question.answerValue && 'ebs-input-verified'}`}
                                    />
                                    {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, question.answerValue, 'required')}
                                    {Number(question.enable_comments) === 1 && (
                                      <div className="generic-form">
                                        <p style={{ cursor: 'pointer' }} className="d-flex align-items-center">{event?.labels?.GENERAL_COMMENT} <i style={{ fontSize: '16px', margin: '3px 0 0 3px' }} className="material-icons" onClick={handleToggleComment(questionKey)}>{question?.show_comment === 1 ? 'remove_circle_outline' : 'add_circle_outline'}</i></p>
                                        {question?.show_comment === 1 && (
                                          <textarea onChange={handleComment(questionKey)} value={question.comment} cols={30} rows={5}></textarea>
                                        )}
                                      </div>
                                    )}
                                  </div>
                                </div>
                              </div>
                            )
                          else if (question.question_type === "open")
                            return (
                              <div className="ebs-field-wrapper">
                                <div className='ebs-half-wrapper'>
                                  <div className="generic-form">
                                    <h5>{`${question.detail.question}`} {Number(question.required_question) === 1 && (
                                      <em className='req'>*</em>
                                    )}</h5>
                                    <textarea value={question.answerValue} onChange={handleOpenQuestion(questionKey)} cols={30} rows={10}></textarea>
                                    {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, question.answerValue, 'required')}
                                    {Number(question.enable_comments) === 1 && (
                                      <div className="generic-form">
                                        <p style={{ cursor: 'pointer' }} className="d-flex align-items-center">{event?.labels?.GENERAL_COMMENT} <i style={{ fontSize: '16px', margin: '3px 0 0 3px' }} className="material-icons" onClick={handleToggleComment(questionKey)}>{question?.show_comment === 1 ? 'remove_circle_outline' : 'add_circle_outline'}</i></p>
                                        {question?.show_comment === 1 && (
                                          <textarea onChange={handleComment(questionKey)} value={question.comment} cols={30} rows={5}></textarea>
                                        )}
                                      </div>
                                    )}
                                  </div>
                                </div>
                              </div>
                            )
                          else if (question.answer && question.answer.length > 0 && question.question_type === "dropdown")
                            return (
                              <div className="ebs-field-wrapper">
                                <div className='ebs-half-wrapper'>
                                  <div className="generic-form">
                                    <h5>{`${question.detail.question}`}{Number(question.required_question) === 1 && (
                                      <em className='req'>*</em>
                                    )}</h5>
                                    <div className="custom-label-select" style={{ maxWidth: '300px' }}>
                                      <Select
                                        classNamePrefix={"react-select"}
                                        components={{ IndicatorSeparator: null }}
                                        styles={style}
                                        placeholder={"Select value from dropdown"}
                                        options={question.answer}
                                        value={question.answerValue ? { label: getSelectedLabel(question.answer, question.answerValue), value: question.answerValue } : question.answerValue}
                                        onChange={handleDropDown(questionKey)}
                                      />
                                    </div>
                                    {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, question.answerValue, 'required')}
                                    {Number(question.enable_comments) === 1 && (
                                      <div className="generic-form">
                                        <p style={{ cursor: 'pointer' }} className="d-flex align-items-center">{event?.labels?.GENERAL_COMMENT} <i style={{ fontSize: '16px', margin: '3px 0 0 3px' }} className="material-icons" onClick={handleToggleComment(questionKey)}>{question?.show_comment === 1 ? 'remove_circle_outline' : 'add_circle_outline'}</i></p>
                                        {question?.show_comment === 1 && (
                                          <textarea onChange={handleComment(questionKey)} value={question.comment} cols={30} rows={5}></textarea>
                                        )}
                                      </div>
                                    )}
                                  </div>
                                </div>
                              </div>
                            )
                          else if (question.question_type === "date")
                            return (
                              <div className="ebs-field-wrapper">
                                <div className='ebs-half-wrapper'>
                                  <div className="generic-form">
                                    <h5>{`${question.detail.question}`}{Number(question.required_question) === 1 && (
                                      <em className='req'>*</em>
                                    )}</h5>
                                    <DateTime
                                      onChange={handleDateChange(questionKey)}
                                      value={question.answerValue}
                                      label={`Select date`}
                                      showdate={'YYYY-MM-DD'}
                                      locale={getLanguageCode(event.language_id)}
                                    />
                                    {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, question.answerValue, 'required')}
                                    {Number(question.enable_comments) === 1 && (
                                      <div className="generic-form">
                                        <p style={{ cursor: 'pointer' }} className="d-flex align-items-center">{event?.labels?.GENERAL_COMMENT} <i style={{ fontSize: '16px', margin: '3px 0 0 3px' }} className="material-icons" onClick={handleToggleComment(questionKey)}>{question?.show_comment === 1 ? 'remove_circle_outline' : 'add_circle_outline'}</i></p>
                                        {question?.show_comment === 1 && (
                                          <textarea onChange={handleComment(questionKey)} value={question.comment} cols={30} rows={5}></textarea>
                                        )}
                                      </div>
                                    )}
                                  </div>
                                </div>
                              </div>
                            )
                          else if (question.question_type === "date_time")
                            return (
                              <div className="ebs-field-wrapper">
                                <div className='ebs-half-wrapper'>
                                  <div className="generic-form">
                                    <h5>{`${question.detail.question}`}{Number(question.required_question) === 1 && (
                                      <em className='req'>*</em>
                                    )}</h5>
                                    <DateTime
                                      onChange={handleDateTimeChange(questionKey)}
                                      value={question.answerValue}
                                      showtime={'HH:mm:ss'}
                                      label={`Select date time`}
                                      showdate={'YYYY-MM-DD'}
                                      locale={getLanguageCode(event.language_id)}
                                    />
                                    {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, question.answerValue, 'required')}
                                    {Number(question.enable_comments) === 1 && (
                                      <div className="generic-form">
                                        <p style={{ cursor: 'pointer' }} className="d-flex align-items-center">{event?.labels?.GENERAL_COMMENT} <i style={{ fontSize: '16px', margin: '3px 0 0 3px' }} className="material-icons" onClick={handleToggleComment(questionKey)}>{question?.show_comment === 1 ? 'remove_circle_outline' : 'add_circle_outline'}</i></p>
                                        {question?.show_comment === 1 && (
                                          <textarea onChange={handleComment(questionKey)} value={question.comment} cols={30} rows={5}></textarea>
                                        )}
                                      </div>
                                    )}
                                  </div>
                                </div>
                              </div>
                            )
                          else if (question.question_type === "single")
                            return (
                              <div className="ebs-field-wrapper">
                                <div className='ebs-half-wrapper'>
                                  <div className='radio-check-field style-radio'>
                                    <h5>{`${question.detail.question}`}{Number(question.required_question) === 1 && (
                                      <em className='req'>*</em>
                                    )}</h5>
                                    {question.answer && question.answer.map((answer: any, answerKey: any) =>
                                      <label onClick={handleRadioButton(questionKey, Number(answer.id))} key={answerKey} className={Number(answer.id) === question.answerValue ? "checked" : ""}><span>{answer.detail.answer}</span></label>
                                    )}
                                    {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, question.answerValue, 'required')}
                                    {Number(question.enable_comments) === 1 && (
                                      <div className="generic-form">
                                        <p style={{ cursor: 'pointer' }} className="d-flex align-items-center">{event?.labels?.GENERAL_COMMENT} <i style={{ fontSize: '16px', margin: '3px 0 0 3px' }} className="material-icons" onClick={handleToggleComment(questionKey)}>{question?.show_comment === 1 ? 'remove_circle_outline' : 'add_circle_outline'}</i></p>
                                        {question?.show_comment === 1 && (
                                          <textarea onChange={handleComment(questionKey)} value={question.comment} cols={30} rows={5}></textarea>
                                        )}
                                      </div>
                                    )}
                                  </div>
                                </div>
                              </div>
                            )
                          else if (question.question_type === "matrix")
                            return (
                              <React.Fragment>
                                <div className={`${width <= 1080 && 'responsive-matrix'} matrix-question-wrapper`}>
                                  <h5>{`${question.detail.question}`}{Number(question.required_question) === 1 && (
                                    <em className='req'>*</em>
                                  )}</h5>
                                  <div className="matrix-table-wrapper">
                                    <div className="matrix-table">
                                      {width > 1080 &&
                                        <div className="martix-row matrix-header">
                                          <div className="matrix-box matrix-heading"></div>
                                          {question.matrix && question.matrix.map((matrix: any, matrixKey: any) =>
                                            <div key={matrixKey} className="matrix-box">{matrix.label}</div>
                                          )}
                                        </div>
                                      }
                                      {question.answer && question.answer.map((answer: any, answerKey: any) =>
                                        <React.Fragment key={answerKey}>
                                          <div className="martix-row">
                                            <div className="matrix-box matrix-heading">{answer.detail.answer}</div>

                                            <React.Fragment>
                                              {question.matrix && question.matrix.map((matrix: any, matrixKey: any) =>
                                                <div key={matrixKey} className="matrix-box"><label className="label-radio"><input checked={Number(answer.answerValue) === Number(matrix.id) ? true : false} type="radio" onChange={handleMatrixAnswer(questionKey, answerKey, matrix.id)} /><span></span></label></div>
                                              )}
                                            </React.Fragment>
                                          </div>
                                        </React.Fragment>
                                      )}
                                    </div>
                                  </div>
                                  {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, question.answer.filter((answer: any) => (answer.answerValue)).length > 0 ? true : null, 'required')}
                                  {Number(question.enable_comments) === 1 && (
                                    <div className="generic-form">
                                      <p style={{ cursor: 'pointer' }} className="d-flex align-items-center">{event?.labels?.GENERAL_COMMENT} <i style={{ fontSize: '16px', margin: '3px 0 0 3px' }} className="material-icons" onClick={handleToggleComment(questionKey)}>{question?.show_comment === 1 ? 'remove_circle_outline' : 'add_circle_outline'}</i></p>
                                      {question?.show_comment === 1 && (
                                        <textarea onChange={handleComment(questionKey)} value={question.comment} cols={30} rows={5}></textarea>
                                      )}
                                    </div>
                                  )}
                                </div>
                              </React.Fragment>
                            )
                        })()
                      }
                      <div className="ebs-seperator" />
                    </React.Fragment>
                  )}
                </div>

                <div className="bottom-button text-center">
                  {props?.orderAttendee?.status === 'complete' && (
                    <a onClick={() => {
                      setLoading(true);
                      if (Number(props?.formSettings?.show_hotels) === 1) {
                        props.goToSection('manage-hotel-booking', props.order_id, props.attendee_id);
                      } else {
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
              </div>
            )}
          </React.Fragment>
        </div>
      )}
    </React.Fragment>
  )
};

export default ManageSubRegistration;