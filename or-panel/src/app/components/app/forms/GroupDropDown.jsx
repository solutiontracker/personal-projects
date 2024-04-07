import React, { CSSProperties } from 'react';

const groupStyles = {
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'space-between',
};
const groupBadgeStyles: CSSProperties = {
  backgroundColor: '#EBECF0',
  borderRadius: '2em',
  color: '#172B4D',
  display: 'inline-block',
  fontSize: 12,
  fontWeight: 'normal',
  lineHeight: '1',
  minWidth: 1,
  padding: '0.16666666666667em 0.5em',
  textAlign: 'center',
};

const formatGroupLabel = () => (
  <div style={groupStyles}>
    <span>{data.label}</span>
    <span style={groupBadgeStyles}>{data.options.length}</span>
  </div>
);

export default function GroupDropDown =() => (
  <Select
    defaultValue={colourOptions[1]}
    options={groupedOptions}
    formatGroupLabel={formatGroupLabel}
  />
);

import React from 'react'

const GroupDropDown = ({ label, display, type, fields,checked}) => {
  let random = Math.ceil(Math.random()*10000);
  return (
    <div className={display === 'inline' ? 'inline radio-check-field' : 'radio-check-field'}>
      <Select
            defaultValue={colourOptions[1]}
            options={groupedOptions}
            formatGroupLabel={formatGroupLabel}
        />
     </div>
  )
}
export default CheckFields;