import React, { Component } from 'react';
import DayPickerInput from 'react-day-picker/DayPickerInput';
import moment from 'moment'
import {
  formatDate,
  parseDate,
} from "react-day-picker/moment";
import 'react-day-picker/lib/style.css';

class MyInput extends Component {
  focus = () => {
    this.input.focus();
  };
  render() {
    let { value, onFocus, onBlur, placeholder, onChange, required, readOnly } = this.props;
    return (
      <label className="label-input date" >
        {
          readOnly ? (
            <input type="text" placeholder=' ' value={value && value !== 'Invalid date' ? value : ''} ref={el => (this.input = el)} readOnly />
          ) : (
              <input type="text" placeholder=' ' value={value && value !== 'Invalid date' ? value : ''} ref={el => (this.input = el)} onBlur={onBlur} onClick={onFocus} onChange={onChange} readOnly />
            )
        }

        <span>{placeholder} {required && <em className="req">*</em>}</span>
      </label>
    );
  }
}

export class DateTime extends Component {

  state = {
    daterange: [],
    highlighted: (this.props.highlighted ? this.props.highlighted: [])
  }

  handleReset = () => {
    this.props.onChange('cleardate')
  }

  componentDidMount() {
    const date = this.state.highlighted;
    if (date && date.length === 2) {
      this.setState({
        daterange: [{
          after: new Date(moment(date[0]).subtract(1, 'days')),
          before: new Date(moment(date[1]).add(1, 'days'))
        }],
      })
    }
  }
  componentDidUpdate(prevProps) {
    if (prevProps.value !== this.props.value) {
      const date = this.state.highlighted;
      if (date && date.length === 2) {
        this.setState({
          daterange: [{
            after: new Date(moment(date[0]).subtract(1, 'days')),
            before: new Date(moment(date[1]).add(1, 'days'))
          }],
        })
      }
    }
  }

  static getDerivedStateFromProps(props, state) {
    if (props.highlighted !== state.highlighted) {
      const date = props.highlighted;
      if (date && date.length === 2) {
        if (date[0] === "" || date[1] === "") {
          return {
            daterange: {},
            highlighted: props.highlighted
          };
        } else {
          return {
            daterange: {
              after: new Date(moment(date[0]).subtract(1, 'days')),
              before: new Date(moment(date[1]).add(1, 'days'))
            },
            highlighted: props.highlighted
          };
        }
      } else {
        return {
          highlighted: props.highlighted
        };
      }
    }
    // Return null to indicate no change to state.
    return null;
  }

  render() {
    const { label, value, onChange, required, fromDate = '', toDate = '', readOnly } = this.props;
    const FORMAT = 'DD/MM/YYYY';
    const MyLink = React.forwardRef((props, ref) => <MyInput readOnly={readOnly} {...props} ref={ref} required={required} />);
    let dayPickerInputRef = null;
    let picker;
    if (this.state.highlighted) {
      picker = {
        disabledDays: [{ after: toDate, before: fromDate }],
        todayButton: 'RESET',
        onTodayButtonClick: (day) => { this.handleReset(); dayPickerInputRef.hideDayPicker(); },
        month: fromDate,
        selectedDays: this.state.daterange,
        modifiers: {
          highlighted: new Date(moment(this.props.value))
        }
      }
    } else {
      picker = {
        disabledDays: [{ after: toDate, before: fromDate }],
        todayButton: 'RESET',
        onTodayButtonClick: (day) => { this.handleReset(); dayPickerInputRef.hideDayPicker(); },
        month: fromDate,
      }
    }
    return (
      <DayPickerInput
        ref={ref => (dayPickerInputRef = ref)}
        component={MyLink}
        placeholder={label}
        parseDate={parseDate}
        formatDate={formatDate}
        format={FORMAT}
        value={(value ? formatDate(new Date(value), FORMAT) : null)}
        dayPickerProps={picker}
        onDayChange={onChange}
      />
    )
  }
}

export default DateTime;