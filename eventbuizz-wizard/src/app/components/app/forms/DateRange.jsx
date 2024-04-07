import React, { Component } from 'react';
import moment from 'moment';
import DayPickerInput from 'react-day-picker/DayPickerInput';
import 'react-day-picker/lib/style.css';
import { Translation } from "react-i18next";
import { formatDate, parseDate } from 'react-day-picker/moment';
class MyInput extends Component {
  focus = () => {
    this.input.focus();
  };
  render() {
    let { value, onFocus, onBlur, placeholder } = this.props;
    return (
      <label className="label-input">
        <input placeholder=' ' value={value} ref={el => (this.input = el)} onBlur={onBlur} onClick={onFocus} onFocus={onFocus} readOnly />
        <span>{placeholder}<em className="req">*</em></span>
      </label>
    );
  }
}

export default class DateRange extends Component {
  constructor(props) {
    super(props);
    this.handleFromChange = this.handleFromChange.bind(this);
    this.handleToChange = this.handleToChange.bind(this);
    this.state = {
      from: undefined,
      to: undefined,
    };
  }

  componentDidMount() {
    if (this.props.from_date !== undefined && this.props.to_date !== undefined) {
      this.setState({
        from: new Date(this.props.from_date),
        to: new Date(this.props.to_date)
      }, () => {
        setTimeout(() => this.props.dateList(moment(this.props.from_date).format('MM/DD/YYYY'), moment(this.props.to_date).format('MM/DD/YYYY')), 500);
      });
    } else if (this.props.datavalue !== undefined) {
      var date = this.props.datavalue;
      this.setState({
        from: new Date(date[0]),
        to: new Date(date[date.length - 1])
      });
    }
  }

  componentDidUpdate(prevProps) {
    // Typical usage (don't forget to compare props):
    if (this.props.from_date !== prevProps.from_date || this.props.to_date !== prevProps.to_date) {
      this.setState({
        from: new Date(this.props.from_date),
        to: new Date(this.props.to_date)
      });
    }
  }

  showFromMonth() {
    const { from, to } = this.state;
    if (!from) {
      return;
    }
    if (moment(to).diff(moment(from), 'months') < 2) {
      this.to.getDayPicker().showMonth(from);
    }
  }

  handleFromChange(from) {
    // Change the from date and focus the "to" input field
    this.setState({ from }, () => {
      this.props.dateList(moment(this.state.from).format('MM/DD/YYYY'), moment(this.state.to).format('MM/DD/YYYY'), this.props.index)
    });
  }

  handleToChange(to) {
    this.setState({ to }, this.showFromMonth);
    setTimeout(() => this.props.dateList(moment(this.state.from).format('MM/DD/YYYY'), moment(this.state.to).format('MM/DD/YYYY'), this.props.index), 500);
  }

  render() {
    const { from, to } = this.state;
    const modifiers = { start: from, end: to };
    const FORMAT = 'DD/MM/YYYY';
    return (
      <Translation>
        {
          t =>
            <div className="InputFromToWrapp ">
              <div className="InputFromTo DateFrom">
                <DayPickerInput
                  component={MyInput}
                  value={from}
                  placeholder={t('HM_AVAILABLE_FROM')}
                  formatDate={formatDate}
                  parseDate={parseDate}
                  dayPickerProps={{
                    selectedDays: [from, { from, to }],
                    disabledDays: [{ after: to, before: new Date() }],
                    toMonth: to,
                    modifiers,
                    numberOfMonths: 2,
                    onDayClick: () => this.to.getInput().focus(),
                  }}
                  onDayChange={this.handleFromChange}
                  format={FORMAT}
                />
              </div>
              <div className="InputFromTo DateTo">
                <DayPickerInput
                  ref={el => (this.to = el)}
                  component={MyInput}
                  value={to}
                  placeholder={t('HM_AVAILABLE_TO')}
                  formatDate={formatDate}
                  parseDate={parseDate}
                  dayPickerProps={{
                    selectedDays: [from, { from, to }],
                    disabledDays: { before: from },
                    modifiers,
                    month: from,
                    fromMonth: from,
                    numberOfMonths: 2,
                  }}
                  onDayChange={this.handleToChange}
                  format={FORMAT}
                />
              </div>
            </div>
        }
      </Translation>
    )
  }
}
