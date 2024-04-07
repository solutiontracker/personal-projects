import React from 'react'

const WithSolidColor = ({ children }) => {
  return (
    <div
      className="edgtf-title edgtf-standard-type edgtf-content-left-alignment edgtf-title-large-text-size edgtf-animation-no edgtf-title-image-not-responsive edgtf-title-with-border"
    >
      <div className="edgtf-title-holder d-flex align-items-center justify-content-center">
        <div className="container">
          <div className="edgtf-title-subtitle-holder">
            <div className="edgtf-title-subtitle-holder-inner">{children}</div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default WithSolidColor