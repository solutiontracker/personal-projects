import React from 'react'

type Props = {
  label?: any,
  display?: any,
  type?: any,
  fields?: any,
  checked?: any
}

const CheckFields = ({ label, display, type, fields, checked }: Props) => {
  const random = Math.ceil(Math.random() * 10000);
  return (
    <div className={display === 'inline' ? 'inline radio-check-field' : 'radio-check-field'}>
      {label && (<h5>{label}</h5>)}
      {fields && fields.map((item: any, k: any) => (
        <label key={k}>
          <input type={type} defaultValue={item} name={type === 'radio' ? type + random : type + random * (k + 1)} defaultChecked={checked.includes(item)} /><span>{item}</span>
        </label>
      ))}
    </div>
  )
}
export default CheckFields;
