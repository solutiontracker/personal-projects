import React from 'react'

const ErrorMessage = ({className , icon, title, errors, error}:any) => {
  return (
    <div className={`alert alert-danger custom-messages`}>
      <div className='d-flex align-items-center'>
        <span className="ico-close" style={{marginRight:"10px"}}><i className="material-icons">{icon ? icon : 'close'}</i></span>
        {title && <h5 className='m-0'>{title}</h5>}
      </div>
      {errors && errors.map((error:string)=>(<p className='m-0 text-danger'>{error}</p>))}
      {error && <p className='m-0 text-danger'>{error}</p>}
    </div>
  )
}

export default ErrorMessage