import React from 'react';
const FormTextBlock = (props: any) => {
  const { data, setFormData } = props;
  return (
    <div className="ebs-field-wrapper">
      <div className="ebs-half-wrapper">
        <div className="generic-form">
          <h5>
            {data.title !== "" && data.title} {data.required === 1 && <em className="req">*</em>}
          </h5>
          {(data.description !== "") && <p className="form-view-description">{data.description}</p>}
        </div>
      </div>
      <div className="ebs-seperator"></div>
    </div>
  )
}
export default FormTextBlock;