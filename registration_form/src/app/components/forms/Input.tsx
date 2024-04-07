import React, { ReactElement, useEffect } from "react";
import ReactTooltip from 'react-tooltip'

type Props = {
  onChange?: any,
  onBlur?: any,
  label?: any,
  type?: any,
  value?: any,
  required?: boolean,
  className?: any,
  tooltip?: any,
  min?: any,
  pattern?: any,
  name?: any,
  field?: any,
  autoComplete?: any,
  countryCode?: any,
  readOnly?: boolean,
}

const Input = ({ onChange, onBlur, label, type, value, required, className, tooltip, min, pattern, autoComplete, field, countryCode, readOnly }: Props): ReactElement => {

  useEffect(() => {
    ReactTooltip.rebuild()
  }, [])

  return (
    <label className={`${className} label-input`}>
      {tooltip &&
        <em data-event={window.innerWidth <= 768 ? 'click focus' : ''} data-tip={tooltip} className="app-tooltip">
          <i className="material-icons">info</i>
        </em>}
      <input data-countrycode={countryCode} onChange={onChange} onBlur={onBlur} name={field} autoComplete={autoComplete ? 'off' : 'on'} pattern={pattern} type={type} placeholder=" " min={min} value={value} readOnly={readOnly} />
      {label && (
        <span><span>{label}</span>{required && (<em className='req'>*</em>)}</span>
        )}
      <ReactTooltip className="ebs-tooltip-wrapper" globalEventOff="click"  data-scroll-hide resizeHide clickable effect="solid" />
    </label>
  );
};

export default Input;