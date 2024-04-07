import React from 'react';
import DayPickerInput from 'react-day-picker/DayPickerInput';
import 'react-day-picker/lib/style.css';
import moment from 'moment-timezone';
import MomentLocaleUtils, {
  formatDate,
  parseDate,
} from 'react-day-picker/moment';
import 'moment/locale/da';

type State = {
  from: any;
  to: any;
  min: any;
  max: any;
  locale: any;
  label_from: any;
  label_to: any;
};

type MyProps = {
  value: any;
  onFocus: any;
  onBlur: any;
  placeholder: any;
};

class MyInput extends React.Component<MyProps> {

  input: any;

  focus = () => {
    this.input.focus();
  };

  render() {
    const { value, onFocus, onBlur, placeholder }: MyProps = this.props;
    return (
      <label className="label-input">
        <input placeholder=' ' value={value} ref={el => (this.input = el)} onBlur={onBlur} onClick={onFocus} onFocus={onFocus} readOnly />
        <span>{placeholder}<em className="req">*</em></span>
      </label>
    );
  }
}

export default class DateRange extends React.Component<any, State>  {

  to: any;

  // eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
  constructor(props: any) {
    super(props);
    this.handleFromChange = this.handleFromChange.bind(this);
    this.handleToChange = this.handleToChange.bind(this);
    this.state = {
      label_from: this.props.label_from ? this.props.label_from : 'Check in',
      label_to: this.props.label_to ? this.props.label_to : 'Check out',
      from: this.props.from,
      to: this.props.to,
      min: this.props.min,
      max: this.props.max,
      locale: this.props.locale ? this.props.locale : 'en'
    };
  }

  showFromMonth(): any {
    const { from, to } = this.state;
    if (!from) {
      return;
    }
    if (moment(to).diff(moment(from), 'months') < 2) {
      this.to.getDayPicker().showMonth(from);
    }
  }

  // eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
  handleFromChange(from: any): any {
    // Change the from date and focus the "to" input field
    this.setState({ from });
    this.props.onChange('from', from);
  }

  // eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
  handleToChange(to: any): any {
    this.setState({ to }, this.showFromMonth);
    this.props.onChange('to', to);
  }

  componentDidUpdate(prevProps: any, prevState: any) {
    if (prevProps.from !== this.props.from || prevProps.to !== this.props.to || prevProps.min !== this.props.min && prevProps.max !== this.props.max) {
      this.setState({
        from: this.props.from ? moment(this.props.from).tz(this.props.eventTimezone).toDate() : '',
        to: this.props.to ? moment(this.props.to).tz(this.props.eventTimezone).toDate() : '',
        min: this.props.min ? moment(this.props.min).tz(this.props.eventTimezone).toDate() : '',
        max: this.props.max ? moment(this.props.max).tz(this.props.eventTimezone).toDate() : ''
      })
    }
  }

  // eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
  render() {

    const { from, to, max, min, label_from, label_to } = this.state;
    
    const modifiers = { start: from, end: to };

    return (
      <>
        <div className="date-booking">
          <DayPickerInput
            component={MyInput}
            value={from}
            placeholder={label_from}
            formatDate={formatDate}
            parseDate={parseDate}
            dayPickerProps={{
              month: new Date(min),
              selectedDays: [from, { from, to }],
              disabledDays: [{ after: max ? max : to, before: min ? min : new Date() }],
              toMonth: to,
              modifiers,
              locale: this.state.locale,
              localeUtils: MomentLocaleUtils,
              numberOfMonths: 2,
              onDayClick: () => this.to.getInput().focus(),
            }}
            onDayChange={this.handleFromChange}
            onDayPickerShow={() => {
              this.setState({ from: '', to: '' });
              this.props.onChange('from', '');
              this.props.onChange('to', '');
            }}
          />
        </div>
        <div className="date-booking check-out">
          <DayPickerInput
            ref={(el:any) => (this.to = el)}
            component={MyInput}
            value={to}
            placeholder={label_to}
            formatDate={formatDate}
            parseDate={parseDate}
            dayPickerProps={{
              selectedDays: [from, { from, to }],
              disabledDays: { after: max ? max : to, before: from },
              modifiers,
              month: from,
              fromMonth: from,
              locale: this.state.locale,
              localeUtils: MomentLocaleUtils, 
              numberOfMonths: 2,
            }}
            onDayChange={this.handleToChange}
          />
        </div>
      </>
    );
  }

}
