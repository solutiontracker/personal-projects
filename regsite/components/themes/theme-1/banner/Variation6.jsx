import SliderBanner from "./components/SliderBanner";
import React from "react";

const Variation6 = ({ banner, event, countdown, regisrationUrl, settings, registerDateEnd }) => {

  const WrapperLayout = (props) => {

    if (props.slides && Number(props.slides.video_type) === 1) {
      return (
        <div>
          {props.slides.url ? <a href={props.slides.url} target="_blank" rel="noreferrer">
              {props.children}
            </a >: props.children}
        </div>
      );
    } else {
      return (
        <div>
          {props.slides.url ? <a href={props.slides.url} target="_blank" rel="noreferrer">
              {props.children}
            </a >: props.children}
        </div>
      );
    }

  }
  return (
    <div className="main-slider-wrapper ebs-classic-banner">
      {banner && (
        <SliderBanner 
        countdown={null}
        registerDateEnd={registerDateEnd}
        eventsiteSettings={event.eventsiteSettings}
         >
          {banner.map((slides, i) => (
            <div key={i} className="slide-wrapper">
              <WrapperLayout
                slides={slides}
              >
                {Number(slides.video_type) === 2 && (
                  <div className="ebs-video-fullscreen">
                    <video autoPlay playsInline muted loop src={`${process.env.NEXT_APP_EVENTCENTER_URL}/${slides.image}`} type="video/mp4"></video>
                  </div>
                )}
                {Number(slides.video_type) === 1 && (
                  <figure className="ebs-classic-figure">
                    <img src={process.env.NEXT_APP_EVENTCENTER_URL + slides.image} />
                  </figure>
                )}
                {((settings.register_button === 1) || (settings.title === 1 && slides.info.title.length > 0) ||  (settings.caption === 1 && slides.info.message.length > 1)) && <div className="classic-caption-wrapp">
                  <div className="text-center classic-inner-caption-wrapp">
                    <div style={{ position: "relative" }}
                      className="parallax-text"
                    >
                      {slides.info.title && settings.title === 1 && (
                        <div style={{color:  slides?.title_color ? slides?.title_color : "#fff"}} className="ebs-banner-title">
                          {slides.info.title}
                        </div>
                      )}
                      {slides.info.message && settings.caption === 1 && (
                        <div style={{color:  slides?.sub_title_color ? slides?.sub_title_color : "#fff"}} className="ebs-banner-subtitle">
                          {slides.info.message}
                        </div>
                      )}
                      {settings.register_button === 1 && registerDateEnd  && <div className="ebs-custom-button-holder">
                        <a href={regisrationUrl} className="edgtf-btn edgtf-btn-huge edgtf-btn-custom-border-hover edgtf-btn-custom-hover-bg edgtf-btn-custom-hover-color">{event.labels.EVENTSITE_REGISTER_NOW2 ? event.labels.EVENTSITE_REGISTER_NOW2 : 'Register Now'}</a>
                      </div>}
                    </div>
                  </div>
                </div>}
              </WrapperLayout>
            </div>
          ))}
        </SliderBanner>
      )}
    </div>
  );
};

export default Variation6;
