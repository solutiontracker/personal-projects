import * as React from 'react';
function percentageToDegrees(percentage) {
  return (percentage / 100) * 360;
}
const CircularProgressBar = ({ value, total, label, border, hidepercentage }) => {
  var left;
  var right;
  if (value > 0) {
    if (value > 50 && value <= 90) {
      if (value <= 50) {
        right = percentageToDegrees(value);
      } else {
        right = 180;
        left = percentageToDegrees(value - 50);
      }
    } else {
      if (value <= 50) {
        left = percentageToDegrees(value);
      } else {
        left = 180;
        right = percentageToDegrees(value - 50);
      }
    }
  }
  return (
    <div className="circular-progress">
      {label && <h2>{label}</h2>}
      <div className="circular-progress-wrapp mx-auto">
        <span className="progress-left">
          <span
            className="progress-bar"
            style={{
              transform: `rotate(${left}deg)`,
              borderColor: border ? border : "#000"
            }}
          ></span>
        </span>
        <span className="progress-right">
          <span
            style={{
              transform: `rotate(${right}deg)`,
              borderColor: border ? border : "#000"
            }}
            className="progress-bar"
          ></span>
        </span>
        <div className="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
          <h3>{total ? total : `${value}`}</h3>
        </div>
        {!hidepercentage && total && (
          <div style={{ borderColor: border ? border : '#000' }} className="progress-percentage  rounded-circle d-flex align-items-center justify-content-center">
            {value}%
        </div>
        )}
      </div>
    </div>
  );
};

export default CircularProgressBar;