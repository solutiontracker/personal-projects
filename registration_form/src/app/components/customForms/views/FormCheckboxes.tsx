import React, { useContext } from 'react'
import { validateShortAnswer } from '../helpers/validation';
import { EventContext } from "@/src//app/context/event/EventProvider";

const FormCheckboxes = (props: any) => {

  const { event } = useContext<any>(EventContext);

  const { data, formData, setFormData, setValidated } = props;
  const onClick = (new_answer: any) => {
    let answers2 = formData[data.form_builder_section_id][data.id]['answer_id']
    if (answers2.findIndex((item: any) => (item == parseInt(new_answer))) > -1) {
      answers2 = answers2.filter((item: any) => (item != parseInt(new_answer)))
    }
    else {
      answers2 = [...answers2, parseInt(new_answer)];
    }
    let newFormData = formData;
    const valid = new_answer !== "" && data.validation.type !== undefined ? validateShortAnswer(data.validation, answers2) : true;
    newFormData = {
      ...formData,
      [data.form_builder_section_id]: {
        ...formData[data.form_builder_section_id],
        [data.id]: {
          ...formData[data.form_builder_section_id][data.id],
          answer_id: answers2,
          was_answered: true,
          requiredError: false,
          validationError: !valid,
          question_type: data.type
        }
      }
    };

    setFormData(newFormData);
    setValidated(valid);
  }
  return (
    <div className="ebs-field-wrapper">
      <div className="ebs-half-wrapper">
        <div className="generic-form">
          <h5>
            {(data.title !== "") && data.title} {data.required === 1 && <em className="req">*</em>}
          </h5>
          {(data.options.description_visible === 1 && data.description !== "") && <p className="form-view-description">{data.description}</p>}
          <div className="radio-check-field">
            <div className="ebs-field-wrapp">
              {data.answers && data.answers.map((element: any, key: any) =>
                <label key={key}
                  className={(formData[data.form_builder_section_id][data.id]['answer_id'] !== undefined && formData[data.form_builder_section_id][data.id]['answer_id'].findIndex((item: any) => (item == parseInt(element.id))) > -1) ? "checked" : ""}
                  onClick={() => (onClick(element.id))}
                >
                  <span>{element.label}</span>
                </label>
              )}

            </div>
          </div>
          {formData[data.form_builder_section_id][data.id]['validationError'] === true && <p className="error-message">{data.validation.custom_error}</p>}
          {formData[data.form_builder_section_id][data.id]['requiredError'] === true && <p className="error-message">{event?.labels?.FORM_BUILDER_FIELD_REQUIRED}</p>}
        </div>
      </div>
      <div className="ebs-seperator"></div>
    </div>
  )
}
export default FormCheckboxes;