import React, { ReactElement, FC, useEffect, useState, useRef, useContext } from "react";
import 'react-day-picker/lib/style.css';
import { service } from '@/src/app/services/service';
import Event from '@/src/app/components/event/interface/Event';
import Loader from '@/src/app/components/forms/Loader';
import Section from '@/src/app/components/customForms/views/Section';
import { Link, useHistory } from 'react-router-dom';
import { EventContext } from "@/src//app/context/event/EventProvider";
interface Props {
  order_id: number;
  attendee_id: number;
  provider: any;
  event: Event;
  orderAttendee?: any;
  formSettings?: any;
  regFormId?: any;
  form_id?: any;
  submitForm: any;
  goToNextForm: any;
  order: any;
}

const CurrentForm: FC<Props> = (props: any): ReactElement => {

  const { section, order_id, attendee_id, form_id, regFormId, submitForm, goToNextForm, provider, order } = props;

  const { event, updateEvent } = useContext<any>(EventContext);

  const [sectionHistory, setSectionHistory] = useState<any>([]);

  const [width, setWidth] = useState(0);

  const [loading, setLoading] = useState(true);

  const [formData, setFormData] = useState<any>(null);

  const [data, setData] = useState<any>({form:null, result:null});
  
  const [sections, setSections] = useState<any>([]);
  
  const [active, setActive] = useState(0);

  const [stepType, setStepType] = useState<any>("next");

  const mounted = useRef(false);

  const style = {
    control: (base: any) => ({
      ...base,
      boxShadow: 'none'
    })
  };

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
      mounted.current = false;
    };
  }, []);

  useEffect(() => {
    if(data.form !== null){
      setSections([...data.form.sections]);
        setFormData(
          data.form.sections.reduce((ack:any, section:any)=> 
          ({...ack, 
            [section.id]: section.questions ? 
            section.questions.reduce((ack:any, question:any)=> ({...ack, 
              [question.id]: data.result[question.id] !== undefined ? {
                answer_id: data.result[question.id].answer_id,
                answer_value: data.result[question.id].answer_value,
                requiredError:false,
                validationError:false, 
                question_type:question.type, 
                was_answered:true
              } :
              {requiredError:false,
               validationError:false, 
               question_type:question.type, 
               answer_id:(question.type === "checkboxes") ? [] : (question.type === "tick_box_grid" || question.type === "multiple_choice_grid") ? {} : "", answer_value:'' }
             }) , {})
              : [] 
            }),{}));
    }
  }, [data]);
  

  useEffect(() => {
    if (order_id && attendee_id) {
      service.post(`${process.env.REACT_APP_API_URL}/organizer/form-builder/getFormAndResult/${event.id}/${regFormId}`, {form_id:form_id, order_id:order_id, attendee_id:attendee_id})
        .then(
          response => {
            console.log(response)
            if(response.status && mounted.current) {
              if(response.data.form.sections.length > 0){
                setData({form: response.data.form, result:response.data.result});
              }else{
                goToNextForm();
              }
            }
          },
          error => { }
        );
    }
  }, [order_id, attendee_id, form_id]);
 
 
   
  useEffect(() => {
    
    if(stepType === 'back'){
        const newSectionHistory = [...sectionHistory];
        newSectionHistory.pop();
        console.log(newSectionHistory);
        setSectionHistory(newSectionHistory)
    }

    if(stepType === 'next'){
        setSectionHistory((prevState:any)=>([...prevState, { previous:prevState.length > 0 ? prevState[prevState.length -1].current : 0, current:active}]));
    }
    }, [active])

  return (
    <React.Fragment>
      <div className="ebs-eventbuizz-form-wrapper">
        {data.form && (
          <React.Fragment>
              {((Number(event?.order?.order_detail?.order?.edit_mode) === 1) || (order?.edit_mode === 1)) && (
                  <div className="col-12 text-right">
                    <Link to={`/${event.url}/${provider}/order-summary/${order_id}`} className="ebs-back-summary"><i className="material-icons">keyboard_backspace</i>{event?.labels?.REGISTRATION_FORM_BACK_TO_SUMMARY}</Link>
                  </div>
              )}
              <div className="ebs-form-title">
                {data.form.title && <div className="row d-flex ebs-title-box align-items-center">
                  <div className="col-6">
                    <h2 className="section-title">
                      {data.form.title}
                    </h2>
                  </div>
                </div>}
                {data.form.description && (
                  <div className="row">
                    <div className="col-12">
                      <p className="ebs-attendee-caption">
                        {data.form.description}
                      </p>  
                    </div>
                  </div>
                )}
              </div>
          </React.Fragment>
          )}
            {sections.length > 0 && sections.map((section:any, index:any)=>(
              active === index && 
              <div key={index} className="wrapper-box"><Section section={section} sections={sections} active={active} setStepType={setStepType} sectionHistory={sectionHistory} setActive={setActive} formData={formData} regFormId={regFormId} setFormData={setFormData} attendee_id={attendee_id} order_id={order_id} event_id={event.id} labels={event.labels} submitFormToServer={submitForm} /></div>
            ))}
      </div>
    </React.Fragment>
  )
};

export default CurrentForm;