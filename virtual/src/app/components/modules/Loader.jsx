import * as React from 'react';

const Loader = ({ className, fixed, heading, message }) => {
  return (
    <div id="loader-wrapper" className={`${className && className} ${fixed && 'popup-fixed'}`}>
      {className ? (
        <div className="wrapper_laoder">
          {heading && (
            <h2>{heading}</h2>
          )}
          {message && (
            <p>{message}</p>
          )}
          <div id="loader"></div>
        </div>
      ) : (
          <div id="loader"></div>
        )}
    </div>
  );
}

export default Loader;

