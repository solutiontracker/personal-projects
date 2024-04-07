import React, { useState, useEffect, useContext } from 'react';
import DateTime from '@/src/app/components/forms/DateTime';
import { EventContext } from "@/src//app/context/event/EventProvider";


const customStyles = {
  control: (base: any) => ({
    ...base,
    height: 35,
    minHeight: 35,
    borderRadius: 0,
    border: 'none',
    padding: 0,
    color: '#444',
    boxShadow: null
  })
};

const _dropdown_min = [
  { value: 'AM', label: 'AM' },
  { value: 'PM', label: `PM` }
];

const FormDatebox = (props: any) => {

  const { event } = useContext<any>(EventContext);

  const { data, setFormData, formData, setValidated } = props;

  const [state, setState] = useState<any>(0);

  const [error, setError] = useState<any>(false);

  const handleCheckDate = (e: any) => {
    console.log(e);
    const format = `MM/DD${data.options.year === 1 ? '/YYYY' : ''} ${data.options.time === 1 ? "HH:mm:ss" : ''}`
    let newFormData = formData;
    newFormData = {
      ...formData,
      [data.form_builder_section_id]: {
        ...formData[data.form_builder_section_id],
        [data.id]: {
          ...formData[data.form_builder_section_id][data.id],
          answer_value: e._isAMomentObject !== undefined && e._isAMomentObject === true ? e.format(format) : '', requiredError: false,
          validationError: false,
          was_answered: true,
          question_type: data.type
        }
      }
    };
    setFormData(newFormData);
  };
  return (
    <div className="ebs-field-wrapper">
      <div className="ebs-half-wrapper">
        <div className="generic-form">
          <h5>
            {data.title && data.title} {data.required === 1 && <em className="req">*</em>}
          </h5>
          {(data.options.description_visible === 1 && data.description !== "") && <p className="form-view-description">{data.description}</p>}
          <DateTime
            showtime={data.options.time === 1 ? "HH:mm:ss" : 0}
            showdate={data.options.year === 1 ? 'MM/DD/YYYY' : 'MM/DD'}
            label={event?.labels?.FORM_BUILDER_SELECT_DATE}
            required={true}
            value={formData[data.form_builder_section_id][data.id]['answer_value'] !== undefined ? formData[data.form_builder_section_id][data.id]['answer_value'] : ""}
            onChange={handleCheckDate}
            clear={1}
          />
          {/* {error && <p className="error-message">This question is required</p>} */}
          {formData[data.form_builder_section_id][data.id]['requiredError'] === true && <p className="error-message">{event?.labels?.FORM_BUILDER_FIELD_REQUIRED}</p>}
        </div>
      </div>

      <div className="ebs-seperator"></div>
    </div>
  )
}

export default FormDatebox