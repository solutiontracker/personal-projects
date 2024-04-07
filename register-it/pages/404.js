
import Image from 'next/image';
import React from 'react';

const Error404 = () => {
  return (
    <div id="ebs-404-page">
      <div className="container">
        <div className="row d-flex align-items-center">
          <div className="col-md-7">
            <div className="ebs-error-content">
              <h4>404 Error</h4>
              <h1>Page not found…</h1>
              <p>Sorry, we cannot find what you are looking for. 
                For further queries please contact the event organiser or send an e-mail with URL and details to  <a href="mailto:support@eventbuizz.com">support@eventbuizz.com</a>
              </p>
              <a href="#!" className='ebs-btn-back'>Return</a>
            </div>
          </div>
          <div className="col-md-5 d-none d-md-block">
          <Image objectFit='contain'
            onLoad={(e) => e.target.style.opacity = 1}
            src={
              require("public/img/404.jpg")
            }
            alt="g"
          />
          </div>
        </div>
        
      </div> 
    </div>
  )
}

export default Error404;