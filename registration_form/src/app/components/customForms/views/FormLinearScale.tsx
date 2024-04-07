import React, { useContext } from 'react';
import { validateShortAnswer } from '../helpers/validation';
import { EventContext } from "@/src//app/context/event/EventProvider";


const FormLinearScale = (props: any) => {

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

      <div className="generic-form">
        <h5>
          {data.title && data.title} {data.required === 1 && <em className="req">*</em>}
        </h5>
        {(data.options.description_visible === 1 && data.description !== "") && <p className="form-view-description">{data.description}</p>}
        <div className="ebs-linear-scale">
          <div className="ebs-linear-view">
            <div className="ebs-linear-view-wrapper d-flex text-center">
              <div className="ebs-linear-box d-flex ebs-linear-caption">
                <div className="ebs-label" ></div>
                <div className="ebs-value"><div className="ebs-value-inner">{data.options.min_label}</div></div>
              </div>
              {Array.apply(null, Array((Number(data.options.max) - (Number(data.options.min) === 0 ? 0 : 1)) + 1)).map((e, i) => (
                <div key={i} className="ebs-linear-box d-flex">
                  <div className="ebs-label" >{i + (Number(data.options.min) === 0 ? 0 : 1)}</div>
                  <div style={{ minHeight: '3.5em' }} >
                    <label className="label-radio">
                      <input name={`item_${data.id}`} defaultValue={data.index} checked={parseInt(formData[data.form_builder_section_id][data.id]['answer_value']) == Number(i)} type="radio" value={i} onChange={(e) => { onChange(e) }} />
                      <span></span>
                    </label>
                  </div>
                </div>
              ))}
              <div className="ebs-linear-box d-flex ebs-linear-caption">
                <div className="ebs-label" ></div>
                <div className="ebs-value"><div className="ebs-value-inner">{data.options.max_label}</div></div>
              </div>
            </div>
          </div>
        </div>
        <div className="ebs-half-wrapper">
          {formData[data.form_builder_section_id][data.id]['requiredError'] === true && <p className="error-message">{event?.labels?.FORM_BUILDER_FIELD_REQUIRED}</p>}
        </div>
      </div>
      <div className="ebs-seperator"></div>
    </div>
  )
}
export default FormLinearScale;