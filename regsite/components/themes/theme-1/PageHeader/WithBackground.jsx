import React from 'react'

const WithBackground = ({children, moduleVariation}) => {
    const WrapperLayout = (props) => {
        if (props.moduleVariation.background_image !== '') {
          return (
            <div style={{ backgroundImage: `url(${process.env.NEXT_APP_EVENTCENTER_URL + '/assets/variation_background/' + props.moduleVariation.background_image}`, minHeight: 250,
            marginBottom:'50px', height:"180px", backgroundSize: 'cover' }} 
            className="edgtf-title edgtf-standard-type edgtf-has-background edgtf-content-left-alignment edgtf-title-large-text-size edgtf-animation-no edgtf-title-image-not-responsive edgtf-title-with-border">
              {props.children}
            </div>
          );
        } else {
          return (
            <div style={{ backgroundPosition: "center", backgroundSize: 'cover' }} className="edgtf-title edgtf-standard-type edgtf-has-background edgtf-content-left-alignment edgtf-title-large-text-size edgtf-animation-no edgtf-title-image-not-responsive edgtf-title-with-border">
              {props.children}
            </div>
          );
        }
    
    }
  return (
    <WrapperLayout moduleVariation={moduleVariation}>
      <div className="edgtf-title-holder d-flex align-items-center justify-content-center">
        <div className="container">
          <div className="edgtf-title-subtitle-holder">
            <div className="edgtf-title-subtitle-holder-inner">{children}</div>
          </div>
        </div>
      </div>
    </WrapperLayout>
  );
}

export default WithBackground