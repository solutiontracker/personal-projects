import React, { useContext } from 'react';
import Input from '../../forms/Input';
import { validateShortAnswer } from '../helpers/validation';
import { EventContext } from "@/src//app/context/event/EventProvider";

const FormShortAnswer = (props: any) => {
  const { event } = useContext<any>(EventContext);

  const { data, formData, setFormData, setValidated } = props;

  const onChange = (evt: any) => {
    // console.log(evt);
    let newFormData = formData;
    const valid = evt.currentTarget.value !== "" && data.validation.type !== undefined ? validateShortAnswer(data.validation, evt.currentTarget.value) : true;
    newFormData = {
      ...formData,
      [data.form_builder_section_id]: {
        ...formData[data.form_builder_section_id],
        [data.id]: {
          ...formData[data.form_builder_section_id][data.id],
          answer_value: evt.currentTarget.value, requiredError: false,
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
    <div className="ebs-field-wrapper">
      <div className="ebs-half-wrapper">
        <div className="generic-form">
          <h5>
            {data.title && data.title} {data.required === 1 && <em className="req">*</em>}
          </h5>
          {(data.options.description_visible === 1 && data.description !== "") && <p className="form-view-description">{data.description}</p>}
          <div className="ebs-options-view">
            <div className="ebs-input-response">
              <Input
                label={event?.labels?.FORM_BUILDER_YOUR_ANSWER}
                type='text'
                value={formData[data.form_builder_section_id][data.id]['answer_value']}
                onChange={(e: any) => { onChange(e) }}
              />
              {formData[data.form_builder_section_id][data.id]['validationError'] === true && <p className="error-message">{data.validation.custom_error}</p>}
              {formData[data.form_builder_section_id][data.id]['requiredError'] === true && <p className="error-message">{event?.labels?.FORM_BUILDER_FIELD_REQUIRED}</p>}
            </div>
          </div>
        </div>
      </div>
      <div className="ebs-seperator"></div>
    </div>
  )
}
export default FormShortAnswer;