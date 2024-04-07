import React from 'react'

const SuccessMessage = ({className , icon, title, message}:any) => {
  return (
    <div className={`alert alert-success custom-messages`}>
      <div className='d-flex align-items-center'>
        <span className="ico-close" style={{marginRight:"10px"}}><i className="material-icons">{icon ? icon : 'close'}</i></span>
        {title && <h5 className='m-0 text-success'>{title}</h5>}
      </div>
      {message && <p className='m-0 text-success'>{message}</p>}
    </div>
  )
}

export default SuccessMessage