import React, { useState, useContext } from 'react';
import Select from "react-select";
import DateTime from '@/src/app/components/forms/DateTime';
import moment from 'moment';
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

const _dropdown_min: any = [
  { value: 'AM', label: 'AM' },
  { value: 'PM', label: `PM` }
];

const FormTimebox = (props: any) => {

  const { event } = useContext<any>(EventContext);

  const { data, setFormData, formData, setValidated } = props;

  const [duration, setDuration] = useState(formData[data.form_builder_section_id][data.id].answer_value !== undefined ? {
    hours: formData[data.form_builder_section_id][data.id].answer_value.split(":")[0] !== undefined ? formData[data.form_builder_section_id][data.id].answer_value.split(":")[0] : '',
    minutes: formData[data.form_builder_section_id][data.id].answer_value.split(":")[1] !== undefined ? formData[data.form_builder_section_id][data.id].answer_value.split(":")[1] : '',
    seconds: formData[data.form_builder_section_id][data.id].answer_value.split(":")[2] !== undefined ? formData[data.form_builder_section_id][data.id].answer_value.split(":")[2] : '',
  } : { hours: "00", minutes: "00", seconds: "00" });

  console.log(formData[data.form_builder_section_id][data.id]);

  const handleCheckDate = (e: any) => {
    let _valid = true;
    if (e) {
      _valid = moment(e, 'hh:mm A').isValid();
    }
    let newFormData = formData;
    newFormData = {
      ...formData,
      [data.form_builder_section_id]: {
        ...formData[data.form_builder_section_id],
        [data.id]: {
          ...formData[data.form_builder_section_id][data.id],
          answer_value: e._isAMomentObject !== undefined && e._isAMomentObject === true ? e.format('hh:mm A') : '', requiredError: false,
          validationError: !_valid,
          was_answered: true,
          question_type: data.type
        }
      }
    };

    setFormData(newFormData);
  };

  const handleInputChange = (target: any, type: any) => {
    const value = target.value;
    if (value < 0) {
      return;
    }
    let _valid = true;
    if (value.includes('e')) {
      _valid = false
    }
    if (type === 'HOUR') {
      if (Number(value) > 99) {
        return;
      }
    }
    if (type === 'HOUR') {
      if(Number(value) > 99){
        return;
      } 
    }
    if (type === 'MINUTE') {
      if (Number(value) > 59) {
        _valid = false
      }
    } if (type === 'SECONDS') {
      if (Number(value) > 59) {
        _valid = false
      }
    }
    const newDuration = { ...duration, [target.name]: value };
    setDuration(newDuration);
    console.log(duration);

    // let durationValue = Object.keys(newDuration).reduce((ack, key) => (newDuration[key] !== "0" ? `${ack}:${newDuration[key]}`: ack ),''); 
    let hours = newDuration.hours !== '' ? `${newDuration.hours}` : '00';
    let minutes = newDuration.minutes !== '' ? `:${newDuration.minutes}` : ':00';
    let seconds = newDuration.seconds !== '' ? `:${newDuration.seconds}` : ':00';
    let durationValue = `${hours}${minutes}${seconds}`;

    if (Number(newDuration.minutes) > 59 || Number(newDuration.seconds) > 59) {
      _valid = false;
    }

    if (data.required === 1 && durationValue === '00:00:00') {
      durationValue = '';
    }

    if(data.required === 1 && durationValue === '00:00:00'){
      durationValue='';
    }

    console.log(durationValue)
    let newFormData = formData;
    newFormData = {
      ...formData,
      [data.form_builder_section_id]: {
        ...formData[data.form_builder_section_id],
        [data.id]: {
          ...formData[data.form_builder_section_id][data.id],
          answer_value: durationValue, requiredError: false,
          validationError: (!_valid),
          was_answered: true,
          question_type: data.type
        }
      }
    };

    setFormData(newFormData);
  }

  const formatInputCheck = (e: any) => {
    // Prevent characters that are not numbers ("e", ".", "+" & "-") ✨
    let checkIfNum;
    if (e.key !== undefined) {
      // Check if it's a "e", ".", "+" or "-"
      checkIfNum = e.key === "e" || e.key === "." || e.key === "+" || e.key === "-";
    }
    else if (e.keyCode !== undefined) {
      // Check if it's a "e" (69), "." (190), "+" (187) or "-" (189)
      checkIfNum = e.keyCode === 69 || e.keyCode === 190 || e.keyCode === 187 || e.keyCode === 189;
    }
    return checkIfNum && e.preventDefault();
  }

  return (
    <div className="ebs-field-wrapper">
      <div className="ebs-half-wrapper">
        <div className="generic-form">
          <h5>
            {data.title && data.title} {data.required === 1 && <em className="req">*</em>}
          </h5>
          {(data.options.description_visible === 1 && data.description !== "") && <p className="form-view-description">{data.description}</p>}
          {data.options.time_type === "TIME" && <DateTime
            showtime={'hh:mm A'}
            showdate={false}
            label={event?.labels?.FORM_BUILDER_SELECT_TIME}
            required={true}
            initialValue={formData[data.form_builder_section_id][data.id]['answer_value'] !== undefined ? formData[data.form_builder_section_id][data.id]['answer_value'] : ""}
            onChange={handleCheckDate}
            clear={1}
          />}
          {data.options.time_type === "DURATION" &&
            <div className="ebs-time-form-view">
              <div className="ebs-time-grid d-flex align-items-center ebs-duration-grid">
                <div className="ebs-box">
                  <div className="ebs-title">{event?.labels?.FORM_BUILDER_HOURS}</div>
                  <input minLength={2} maxLength={2} name='hours' onKeyDown={formatInputCheck} onFocus={(e) => e.target.select()} onChange={(e) => handleInputChange(e.target, 'HOUR')} type="number" placeholder="00" value={duration.hours} />
                </div>
                <div className="ebs-box-sep">:</div>
                <div className="ebs-box">
                  <div className="ebs-title">{event?.labels?.FORM_BUILDER_MINUTES}</div>
                  <input minLength={2} maxLength={2} name='minutes' onKeyDown={formatInputCheck} onFocus={(e) => e.target.select()} onChange={(e) => handleInputChange(e.target, 'MINUTE')} type="number" placeholder="00" value={duration.minutes} />
                </div>
                <div className="ebs-box-sep">:</div>
                <div className="ebs-box">
                  <div className="ebs-title">{event?.labels?.FORM_BUILDER_SECONDS}</div>
                  <input minLength={2} maxLength={2} name='seconds' onKeyDown={formatInputCheck} onFocus={(e) => e.target.select()} onChange={(e) => handleInputChange(e.target, 'SECONDS')} type="number" placeholder="00" value={duration.seconds} />
                </div>
              </div>
            </div>}

          {formData[data.form_builder_section_id][data.id]['validationError'] === true && <p className="error-message">{event?.labels?.FORM_BUILDER_INVALID_DURATION_INPUT}</p>}
          {formData[data.form_builder_section_id][data.id]['requiredError'] === true && <p className="error-message">{event?.labels?.FORM_BUILDER_FIELD_REQUIRED}</p>}
        </div>
      </div>
      <div className="ebs-seperator"></div>
    </div>
  )
}
export default FormTimebox;