import React, { ReactElement, FC, useState, useEffect } from 'react';
import moment from 'moment';

type Props = {
    value: any;
    seconds: any;
    timeChange: any;
    hourLabel: any;
    MinuteLabel: any;
    SecondLabel: any;
}

const Picker: FC<Props> = (props): ReactElement => {

    const [time, setTime] = useState(props.seconds ? `${props.value ? props.value : '00:00:00'}` : `${props.value ? props.value : '00:00'}`);
    const [hour, setHour] = useState(true);
    const [minutes, setMinutes] = useState(false);
    const [seconds, setSeconds] = useState(false);

    const splitValue = props.value && props.value.split(':');

    const handleEvent = (element: any, value: any) => () => {
        const stateTime = time.split(':');
        if (element === 'hour') {
            setTime(props.seconds ? `${value}:${stateTime[1]}:${stateTime[2]}` : `${value}:${stateTime[1]}`);
            props.timeChange(props.seconds ? `${value}:${stateTime[1]}:${stateTime[2]}` : `${value}:${stateTime[1]}`);
        } else if (element === 'minutes') {
            setTime(props.seconds ? `${stateTime[0]}:${value}:${stateTime[2]}` : `${stateTime[0]}:${value}`);
            props.timeChange(props.seconds ? `${stateTime[0]}:${value}:${stateTime[2]}` : `${stateTime[0]}:${value}`);
        } else {
            setTime(`${stateTime[0]}:${stateTime[1]}:${value}`);
            props.timeChange(`${stateTime[0]}:${stateTime[1]}:${value}`);
        }
    }

    const handleNavigation = (element: any) => (e: any) => {
        e.preventDefault();
        setHour(element === 'hour' ? true : false);
        setMinutes(element === 'minutes' ? true : false);
        setSeconds(element === 'seconds' ? true : false);
    }

    const Hour = () => {
        const n = 24;
        return (
            <div className="hour-wrapper time-content-wrapper">
                {[...Array(n)].map((e, i) => {
                    const digit = i < 10 ? '0' + i : i;
                    return (
                        <span
                            onClick={handleEvent('hour', digit)}
                            className={splitValue !== undefined && splitValue[0] === digit.toString() ? "active time-card card-hour" : "time-card card-hour"}
                            key={i}>{digit}</span>
                    )
                })}
            </div>
        )
    }

    const MinutesSecond = ({ type }: any) => {
        const n = 12;
        const index = type === 'minutes' ? 1 : 2;
        return (
            <div className="minutes-wrapper time-content-wrapper">
                {[...Array(n)].map((e, i) => {
                    const digit = i < 2 ? '0' + i * 5 : i * 5;
                    return (
                        <span
                            onClick={handleEvent(type, digit)}
                            className={splitValue !== undefined && splitValue[index] === digit.toString() ? "active time-card card-hour" : "time-card card-hour"}
                            key={i}>{digit}</span>
                    )
                })}
            </div>
        )
    }

    return (
        <div className='time-wrapper'>
            <div className="navigation-time-wrapper">
                <span onClick={handleNavigation('hour')} className={hour ? 'active' : ''}>{props.hourLabel}</span>
                <span onClick={handleNavigation('minutes')} className={minutes ? 'active' : ''}>{props.MinuteLabel}</span>
                {props.seconds && <span onClick={handleNavigation('seconds')} className={seconds ? 'active' : ''}>{props.SecondLabel}</span>}
            </div>
            <div className="main-time-area">
                {hour && <Hour />}
                {minutes && <MinutesSecond type="minutes" />}
                {seconds && <MinutesSecond type="seconds" />}
            </div>
        </div>
    )
};

type DateTimeProps = {
    value?: any;
    stateName?: any;
    onChange?: any;
    label?: any;
    required?: any;
    className?: any;
    seconds?: any;
}

const TimePicker: FC<DateTimeProps> = (props): ReactElement => {

    const [showPicker, setShowPicker] = useState(false);

    const [value, setValue] = useState(props.value ? props.value : '');

    useEffect(() => {
        if (props.value !== value) {
            if (props.value !== undefined) {
                setValue(props.value);
            }
        }
    }, [props, value]);

    const inputChange = (e: any) => {
        const validate = moment(e.target.value, "HH:mm:ss").format("hh:mm A");
        props.onChange(props.stateName, validate)
    }

    const handleOpen = (event: any) => {
        event.preventDefault();
        setShowPicker(!showPicker);
    }

    const timeChange = (type: any) => {
        props.onChange(props.stateName, type)
    }

    const handleClose = (event: any) => {
        event.preventDefault();
        setShowPicker(!showPicker);
    }

    const { label, required, className, seconds } = props;

    return (
        <div className="time-picker-wrapper">
            <label onClick={handleOpen} className={`${className} label-input datetimeclock`}>
                <input pattern='[0-9:]*' onChange={inputChange} type='text' placeholder=" " value={value} readOnly />
                {label && (
                    <span>{label}{required && (<em className='req'>*</em>)}</span>
                )}
            </label>
            {showPicker && (
                <React.Fragment>
                    <Picker
                        seconds={seconds}
                        value={value}
                        timeChange={timeChange}
                        hourLabel={'Hour'}
                        MinuteLabel={'Minutes'}
                        SecondLabel={'Seconds'}
                    />
                    <div onClick={handleClose} className="blanket"></div>
                </React.Fragment>
            )}
        </div>
    )
};

export default TimePicker;
