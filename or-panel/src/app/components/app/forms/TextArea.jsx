import * as React from 'react';

const TextArea = ({ className, label, height, required, value, onChange, isDisabled }) => {
    const textAreaHeight = {
      height: height,
      maxHeight: '330px'
    }
  return (
    <label className={`${className} label-textarea`}>
      <textarea onChange={onChange} value={value} disabled={isDisabled !== undefined && isDisabled ? true : false} style={textAreaHeight} name="textarea" key="editor1" height="450" cols="30" rows="11" placeholder=' '></textarea>
      {label && (
        <span>{label}{required && (<em className='req'>*</em>)}</span>
      )}
    </label>
  );
}

export default TextArea;
