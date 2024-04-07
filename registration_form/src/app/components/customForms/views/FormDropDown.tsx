import React, { useEffect, useContext } from 'react';
import { validateShortAnswer } from '../helpers/validation';
import Select from "react-select";
import { EventContext } from "@/src//app/context/event/EventProvider";

const customStyles = {
  control: (base: any) => ({
    ...base,
    height: 45,
    minHeight: 45,
    borderRadius: 4,
    border: '1px solid rgba(0, 0, 0, 0.1)',
    padding: 0,
    color: '#444',
    boxShadow: null
  }),
  option: (styles: any) => ({
    ...styles,
    whiteSpace: 'nowrap',
    overflow: 'hidden',
    textOverflow: 'ellipsis'

  })
};



const FormDropDown = (props: any) => {

  const { event } = useContext<any>(EventContext);

  const { data, formData, setFormData, setValidated, setNextSection } = props;

  const onChange = (evt: any) => {
    console.log(evt);
    const selectedAnswer = data.answers.find((answer: any) => (parseInt(answer.id) === parseInt(evt.value)));
    let newFormData = formData;
    const valid = evt.value !== "" && data.validation.type !== undefined ? validateShortAnswer(data.validation, evt.value) : true;
    newFormData = {
      ...formData,
      [data.form_builder_section_id]: {
        ...formData[data.form_builder_section_id],
        [data.id]: {
          ...formData[data.form_builder_section_id][data.id],
          answer_id: parseInt(evt.value), requiredError: false,
          validationError: !valid,
          was_answered: true,
          question_type: data.type
        }
      }
    };

    if (data.options.section_based === 1) {
      setNextSection(selectedAnswer.next_section);
    }
    setFormData(newFormData);
    setValidated(valid);
  }

  useEffect(() => {
    if (formData[data.form_builder_section_id][data.id]['answer_id'] !== undefined && formData[data.form_builder_section_id][data.id]['answer_id'] !== "" && data.options.section_based === 1) {
      const answer = data.answers.find((el: any) => (el.id === formData[data.form_builder_section_id][data.id]['answer_id']));
      setNextSection(answer.next_section);
    }
  }, [])

  const answer = formData[data.form_builder_section_id][data.id]['answer_id'] !== undefined ? data.answers.find((answer: any) => (parseInt(answer.id) === parseInt(formData[data.form_builder_section_id][data.id]['answer_id']))) : null;
  return (
    <div className="ebs-field-wrapper">
      <div className="ebs-half-wrapper">
        <div className="generic-form">
          <h5>
            {data.title && data.title} {data.required === 1 && <em className="req">*</em>}
          </h5>
          {(data.options.description_visible === 1 && data.description !== "") && <p className="form-view-description">{data.description}</p>}
          <div className="ebs-options-view">
            <Select
              menuColor='red'
              // maxMenuHeight="1"
              menuPlacement="auto"
              placeholder={event?.labels?.FORM_BUILDER_SELECT_CHOOSE}
              isSearchable={false}
              styles={customStyles}
              components={{ IndicatorSeparator: () => null }}
              onChange={(item) => onChange(item)}
              options={data.answers.map((item: any) => ({ label: item.label, value: item.id }))}
              value={(answer !== null && answer !== undefined) ? { label: answer.label, value: answer.id } : null}
              theme={theme => ({
                ...theme,
                borderRadius: 0,
                display: 'none',
                colors: {
                  ...theme.colors,
                  primary25: '#F4F4F4',
                  primary: '#E39840',
                },
              })} />
          </div>
          {formData[data.form_builder_section_id][data.id]['validationError'] === true && <p className="error-message">{data.validation.custom_error}</p>}
          {formData[data.form_builder_section_id][data.id]['requiredError'] === true && <p className="error-message">{event?.labels?.FORM_BUILDER_FIELD_REQUIRED}</p>}
        </div>
      </div>
      <div className="ebs-seperator"></div>
    </div>
  )
}
export default FormDropDown;