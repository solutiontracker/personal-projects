import React, { useEffect, useContext } from 'react'
import { EventContext } from "@/src//app/context/event/EventProvider";

const FormMultipleChoice = (props: any) => {

  const { event } = useContext<any>(EventContext);

  const { data, formData, setFormData, setValidated, setNextSection } = props;
  const onClick = (id: any, next_section: any) => {
    // console.log(evt);
    let newFormData = formData;
    newFormData = {
      ...formData,
      [data.form_builder_section_id]: {
        ...formData[data.form_builder_section_id],
        [data.id]: {
          ...formData[data.form_builder_section_id][data.id],
          answer_id: parseInt(id), requiredError: false,
          validationError: false,
          was_answered: true,
          question_type: data.type
        }
      }
    };
    if (data.options.section_based === 1) {
      setNextSection(next_section);
    }
    setFormData(newFormData);
  }

  useEffect(() => {
    if (formData[data.form_builder_section_id][data.id]['answer_id'] !== undefined && formData[data.form_builder_section_id][data.id]['answer_id'] !== "" && data.options.section_based === 1) {
      const answer = data.answers.find((el: any) => (el.id === formData[data.form_builder_section_id][data.id]['answer_id']));
      setNextSection(answer.next_section);
    }
  }, [])


  return (
    <div className="ebs-field-wrapper">
      <div className="ebs-half-wrapper">
        <div className="generic-form">
          <h5>
            {data.title && data.title} {data.required === 1 && <em className="req">*</em>}
          </h5>
          {(data.options.description_visible === 1 && data.description !== "") && <p className="form-view-description">{data.description}</p>}
          <div className="radio-check-field style-radio">
            {data.answers && data.answers.map((element: any, key: any) => (
              <label key={key}
                className={(formData[data.form_builder_section_id][data.id]['answer_id'] !== undefined && formData[data.form_builder_section_id][data.id]['answer_id'] === element.id) ? "checked" : ""}
                onClick={(e) => { onClick(element.id, element.next_section) }}
              >
                <span>{element.label && element.label}</span>
              </label>
            ))}
          </div>
          {formData[data.form_builder_section_id][data.id]['validationError'] === true && <p className="error-message">{data.validation.custom_error}</p>}
          {formData[data.form_builder_section_id][data.id]['requiredError'] === true && <p className="error-message">{event?.labels?.FORM_BUILDER_FIELD_REQUIRED}</p>}
        </div>
      </div>
      <div className="ebs-seperator"></div>
    </div>
  );
}
export default FormMultipleChoice;