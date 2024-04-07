import React, { Component } from 'react'
import moment from 'moment';
import { withTranslation } from "react-i18next";

class Picker extends Component {
  constructor(props) {
    super(props);
    this.state = {
      time: this.props.seconds ? `${this.props.value ? this.props.value : '00:00:00'}` : `${this.props.value ? this.props.value : '00:00'}`,
      hour: true,
      minutes: false,
      seconds: false
    }
  }
  handleEvent = (element, time) => e => {
    const statetime = this.state.time.split(':');
    if (element === 'hour') {
      this.setState({
        time: this.props.seconds ? `${time}:${statetime[1]}:${statetime[2]}` : `${time}:${statetime[1]}`
      }, () => {
        this.props.timeChange(this.state.time);
      })
    } else if (element === 'minutes') {
      this.setState({
        time: this.props.seconds ? `${statetime[0]}:${time}:${statetime[2]}` : `${statetime[0]}:${time}`
      }, () => {
        this.props.timeChange(this.state.time)
      })
    } else {
      this.setState({
        time: `${statetime[0]}:${statetime[1]}:${time}`
      }, () => {
        this.props.timeChange(this.state.time)
      })
    }

  }
  handleNavigation = element => e => {
    e.preventDefault();
    this.setState({
      hour: element === 'hour' ? true : false,
      minutes: element === 'minutes' ? true : false,
      seconds: element === 'seconds' ? true : false
    })
  }
  render() {
    const { value, seconds } = this.props;
    const splitvalue = value && value.split(':');
    const Hour = () => {
      const n = 24;
      return (
        <div className="hour-wrapper time-content-wrapper">
          {[...Array(n)].map((e, i) => {
            const digit = i < 10 ? '0' + i : i;
            return (
              <span
                onClick={this.handleEvent('hour', digit)}
                className={splitvalue[0] === digit.toString() ? "active time-card card-hour" : "time-card card-hour"}
                key={i}>{digit}</span>
            )
          })}
        </div>
      )
    }
    const MinutesSecond = ({ type }) => {
      const n = 12;
      const index = type === 'minutes' ? 1 : 2;
      return (
        <div className="minutes-wrapper time-content-wrapper">
          {[...Array(n)].map((e, i) => {
            const digit = i < 2 ? '0' + i * 5 : i * 5;
            return (
              <span
                onClick={this.handleEvent(type, digit)}
                className={splitvalue[index] === digit.toString() ? "active time-card card-hour" : "time-card card-hour"}
                key={i}>{digit}</span>
            )
          })}
        </div>
      )
    }
    return (
      <div className='time-wrapper'>
        <div className="navigation-time-wrapper">
          <span onClick={this.handleNavigation('hour')} className={this.state.hour ? 'active' : ''}>{this.props.hourLabel}</span>
          <span onClick={this.handleNavigation('minutes')} className={this.state.minutes ? 'active' : ''}>{this.props.MinuteLabel}</span>
          {seconds && <span onClick={this.handleNavigation('seconds')} className={this.state.seconds ? 'active' : ''}>{this.props.SecondLabel}</span>}
        </div>
        <div className="main-time-area">
          {this.state.hour && <Hour />}
          {this.state.minutes && <MinutesSecond type="minutes" />}
          {this.state.seconds && <MinutesSecond type="seconds" />}
        </div>
      </div>
    )
  }
}

class Timepicker extends Component {
  constructor(props) {
    super(props);
    this.state = {
      showPicker: false,
      value: this.props.value && this.props.value
    }
  }
  componentDidUpdate(prevProps) {
    if (this.props.value !== prevProps.value) {
      if (this.props.value !== undefined) {
        var value = this.props.value;
        this.setState({
          value: value
        })
      }
    }
  }
  inputChange = e => {
    const validate = moment(e.target.value, "HH:mm:ss").format("hh:mm A");
    this.props.onChange(this.props.stateName, validate, this.props.validateName)
  }
  handleOpen = event => {
    event.preventDefault();
    this.setState({
      showPicker: !this.state.showPicker
    })
  }

  timeChange = type => {
    this.props.onChange(this.props.stateName, type, this.props.validateName)
  }
  handleClose = event => {
    event.preventDefault();
    this.setState({
      showPicker: !this.state.showPicker
    })
  }

  render() {
    const { label, required, className, seconds } = this.props;
    return (
      <div className="time-picker-wrapper">
        <label onClick={this.handleOpen.bind(this)} className={`${className} label-input datetimeclock`}>
          <input pattern='[0-9:]*' onChange={this.inputChange.bind(this)} type='text' placeholder=" " value={this.state.value} readOnly />
          {label && (
            <span>{label}{required && (<em className='req'>*</em>)}</span>
          )}
        </label>
        {this.state.showPicker && (
          <React.Fragment>
            <Picker
              seconds={seconds}
              value={this.state.value}
              timeChange={this.timeChange.bind(this)}
              hourLabel={this.props.t('G_HOUR')}
              MinuteLabel={this.props.t('G_MINUTES')}
              SecondLabel={this.props.t('G_SECONDS')}
            />
            <div onClick={this.handleClose.bind(this)} className="blanket"></div>
          </React.Fragment>
        )}
      </div>
    )
  }
}

export default withTranslation()(Timepicker);