import * as React from 'react';

const Input = ({ label, type, value, required, onChange, className, tooltip, min, pattern, disabled = false, icon, onClick }) => {
  return (
    <label className={`${className} label-input ${icon ? 'input-icon' : ''}`}>
      {icon &&
        <i onClick={onClick} className="material-icons input-material-icons">{icon}</i>
      }
      {tooltip &&
        <em className="app-tooltip">
          <i className="material-icons">info</i>
          <div className="app-tooltipwrapper">{tooltip}</div>
        </em>}
      <input onChange={onChange} pattern={pattern} type={type} placeholder=" " min={min} value={value} disabled={disabled} />
      {label && (
        <span>{label}{required && (<em className='req'>*</em>)}</span>
      )}
    </label>
  );
}

export default Input;

