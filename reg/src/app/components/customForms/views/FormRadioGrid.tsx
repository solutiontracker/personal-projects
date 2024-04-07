import React, { useState, useRef, useEffect, useContext } from 'react';
import { shuffleArray } from '../helpers/validation';
import { EventContext } from "@/src//app/context/event/EventProvider";


const FormRadioGrid = (props: any) => {

  const { event } = useContext<any>(EventContext);

  const { data, formData, setFormData, setValidated } = props;

  const [gridQuestions, setQridQuestions] = useState([]);

  const mountRef = useRef<null | number>(null);
  useEffect(() => {
    if (mountRef.current === null) {
      setQridQuestions((data.options.shuffle === 1 && data.grid_questions.length > 0) ? shuffleArray(data.grid_questions) : data.grid_questions);
      mountRef.current = 1;
    }

  }, [])

  const onChange = (evt: any, anwser_id: any, question_id: any) => {
    let answers2 = formData[data.form_builder_section_id][data.id]['answer_id']
    let valid = true;

    if (answers2[question_id] == anwser_id) {
      delete answers2[question_id];
      console.log(answers2);
    } else {
      if (data.options.limit !== undefined && data.options.limit === 1) {
        if (Object.keys(answers2).findIndex((item) => (answers2[item] === anwser_id)) > -1) {
          return;
        }
      }
      answers2[question_id] = anwser_id;
    }

    if (data.required !== undefined && data.required === 1) {
      let answerdRows = data.grid_questions.filter((item: any) => (answers2[item.id] !== undefined ? true : false))
      valid = answerdRows.length === data.grid_questions.length ? true : false;
    }

    let newFormData = formData;
    newFormData = {
      ...formData,
      [data.form_builder_section_id]: {
        ...formData[data.form_builder_section_id],
        [data.id]: {
          ...formData[data.form_builder_section_id][data.id],
          answer_id: answers2, requiredError: false,
          validationError: !valid,
          was_answered: true,
          question_type: data.type
        }
      }
    };

    setFormData(newFormData);
    setValidated(valid);
  }

  return (
    <div className="matrix-question-wrapper">
      <h5 className="form-view-title">
        {data.title && data.title} {data.required === 1 && <em className="req">*</em>}
      </h5>
      {(data.options.description_visible === 1 && data.description !== "") && <p className="form-view-description">{data.description}</p>}
      <div className="ebs-options-view">
        <div className="generic-form">
          <div className="matrix-table-wrapper">
            <div className="matrix-table">
              <div className="martix-row matrix-header">
                <div className="matrix-box matrix-heading"></div>
                {data.answers && data.answers.map((list: any, k: any) =>
                  <div key={k} className="matrix-box">{list.label}</div>
                )}
              </div>
              {data.grid_questions && gridQuestions.map((items: any, key: any) =>
                <div key={key} className="martix-row">
                  <div className="matrix-box matrix-heading">{items.label}</div>
                  {data.answers && data.answers.map((element: any, k: any) =>
                    <div key={k} className="matrix-box">
                      <label className="label-radio">
                        <input name={`item_${key}`} defaultValue={data.index} type="checkbox" checked={(formData[data.form_builder_section_id][data.id].answer_id !== undefined &&
                          formData[data.form_builder_section_id][data.id].answer_id[items.id] !== undefined && formData[data.form_builder_section_id][data.id].answer_id[items.id] === element.id) ? true : false} value={element.id} onChange={(e) => { onChange(e, element.id, items.id) }} />
                        <span className='radio'></span>
                      </label>
                    </div>
                  )}
                </div>
              )}
            </div>
          </div>
          <div className="ebs-half-wrapper">{formData[data.form_builder_section_id][data.id]['requiredError'] === true && <p className="error-message">{event?.labels?.FORM_BUILDER_FIELD_REQUIRED}</p>}
          </div>
        </div>
      </div>
      <div className="ebs-seperator"></div>
    </div>
  )
}
export default FormRadioGrid;