import React from 'react';
import "react-datetime/css/react-datetime.css";
import Datetime from "react-datetime";

class MyDTPicker extends React.Component {
  render() {
    return <Datetime onChange={this.props.onChange} value={this.props.value} timeFormat={this.props.showtime} dateFormat={this.props.showdate} inputProps={{ placeholder: this.props.placeholder, required: this.props.required, disabled: this.props.readOnly }} renderInput={this.renderInput} />;
  }
  renderInput(props) {
    return (
      <div className="DayPickerInput">
      <label className="label-input">
      <input {...props} placeholder=' ' />
      <span>{props.placeholder}{props.required && <em className="req">*</em>}</span>
    </label>
    </div>
    );
  }
}



const DateTime = ({label,value,showtime,showdate,onChange,required,toDate,fromDate, readOnly}) => {
  return (
    <MyDTPicker onChange={onChange} value={value} readOnly={readOnly} showtime={showtime !== undefined ? showtime : false} showdate={showdate !== undefined ? showdate : true} placeholder={label} />
  )
};

export default DateTime;
