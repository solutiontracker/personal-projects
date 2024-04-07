import React, { ReactElement, FC, useRef } from 'react';
import "react-datetime/css/react-datetime.css";
import Datetime from "react-datetime";

type Props = {
  value: any;
  onChange: any;
  onBlur: any;
  placeholder: any;
  required: any;
  showtime: boolean;
  showdate: boolean;
  locale?: any;
  initialValue?: any;
  clear?:any
}

const MyDTPicker: FC<any> = (props: Props): ReactElement => {

  const textInput = useRef<any>(null);

  const renderView = (mode: any, renderDefault: any, showTime: any,showDate:any,) => {
    // Only for years, months and days view
    return (
      <div className="ebs-date-wrapper">
        {showTime !== 0 && showDate !== 0 && <div className="ebs-top-caption">
          <label className='ebs-days'> <input defaultChecked onChange={() => textInput.current?.navigate('days')} type="radio" name="calendar" />
            <span><i className="material-icons">calendar_month</i></span>
          </label>
          <label className='ebs-time'> <input onChange={() => textInput.current?.navigate('time')} type="radio" name="calendar" />
            <span><i className="material-icons">schedule</i></span>
          </label>
        </div>}
        {renderDefault()}
      </div>
    );
  };

  const renderInput = (props: any) => {
    return (
      <div className="DayPickerInput">
        <label className={`label-input ${props.timeOnly ? 'ebs-time-icon' : ''}`}>
          <input readOnly {...props} placeholder=' ' />
          <span>{props.placeholder}{props.required && <em className="req">*</em>}</span>
          {props.clear === 1 && props.value !== '' && <div className='clear-date-btn' onClick={() => {props.onChange('')}}>Clear</div>}
        </label>
      </div>
    );
  }

  return <Datetime locale={props?.locale !== undefined ? props?.locale : 'en'} initialValue={props.initialValue} ref={textInput} renderView={(mode, renderDefault) => renderView(mode, renderDefault, props.showtime,props.showdate)} initialViewMode={props.showdate ? 'days' : 'time'} closeOnSelect={props.showtime ? false : true} onChange={props.onChange} value={props.value} timeFormat={props.showtime} dateFormat={props.showdate} inputProps={{ placeholder: props.placeholder, required: props.required, timeOnly: props.showtime && !props.showdate, clear:props.clear }} renderInput={renderInput} />;
};

type DateTimeProps = {
  label?: any;
  value?: any;
  showtime?: any;
  showdate?: any;
  onChange?: any;
  required?: any;
  toDate?: any;
  fromDate?: any;
  locale?: any;
  initialValue?: any;
  clear?:any
}

const DateTime: FC<DateTimeProps> = (props): ReactElement => {
  return (
    <MyDTPicker locale={props?.locale !== undefined ? props?.locale : 'en'} clear={props.clear} initialValue={props.initialValue} onChange={props.onChange} value={props.value} showtime={props.showtime !== undefined ? props.showtime : false} showdate={props.showdate !== undefined ? props.showdate : true} placeholder={props.label} />
  )
};

export default DateTime;
